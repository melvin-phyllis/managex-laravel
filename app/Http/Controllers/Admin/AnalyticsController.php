<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Leave;
use App\Models\Payroll;
use App\Models\Presence;
use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    /**
     * Display the analytics dashboard.
     */
    public function index()
    {
        $departments = Department::active()->orderBy('name')->get();

        // Initial data for page load (optional, to avoid immediate fetch if desired, but we'll fetch via JS for smoother load)
        $initialData = [
            'kpis' => null, // Will be loaded via API
            'charts' => null,
        ];

        return view('admin.analytics.index', compact('departments', 'initialData'));
    }

    /**
     * Get KPI data.
     */
    public function getKpiData(Request $request): JsonResponse
    {
        $periodDates = $this->getPeriodDates($request->get('period', 'month'));
        $departmentId = $request->get('department_id');
        $contractType = $request->get('contract_type');

        // 1. Effectif total
        $currentQuery = User::where('role', 'employee');
        if ($departmentId) $currentQuery->where('department_id', $departmentId);
        if ($contractType) $currentQuery->where('contract_type', $contractType);
        $currentCount = $currentQuery->count();

        $previousQuery = User::where('role', 'employee')->where('hire_date', '<=', now()->subMonth());
        // Note: Previous count approximation doesn't perfect account for departures in the past, but good enough for trend locally
        $previousCount = $previousQuery->count();

        $variation = $previousCount > 0
            ? round((($currentCount - $previousCount) / $previousCount) * 100, 1)
            : 0;

        // Determine the period first (used for multiple queries)
        $period = $request->get('period', 'month');
        if ($period === 'custom') {
            $currentMonth = (int) $request->get('custom_month', now()->month);
            $currentYear = (int) $request->get('custom_year', now()->year);
        } else {
            $currentMonth = (int) now()->month;
            $currentYear = (int) now()->year;
        }
        
        // Create date range for the selected month
        $monthStart = \Carbon\Carbon::create($currentYear, $currentMonth, 1)->startOfMonth();
        $monthEnd = \Carbon\Carbon::create($currentYear, $currentMonth, 1)->endOfMonth();
        
        // 2. Présences du mois sélectionné
        $presencesMonth = Presence::month($currentMonth, $currentYear)
            ->forDepartment($departmentId)
            ->get();
        
        // Nombre de jours ouvrés dans le mois (approximatif)
        $workingDaysInMonth = 0;
        $tempDate = $monthStart->copy();
        while ($tempDate <= $monthEnd) {
            if (!$tempDate->isWeekend()) $workingDaysInMonth++;
            $tempDate->addDay();
        }
        
        // Présences uniques (nombre de jours avec au moins une présence)
        $presenceCount = $presencesMonth->unique('date')->count();
        $expectedPresences = $currentCount * $workingDaysInMonth;

        // 3. En congé (for the selected month period)
        $onLeave = Leave::where('statut', 'approved')
            ->where(function($q) use ($monthStart, $monthEnd) {
                $q->where('date_debut', '<=', $monthEnd)
                  ->where('date_fin', '>=', $monthStart);
            })
            ->when($departmentId, fn($q) => $q->whereHas('user', fn($u) => $u->where('department_id', $departmentId)))
            ->get();

        // 4. Jours d'absence non justifiés (approximation)
        // Total jours attendus - présences - jours de congé
        $totalLeaveDays = $onLeave->sum(function($leave) use ($monthStart, $monthEnd) {
            $start = max($leave->date_debut, $monthStart);
            $end = min($leave->date_fin, $monthEnd);
            return $start->diffInDaysFiltered(fn($d) => !$d->isWeekend(), $end) + 1;
        });
        $absent = max(0, $expectedPresences - $presenceCount - $totalLeaveDays);

        // 5. Masse salariale
        $payrollTotal = Payroll::where('mois', $currentMonth)
            ->where('annee', $currentYear)
            ->when($departmentId, fn($q) => $q->whereHas('user', fn($u) => $u->where('department_id', $departmentId)))
            ->sum('net_salary');

        // 6. Heures supplémentaires (basé sur worked_hours > 8h)
        $overtimeData = $presencesMonth->sum(function ($p) {
            $workedHours = $p->worked_hours ?? 0;
            return max(0, $workedHours - 8);
        });

        return response()->json([
            'effectif_total' => [
                'value' => $currentCount,
                'variation' => $variation,
                'previous' => $previousCount
            ],
            'presents_today' => [
                'value' => $presenceCount,
                'expected' => $expectedPresences,
                'percentage' => $expectedPresences > 0 ? round(($presenceCount / $expectedPresences) * 100) : 0
            ],
            'en_conge' => [
                'value' => $onLeave->count(),
                'types' => [
                    'conge' => $onLeave->where('type', 'conge')->count(),
                    'maladie' => $onLeave->where('type', 'maladie')->count(),
                    'autre' => $onLeave->where('type', 'autre')->count(),
                ]
            ],
            'absents_non_justifies' => [
                'value' => $absent,
            ],
            'masse_salariale' => [
                'value' => $payrollTotal,
                'formatted' => number_format($payrollTotal, 0, ',', ' ') . ' FCFA'
            ],
            'heures_supplementaires' => [
                'value' => round($overtimeData, 1),
                'count' => $presencesMonth->filter(fn($p) => ($p->worked_hours ?? 0) > 8)->unique('user_id')->count()
            ]
        ]);
    }

    /**
     * Get Charts data.
     */
    public function getChartsData(Request $request): JsonResponse
    {
        $startDate = $this->getPeriodDates($request->get('period', 'month'))['start'];
        $endDate = $this->getPeriodDates($request->get('period', 'month'))['end'];
        $departmentId = $request->get('department_id');

        // 1. Evolution presences (Lines)
        $presenceTrendQuery = Presence::selectRaw('DATE(date) as day, COUNT(*) as count')
            ->whereBetween('date', [$startDate, $endDate]);
            
        if ($departmentId) {
             $presenceTrendQuery->forDepartment($departmentId);
        }
        
        $presenceTrend = $presenceTrendQuery->groupBy('day')
            ->orderBy('day')
            ->get();

        // 2. Repartition par departement
        $deptDistribution = Department::withCount(['users' => fn($q) => $q->where('role', 'employee')])
            ->get()
            ->map(fn($d) => ['label' => $d->name, 'value' => $d->users_count]);

        // 3. Recrutements vs Departs (12 mois) - Fixed range
        $months = collect();
        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $recruits = User::whereMonth('hire_date', $date->month)
                ->whereYear('hire_date', $date->year)
                ->when($departmentId, fn($q) => $q->where('department_id', $departmentId))
                ->count();
            $departures = User::whereMonth('contract_end_date', $date->month)
                ->whereYear('contract_end_date', $date->year)
                ->whereNotNull('contract_end_date') // Assuming DepartedInPeriod logic
                ->when($departmentId, fn($q) => $q->where('department_id', $departmentId))
                ->count();
                
            $months->push([
                'label' => $date->translatedFormat('M Y'),
                'recruits' => $recruits,
                'departures' => $departures
            ]);
        }

        // 4. Taux absenteisme par service
        $absenteism = Department::with(['users' => fn($q) => $q->where('role', 'employee')])->get()->map(function($dept) use ($startDate, $endDate) {
            $employees = $dept->users->count();
            if ($employees === 0) return ['label' => $dept->name, 'rate' => 0];
            
            // Calculate work days in period
            $daysInPeriod = $startDate->diffInDays($endDate) + 1; // Approx check
            // Better: use business days
            $workDays = 22; // Using standard monthly avg for simplicity or verify period
            if ($daysInPeriod < 20) $workDays = 5; // Week
            
            $expectedPresences = $employees * $workDays;
            
            $actualPresences = Presence::whereHas('user', fn($q) => $q->where('department_id', $dept->id))
                ->whereBetween('date', [$startDate, $endDate])
                ->count();
                
            $rate = $expectedPresences > 0
                ? round((1 - $actualPresences / $expectedPresences) * 100, 1)
                : 0;
            return ['label' => $dept->name, 'rate' => max(0, $rate)];
        });

        // 5. Heures travaillees par semaine
        $weeklyHours = collect();
        for ($w = 4; $w >= 0; $w--) {
            $weekStart = now()->subWeeks($w)->startOfWeek();
            $weekEnd = now()->subWeeks($w)->endOfWeek();
            
            $hoursQuery = Presence::whereBetween('date', [$weekStart, $weekEnd]);
            if ($departmentId) $hoursQuery->forDepartment($departmentId);
            $hours = $hoursQuery->get()->sum('hours_worked') ?? 0;
            
            $weeklyHours->push([
                'label' => 'Sem ' . $weekStart->weekOfYear,
                'hours' => round($hours, 1)
            ]);
        }

        return response()->json([
            'presence_trend' => [
                'labels' => $presenceTrend->pluck('day')->map(fn($d) => Carbon::parse($d)->format('d/m')),
                'data' => $presenceTrend->pluck('count')
            ],
            'department_distribution' => [
                'labels' => $deptDistribution->pluck('label'),
                'data' => $deptDistribution->pluck('value'),
                'colors' => ['#3B82F6', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6', '#EC4899']
            ],
            'recruitment_turnover' => [
                'labels' => $months->pluck('label'),
                'recrutements' => $months->pluck('recruits'),
                'departs' => $months->pluck('departures')
            ],
            'absenteisme_par_service' => [
                'labels' => $absenteism->pluck('label'),
                'rates' => $absenteism->pluck('rate')
            ],
            'heures_travaillees_semaine' => [
                'labels' => $weeklyHours->pluck('label'),
                'data' => $weeklyHours->pluck('hours')
            ]
        ]);
    }

    public function getRecentActivities(Request $request): JsonResponse
    {
        $activities = collect();
        
        // Recent presences
        $presences = Presence::with('user')->orderBy('created_at', 'desc')->take(5)->get()->map(function($p) {
            return [
                'type' => 'presence',
                'user' => $p->user->name,
                'avatar' => $p->user->avatar,
                'description' => 'a pointé son arrivée à ' . $p->check_in_formatted,
                'time' => $p->created_at->diffForHumans()
            ];
        });
        
        // Recent leaves
        $leaves = Leave::with('user')->orderBy('created_at', 'desc')->take(5)->get()->map(function($l) {
            return [
                'type' => 'leave',
                'user' => $l->user->name,
                'avatar' => $l->user->avatar,
                'description' => 'a demandé un congé (' . $l->type_label . ')',
                'time' => $l->created_at->diffForHumans()
            ];
        });

        // Recent tasks
        $tasks = Task::with('user')->orderBy('updated_at', 'desc')->take(5)->get()->filter(fn($t) => $t->statut == 'completed')->map(function($t) {
            $user = $t->user; 
            return [
                'type' => 'task',
                'user' => $user ? $user->name : 'Système',
                'avatar' => $user ? $user->avatar : null,
                'description' => 'a terminé la tâche "' . $t->titre . '"',
                'time' => $t->updated_at->diffForHumans()
            ];
        });

        return response()->json(
            $presences->merge($leaves)->merge($tasks)->sortByDesc('time')->take(10)->values()
        );
    }

    public function getPendingRequests(Request $request): JsonResponse
    {
        $pendingLeaves = Leave::with('user')->pending()->take(5)->get()->map(function($l) {
            return [
                'id' => $l->id,
                'type' => 'Congé',
                'user' => $l->user->name,
                'details' => $l->type_label . ' (' . $l->duree . ' jours)',
                'date' => $l->created_at->format('d/m/Y')
            ];
        });

        return response()->json($pendingLeaves);
    }

    public function getTopLatecomers(Request $request): JsonResponse
    {
        $month = now()->month;
        return response()->json(
            Presence::where('is_late', true)
                ->whereMonth('date', $month)
                ->select('user_id', DB::raw('COUNT(*) as late_count'), DB::raw('SUM(late_minutes) as total_minutes'))
                ->groupBy('user_id')
                ->orderByDesc('late_count')
                ->limit(5)
                ->with('user:id,name,department_id', 'user.department:id,name')
                ->get()
                ->map(fn($p, $i) => [
                    'rank' => $i + 1,
                    'user_id' => $p->user_id,
                    'name' => $p->user->name,
                    'department' => $p->user->department->name ?? '-',
                    'count' => $p->late_count,
                    'avg_minutes' => $p->late_count > 0 ? round($p->total_minutes / $p->late_count) : 0
                ])
        );
    }

    public function getHrAlerts(Request $request): JsonResponse
    {
        return response()->json([
            'contracts' => User::expiringContracts()->with('department')->limit(5)->get()->map(fn($u) => [
                'name' => $u->name,
                'department' => $u->department->name ?? '-',
                'end_date' => $u->contract_end_date->format('d/m/Y'),
                'days' => now()->diffInDays($u->contract_end_date),
            ]),
            'birthdays' => User::upcomingBirthdays()->limit(5)->get()->map(fn($u) => [
                'name' => $u->name,
                'date' => $u->date_of_birth->format('d/m'),
                'age' => $u->date_of_birth->age + 1
            ])
        ]);
    }

    private function getPeriodDates(string $period): array
    {
        $end = now();
        $start = match($period) {
            'today' => now(),
            'week' => now()->startOfWeek(),
            'month' => now()->startOfMonth(),
            'year' => now()->startOfYear(),
            default => now()->subMonth()
        };
        return ['start' => $start, 'end' => $end];
    }
}
