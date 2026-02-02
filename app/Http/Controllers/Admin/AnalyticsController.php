<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\EmployeeEvaluation;
use App\Models\InternEvaluation;
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
        $departments = Department::getActiveCached();

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

        // 7. Turnover (NOUVEAU)
        $startOfYear = Carbon::create($currentYear, 1, 1);
        $entries = User::where('role', 'employee')
            ->whereYear('hire_date', $currentYear)
            ->when($departmentId, fn($q) => $q->where('department_id', $departmentId))
            ->count();
        $exits = User::where('role', 'employee')
            ->whereYear('contract_end_date', $currentYear)
            ->whereNotNull('contract_end_date')
            ->when($departmentId, fn($q) => $q->where('department_id', $departmentId))
            ->count();
        $avgEmployees = max(1, $currentCount);
        $turnoverRate = round((($entries + $exits) / 2) / $avgEmployees * 100, 1);

        // 8. Masse salariale mois précédent (pour variation)
        $previousMonth = $currentMonth > 1 ? $currentMonth - 1 : 12;
        $previousYear = $currentMonth > 1 ? $currentYear : $currentYear - 1;
        $previousPayrollTotal = Payroll::where('mois', $previousMonth)
            ->where('annee', $previousYear)
            ->when($departmentId, fn($q) => $q->whereHas('user', fn($u) => $u->where('department_id', $departmentId)))
            ->sum('net_salary');
        $payrollVariation = $previousPayrollTotal > 0 
            ? round((($payrollTotal - $previousPayrollTotal) / $previousPayrollTotal) * 100, 1) 
            : 0;

        // 9. Tâches (NOUVEAU)
        $tasksCompleted = Task::where('statut', 'completed')
            ->whereMonth('updated_at', $currentMonth)
            ->whereYear('updated_at', $currentYear)
            ->when($departmentId, fn($q) => $q->whereHas('user', fn($u) => $u->where('department_id', $departmentId)))
            ->count();
        $tasksPending = Task::whereIn('statut', ['pending', 'approved', 'in_progress'])
            ->when($departmentId, fn($q) => $q->whereHas('user', fn($u) => $u->where('department_id', $departmentId)))
            ->count();

        // 10. Stagiaires (NOUVEAU)
        $internsCount = User::where('role', 'employee')
            ->where('contract_type', 'stage')
            ->when($departmentId, fn($q) => $q->where('department_id', $departmentId))
            ->count();
        $internsToEvaluate = User::where('role', 'employee')
            ->where('contract_type', 'stage')
            ->whereDoesntHave('internEvaluations', function($q) {
                $q->where('week_start', '>=', now()->startOfWeek()->subWeek());
            })
            ->when($departmentId, fn($q) => $q->where('department_id', $departmentId))
            ->count();

        // 11. Heures de retard à rattraper (NOUVEAU)
        $lateHoursData = Presence::where('is_late', true)
            ->whereMonth('date', $currentMonth)
            ->whereYear('date', $currentYear)
            ->when($departmentId, fn($q) => $q->forDepartment($departmentId))
            ->selectRaw('SUM(late_minutes) as total, COUNT(DISTINCT user_id) as employees')
            ->first();
        $totalLateMinutes = $lateHoursData->total ?? 0;
        $lateEmployees = $lateHoursData->employees ?? 0;

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
                'formatted' => number_format($payrollTotal, 0, ',', ' ') . ' FCFA',
                'variation' => $payrollVariation
            ],
            'heures_supplementaires' => [
                'value' => round($overtimeData, 1),
                'count' => $presencesMonth->filter(fn($p) => ($p->worked_hours ?? 0) > 8)->unique('user_id')->count()
            ],
            'turnover' => [
                'rate' => $turnoverRate,
                'entries' => $entries,
                'exits' => $exits
            ],
            'tasks' => [
                'completed' => $tasksCompleted,
                'pending' => $tasksPending,
                'total' => $tasksCompleted + $tasksPending
            ],
            'interns' => [
                'count' => $internsCount,
                'to_evaluate' => $internsToEvaluate
            ],
            'late_hours' => [
                'total' => round($totalLateMinutes / 60, 1), // En heures
                'employees' => $lateEmployees
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
        $totalWeeklyHours = 0;
        for ($w = 4; $w >= 0; $w--) {
            $weekStart = now()->subWeeks($w)->startOfWeek();
            $weekEnd = now()->subWeeks($w)->endOfWeek();
            
            $hoursQuery = Presence::whereBetween('date', [$weekStart, $weekEnd]);
            if ($departmentId) $hoursQuery->forDepartment($departmentId);
            $hours = $hoursQuery->get()->sum('hours_worked') ?? 0;
            $totalWeeklyHours += $hours;
            
            $weeklyHours->push([
                'label' => 'Sem ' . $weekStart->weekOfYear,
                'hours' => round($hours, 1)
            ]);
        }

        // 6. Répartition par type de contrat (NOUVEAU)
        $contractTypes = User::where('role', 'employee')
            ->when($departmentId, fn($q) => $q->where('department_id', $departmentId))
            ->selectRaw('contract_type, COUNT(*) as count')
            ->groupBy('contract_type')
            ->get()
            ->map(fn($c) => [
                'label' => match($c->contract_type) {
                    'cdi' => 'CDI',
                    'cdd' => 'CDD',
                    'stage' => 'Stage',
                    'alternance' => 'Alternance',
                    'freelance' => 'Freelance',
                    'interim' => 'Intérim',
                    default => $c->contract_type ?? 'Autre'
                },
                'value' => $c->count
            ]);

        // 7. Performance des tâches (NOUVEAU)
        $taskStats = Task::when($departmentId, fn($q) => $q->whereHas('user', fn($u) => $u->where('department_id', $departmentId)))
            ->selectRaw("statut, COUNT(*) as count")
            ->groupBy('statut')
            ->get()
            ->mapWithKeys(fn($t) => [$t->statut => $t->count]);

        // 8. Ponctualité par département (NOUVEAU)
        $punctuality = Department::with(['users' => fn($q) => $q->where('role', 'employee')])->get()->map(function($dept) use ($startDate, $endDate) {
            $userIds = $dept->users->pluck('id');
            if ($userIds->isEmpty()) return ['label' => $dept->name, 'on_time' => 0, 'late' => 0];
            
            $onTime = Presence::whereIn('user_id', $userIds)
                ->whereBetween('date', [$startDate, $endDate])
                ->where('is_late', false)
                ->count();
            $late = Presence::whereIn('user_id', $userIds)
                ->whereBetween('date', [$startDate, $endDate])
                ->where('is_late', true)
                ->count();
                
            return ['label' => $dept->name, 'on_time' => $onTime, 'late' => $late];
        })->filter(fn($d) => $d['on_time'] > 0 || $d['late'] > 0);

        return response()->json([
            'presence_trend' => [
                'labels' => $presenceTrend->pluck('day')->map(fn($d) => Carbon::parse($d)->format('d/m')),
                'data' => $presenceTrend->pluck('count')
            ],
            'department_distribution' => [
                'labels' => $deptDistribution->pluck('label'),
                'data' => $deptDistribution->pluck('value'),
                'colors' => ['#3B82F6', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6', '#EC4899', '#14B8A6', '#F97316']
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
                'data' => $weeklyHours->pluck('hours'),
                'total' => round($totalWeeklyHours, 1)
            ],
            'contract_types' => [
                'labels' => $contractTypes->pluck('label'),
                'data' => $contractTypes->pluck('value'),
                'colors' => ['#6366F1', '#22C55E', '#F59E0B', '#EC4899', '#8B5CF6', '#06B6D4']
            ],
            'task_performance' => [
                'completed' => $taskStats['completed'] ?? 0,
                'in_progress' => $taskStats['in_progress'] ?? 0,
                'approved' => $taskStats['approved'] ?? 0,
                'pending' => $taskStats['pending'] ?? 0,
                'cancelled' => $taskStats['cancelled'] ?? 0,
            ],
            'punctuality' => [
                'labels' => $punctuality->pluck('label'),
                'on_time' => $punctuality->pluck('on_time'),
                'late' => $punctuality->pluck('late')
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
                'avatar' => $p->user->avatar ? avatar_url($p->user->avatar) : null,
                'description' => 'a pointé son arrivée à ' . $p->check_in_formatted,
                'time' => $p->created_at->diffForHumans()
            ];
        });
        
        // Recent leaves
        $leaves = Leave::with('user')->orderBy('created_at', 'desc')->take(5)->get()->map(function($l) {
            return [
                'type' => 'leave',
                'user' => $l->user->name,
                'avatar' => $l->user->avatar ? avatar_url($l->user->avatar) : null,
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
                'avatar' => $user && $user->avatar ? avatar_url($user->avatar) : null,
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

    /**
     * Get top performers based on evaluations
     */
    public function getTopPerformers(Request $request): JsonResponse
    {
        $month = (int) $request->get('custom_month', now()->month);
        $year = (int) $request->get('custom_year', now()->year);
        $departmentId = $request->get('department_id');

        // Top employés par évaluation
        $topEmployees = EmployeeEvaluation::with(['user:id,name,avatar,department_id', 'user.department:id,name'])
            ->forPeriod($month, $year)
            ->validated()
            ->when($departmentId, fn($q) => $q->whereHas('user', fn($u) => $u->where('department_id', $departmentId)))
            ->orderByDesc('total_score')
            ->limit(5)
            ->get()
            ->map(fn($eval, $index) => [
                'rank' => $index + 1,
                'name' => $eval->user->name ?? '-',
                'avatar' => $eval->user->avatar ? avatar_url($eval->user->avatar) : null,
                'department' => $eval->user->department->name ?? '-',
                'score' => $eval->total_score,
                'max_score' => EmployeeEvaluation::MAX_SCORE,
                'percentage' => $eval->score_percentage,
            ]);

        // Top stagiaires par évaluation (dernières 4 semaines)
        $weekStart = now()->startOfWeek();
        $topInternsData = InternEvaluation::submitted()
            ->where('week_start', '>=', $weekStart->copy()->subWeeks(4))
            ->when($departmentId, fn($q) => $q->whereHas('intern', fn($u) => $u->where('department_id', $departmentId)))
            ->selectRaw('intern_id, AVG(discipline_score + behavior_score + skills_score + communication_score) as avg_score')
            ->groupBy('intern_id')
            ->orderByDesc('avg_score')
            ->limit(5)
            ->get();

        // Load interns separately
        $internIds = $topInternsData->pluck('intern_id');
        $interns = User::with('department:id,name')
            ->whereIn('id', $internIds)
            ->get()
            ->keyBy('id');

        $topInterns = $topInternsData->map(function ($eval, $index) use ($interns) {
            $intern = $interns->get($eval->intern_id);
            return [
                'rank' => $index + 1,
                'name' => $intern->name ?? '-',
                'avatar' => $intern && $intern->avatar ? avatar_url($intern->avatar) : null,
                'department' => $intern->department->name ?? '-',
                'score' => round($eval->avg_score ?? 0, 1),
                'max_score' => 10,
                'percentage' => $eval->avg_score ? round(($eval->avg_score / 10) * 100, 1) : 0,
            ];
        });

        return response()->json([
            'employees' => $topEmployees,
            'interns' => $topInterns,
        ]);
    }

    /**
     * Get best attendance rankings
     */
    public function getBestAttendance(Request $request): JsonResponse
    {
        $month = (int) $request->get('custom_month', now()->month);
        $year = (int) $request->get('custom_year', now()->year);
        $departmentId = $request->get('department_id');

        // Meilleure assiduité (plus de présences, moins de retards)
        $attendanceData = Presence::whereMonth('date', $month)
            ->whereYear('date', $year)
            ->when($departmentId, fn($q) => $q->forDepartment($departmentId))
            ->select('user_id')
            ->selectRaw('COUNT(*) as presence_count')
            ->selectRaw('SUM(CASE WHEN is_late = 0 OR is_late IS NULL THEN 1 ELSE 0 END) as on_time_count')
            ->selectRaw('SUM(CASE WHEN is_late = 1 THEN 1 ELSE 0 END) as late_count')
            ->groupBy('user_id')
            ->orderByDesc('on_time_count')
            ->orderBy('late_count')
            ->limit(5)
            ->get();

        // Load users separately
        $userIds = $attendanceData->pluck('user_id');
        $users = User::with('department:id,name')
            ->whereIn('id', $userIds)
            ->get()
            ->keyBy('id');

        // Calculate total hours for each user
        $totalHours = [];
        if ($userIds->isNotEmpty()) {
            $presences = Presence::whereMonth('date', $month)
                ->whereYear('date', $year)
                ->whereIn('user_id', $userIds)
                ->whereNotNull('check_out')
                ->get();
            
            foreach ($presences as $presence) {
                $userId = $presence->user_id;
                $hours = $presence->hours_worked ?? 0;
                $totalHours[$userId] = ($totalHours[$userId] ?? 0) + $hours;
            }
        }

        $bestAttendance = $attendanceData->map(function ($p, $index) use ($users, $totalHours) {
            $user = $users->get($p->user_id);
            return [
                'rank' => $index + 1,
                'name' => $user->name ?? '-',
                'avatar' => $user && $user->avatar ? avatar_url($user->avatar) : null,
                'department' => $user->department->name ?? '-',
                'presence_count' => (int) $p->presence_count,
                'on_time_count' => (int) $p->on_time_count,
                'late_count' => (int) $p->late_count,
                'punctuality_rate' => $p->presence_count > 0 
                    ? round(($p->on_time_count / $p->presence_count) * 100, 1) 
                    : 0,
                'total_hours' => round($totalHours[$p->user_id] ?? 0, 1),
            ];
        });

        return response()->json($bestAttendance);
    }

    /**
     * Get evaluation statistics
     */
    public function getEvaluationStats(Request $request): JsonResponse
    {
        $month = (int) $request->get('custom_month', now()->month);
        $year = (int) $request->get('custom_year', now()->year);
        $departmentId = $request->get('department_id');

        // Statistiques évaluations employés
        $employeeEvalStats = EmployeeEvaluation::forPeriod($month, $year)
            ->when($departmentId, fn($q) => $q->whereHas('user', fn($u) => $u->where('department_id', $departmentId)))
            ->selectRaw('COUNT(*) as total')
            ->selectRaw("SUM(CASE WHEN status = 'validated' THEN 1 ELSE 0 END) as validated")
            ->selectRaw("SUM(CASE WHEN status = 'draft' THEN 1 ELSE 0 END) as draft")
            ->selectRaw('AVG(total_score) as avg_score')
            ->selectRaw('MAX(total_score) as max_score')
            ->selectRaw('MIN(total_score) as min_score')
            ->first();

        // Employés non évalués ce mois
        $evaluatedUserIds = EmployeeEvaluation::forPeriod($month, $year)->pluck('user_id');
        $notEvaluatedCount = User::where('role', 'employee')
            ->where('contract_type', '!=', 'stage')
            ->when($departmentId, fn($q) => $q->where('department_id', $departmentId))
            ->whereNotIn('id', $evaluatedUserIds)
            ->count();

        // Distribution des notes (pour graphique)
        $scoreDistribution = EmployeeEvaluation::forPeriod($month, $year)
            ->validated()
            ->when($departmentId, fn($q) => $q->whereHas('user', fn($u) => $u->where('department_id', $departmentId)))
            ->selectRaw("
                CASE 
                    WHEN total_score >= 5 THEN 'Excellent (5+)'
                    WHEN total_score >= 4 THEN 'Bien (4-5)'
                    WHEN total_score >= 3 THEN 'Satisfaisant (3-4)'
                    WHEN total_score >= 2 THEN 'A ameliorer (2-3)'
                    ELSE 'Insuffisant (<2)'
                END as grade_range,
                COUNT(*) as count
            ")
            ->groupBy('grade_range')
            ->get();

        // Statistiques stagiaires (dernières 4 semaines)
        $internEvalStats = InternEvaluation::submitted()
            ->where('week_start', '>=', now()->subWeeks(4)->startOfWeek())
            ->when($departmentId, fn($q) => $q->whereHas('intern', fn($u) => $u->where('department_id', $departmentId)))
            ->selectRaw('COUNT(*) as total')
            ->selectRaw('AVG(discipline_score + behavior_score + skills_score + communication_score) as avg_score')
            ->first();

        // Stagiaires non évalués cette semaine
        $currentWeekStart = now()->startOfWeek();
        $evaluatedInternIds = InternEvaluation::where('week_start', $currentWeekStart)->pluck('intern_id');
        $internsNotEvaluated = User::where('role', 'employee')
            ->where('contract_type', 'stage')
            ->when($departmentId, fn($q) => $q->where('department_id', $departmentId))
            ->whereNotIn('id', $evaluatedInternIds)
            ->count();

        return response()->json([
            'employees' => [
                'total' => $employeeEvalStats->total ?? 0,
                'validated' => $employeeEvalStats->validated ?? 0,
                'draft' => $employeeEvalStats->draft ?? 0,
                'not_evaluated' => $notEvaluatedCount,
                'avg_score' => round($employeeEvalStats->avg_score ?? 0, 2),
                'max_score' => round($employeeEvalStats->max_score ?? 0, 2),
                'min_score' => round($employeeEvalStats->min_score ?? 0, 2),
                'score_distribution' => $scoreDistribution,
            ],
            'interns' => [
                'total_evaluations' => $internEvalStats->total ?? 0,
                'avg_score' => round($internEvalStats->avg_score ?? 0, 2),
                'not_evaluated_this_week' => $internsNotEvaluated,
            ],
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

    /**
     * Export analytics data to PDF
     */
    public function exportPdf(Request $request)
    {
        $data = $this->getExportData($request);
        
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.analytics-report', $data);
        $pdf->setPaper('a4', 'portrait');
        
        return $pdf->download('rapport-analytics-' . now()->format('Y-m-d') . '.pdf');
    }

    /**
     * Export analytics data to Excel
     */
    public function exportExcel(Request $request)
    {
        $data = $this->getExportData($request);
        
        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\AnalyticsExport($data),
            'rapport-analytics-' . now()->format('Y-m-d') . '.xlsx'
        );
    }

    /**
     * Get data for export (PDF/Excel)
     */
    private function getExportData(Request $request): array
    {
        $period = $request->get('period', 'month');
        $departmentId = $request->get('department_id');
        
        // Période
        $periodLabel = match($period) {
            'today' => "Aujourd'hui",
            'week' => 'Cette semaine',
            'month' => 'Ce mois',
            'year' => 'Cette année',
            default => 'Ce mois'
        };

        // Récupérer les KPIs via la méthode existante
        $kpisResponse = $this->getKpiData($request);
        $kpis = json_decode($kpisResponse->getContent(), true);

        // Département sélectionné
        $department = $departmentId ? Department::find($departmentId) : null;

        // Effectif par département
        $departmentStats = Department::withCount(['users' => fn($q) => $q->where('role', 'employee')])
            ->get()
            ->map(fn($d) => [
                'name' => $d->name,
                'count' => $d->users_count
            ]);

        // Top retardataires
        $latecomersResponse = $this->getTopLatecomers($request);
        $latecomers = json_decode($latecomersResponse->getContent(), true);

        // Congés en attente
        $pendingLeaves = Leave::with('user')->pending()->get();

        // Top performers
        $topPerformersResponse = $this->getTopPerformers($request);
        $topPerformers = json_decode($topPerformersResponse->getContent(), true);

        // Best attendance
        $bestAttendanceResponse = $this->getBestAttendance($request);
        $bestAttendance = json_decode($bestAttendanceResponse->getContent(), true);

        // Evaluation stats
        $evalStatsResponse = $this->getEvaluationStats($request);
        $evaluationStats = json_decode($evalStatsResponse->getContent(), true);

        return [
            'title' => 'Rapport Analytics RH',
            'period_label' => $periodLabel,
            'department' => $department,
            'generated_at' => now()->format('d/m/Y H:i'),
            'kpis' => $kpis,
            'department_stats' => $departmentStats,
            'latecomers' => $latecomers,
            'pending_leaves' => $pendingLeaves,
            'top_performers' => $topPerformers,
            'best_attendance' => $bestAttendance,
            'evaluation_stats' => $evaluationStats,
        ];
    }
}
