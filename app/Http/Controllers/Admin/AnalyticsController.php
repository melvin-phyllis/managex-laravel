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
use App\Services\AI\AnalyticsInsightService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
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
        if ($departmentId) {
            $currentQuery->where('department_id', $departmentId);
        }
        if ($contractType) {
            $currentQuery->where('contract_type', $contractType);
        }
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
            if (! $tempDate->isWeekend()) {
                $workingDaysInMonth++;
            }
            $tempDate->addDay();
        }

        // Présences uniques (nombre de jours avec au moins une présence)
        $presenceCount = $presencesMonth->unique('date')->count();
        $expectedPresences = $currentCount * $workingDaysInMonth;

        // 3. En congé (for the selected month period)
        $onLeave = Leave::where('statut', 'approved')
            ->where(function ($q) use ($monthStart, $monthEnd) {
                $q->where('date_debut', '<=', $monthEnd)
                    ->where('date_fin', '>=', $monthStart);
            })
            ->when($departmentId, fn ($q) => $q->whereHas('user', fn ($u) => $u->where('department_id', $departmentId)))
            ->get();

        // 4. Jours d'absence non justifiés (approximation)
        // Total jours attendus - présences - jours de congé
        $totalLeaveDays = $onLeave->sum(function ($leave) use ($monthStart, $monthEnd) {
            $start = max($leave->date_debut, $monthStart);
            $end = min($leave->date_fin, $monthEnd);

            return $start->diffInDaysFiltered(fn ($d) => ! $d->isWeekend(), $end) + 1;
        });
        $absent = max(0, $expectedPresences - $presenceCount - $totalLeaveDays);

        // 5. Masse salariale
        $payrollTotal = Payroll::where('mois', $currentMonth)
            ->where('annee', $currentYear)
            ->when($departmentId, fn ($q) => $q->whereHas('user', fn ($u) => $u->where('department_id', $departmentId)))
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
            ->when($departmentId, fn ($q) => $q->where('department_id', $departmentId))
            ->count();
        $exits = User::where('role', 'employee')
            ->whereYear('contract_end_date', $currentYear)
            ->whereNotNull('contract_end_date')
            ->when($departmentId, fn ($q) => $q->where('department_id', $departmentId))
            ->count();
        $avgEmployees = max(1, $currentCount);
        $turnoverRate = round((($entries + $exits) / 2) / $avgEmployees * 100, 1);

        // 8. Masse salariale mois précédent (pour variation)
        $previousMonth = $currentMonth > 1 ? $currentMonth - 1 : 12;
        $previousYear = $currentMonth > 1 ? $currentYear : $currentYear - 1;
        $previousPayrollTotal = Payroll::where('mois', $previousMonth)
            ->where('annee', $previousYear)
            ->when($departmentId, fn ($q) => $q->whereHas('user', fn ($u) => $u->where('department_id', $departmentId)))
            ->sum('net_salary');
        $payrollVariation = $previousPayrollTotal > 0
            ? round((($payrollTotal - $previousPayrollTotal) / $previousPayrollTotal) * 100, 1)
            : 0;

        // 9. Tâches (NOUVEAU)
        $tasksCompleted = Task::where('statut', 'completed')
            ->whereMonth('updated_at', $currentMonth)
            ->whereYear('updated_at', $currentYear)
            ->when($departmentId, fn ($q) => $q->whereHas('user', fn ($u) => $u->where('department_id', $departmentId)))
            ->count();
        $tasksPending = Task::whereIn('statut', ['pending', 'approved', 'in_progress'])
            ->when($departmentId, fn ($q) => $q->whereHas('user', fn ($u) => $u->where('department_id', $departmentId)))
            ->count();

        // 10. Stagiaires (NOUVEAU)
        $internsCount = User::where('role', 'employee')
            ->where('contract_type', 'stage')
            ->when($departmentId, fn ($q) => $q->where('department_id', $departmentId))
            ->count();
        $internsToEvaluate = User::where('role', 'employee')
            ->where('contract_type', 'stage')
            ->whereDoesntHave('internEvaluations', function ($q) {
                $q->where('week_start', '>=', now()->startOfWeek()->subWeek());
            })
            ->when($departmentId, fn ($q) => $q->where('department_id', $departmentId))
            ->count();

        // 11. Heures de retard à rattraper (NOUVEAU)
        $lateHoursData = Presence::where('is_late', true)
            ->whereMonth('date', $currentMonth)
            ->whereYear('date', $currentYear)
            ->when($departmentId, fn ($q) => $q->forDepartment($departmentId))
            ->selectRaw('SUM(late_minutes) as total, COUNT(DISTINCT user_id) as employees')
            ->first();
        $totalLateMinutes = $lateHoursData->total ?? 0;
        $lateEmployees = $lateHoursData->employees ?? 0;

        return response()->json([
            'effectif_total' => [
                'value' => $currentCount,
                'variation' => $variation,
                'previous' => $previousCount,
            ],
            'punctuality_today' => [
                'value' => $presenceCount,
                'on_time' => $presencesMonth->where('is_late', false)->unique('date')->count(),
                'percentage' => $presenceCount > 0 
                    ? round(($presencesMonth->where('is_late', false)->count() / $presencesMonth->count()) * 100) 
                    : 0,
            ],
            'en_conge' => [
                'value' => $onLeave->count(),
                'types' => [
                    'conge' => $onLeave->where('type', 'conge')->count(),
                    'maladie' => $onLeave->where('type', 'maladie')->count(),
                    'autre' => $onLeave->where('type', 'autre')->count(),
                ],
            ],
            'absents_non_justifies' => [
                'value' => $absent,
            ],
            'masse_salariale' => [
                'value' => $payrollTotal,
                'formatted' => number_format($payrollTotal, 0, ',', ' ').' FCFA',
                'variation' => $payrollVariation,
            ],
            'heures_supplementaires' => [
                'value' => round($overtimeData, 1),
                'count' => $presencesMonth->filter(fn ($p) => ($p->worked_hours ?? 0) > 8)->unique('user_id')->count(),
            ],
            'turnover' => [
                'rate' => $turnoverRate,
                'entries' => $entries,
                'exits' => $exits,
            ],
            'tasks' => [
                'completed' => $tasksCompleted,
                'pending' => $tasksPending,
                'total' => $tasksCompleted + $tasksPending,
            ],
            'interns' => [
                'count' => $internsCount,
                'to_evaluate' => $internsToEvaluate,
            ],
            'late_hours' => [
                'total' => round($totalLateMinutes / 60, 1), // En heures
                'employees' => $lateEmployees,
            ],
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
        $deptDistribution = Department::withCount(['users' => fn ($q) => $q->where('role', 'employee')])
            ->get()
            ->map(fn ($d) => ['label' => $d->name, 'value' => $d->users_count]);

        // 3. Recrutements vs Departs (12 mois) - Fixed range
        $months = collect();
        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $recruits = User::whereMonth('hire_date', $date->month)
                ->whereYear('hire_date', $date->year)
                ->when($departmentId, fn ($q) => $q->where('department_id', $departmentId))
                ->count();
            $departures = User::whereMonth('contract_end_date', $date->month)
                ->whereYear('contract_end_date', $date->year)
                ->whereNotNull('contract_end_date') // Assuming DepartedInPeriod logic
                ->when($departmentId, fn ($q) => $q->where('department_id', $departmentId))
                ->count();

            $months->push([
                'label' => $date->translatedFormat('M Y'),
                'recruits' => $recruits,
                'departures' => $departures,
            ]);
        }

        // 4. Taux absenteisme par service
        $absenteism = Department::with(['users' => fn ($q) => $q->where('role', 'employee')])->get()->map(function ($dept) use ($startDate, $endDate) {
            $employees = $dept->users->count();
            if ($employees === 0) {
                return ['label' => $dept->name, 'rate' => 0];
            }

            // Calculate work days in period
            $daysInPeriod = $startDate->diffInDays($endDate) + 1; // Approx check
            // Better: use business days
            $workDays = 22; // Using standard monthly avg for simplicity or verify period
            if ($daysInPeriod < 20) {
                $workDays = 5;
            } // Week

            $expectedPresences = $employees * $workDays;

            $actualPresences = Presence::whereHas('user', fn ($q) => $q->where('department_id', $dept->id))
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
            if ($departmentId) {
                $hoursQuery->forDepartment($departmentId);
            }
            $hours = $hoursQuery->get()->sum('hours_worked') ?? 0;
            $totalWeeklyHours += $hours;

            $weeklyHours->push([
                'label' => 'Sem '.$weekStart->weekOfYear,
                'hours' => round($hours, 1),
            ]);
        }

        // 6. Répartition par type de contrat (NOUVEAU)
        $contractTypes = User::where('role', 'employee')
            ->when($departmentId, fn ($q) => $q->where('department_id', $departmentId))
            ->selectRaw('contract_type, COUNT(*) as count')
            ->groupBy('contract_type')
            ->get()
            ->map(fn ($c) => [
                'label' => match ($c->contract_type) {
                    'cdi' => 'CDI',
                    'cdd' => 'CDD',
                    'stage' => 'Stage',
                    'alternance' => 'Alternance',
                    'freelance' => 'Freelance',
                    'interim' => 'Intérim',
                    default => $c->contract_type ?? 'Autre'
                },
                'value' => $c->count,
            ]);

        // 7. Performance des tâches (NOUVEAU)
        $taskStats = Task::when($departmentId, fn ($q) => $q->whereHas('user', fn ($u) => $u->where('department_id', $departmentId)))
            ->selectRaw('statut, COUNT(*) as count')
            ->groupBy('statut')
            ->get()
            ->mapWithKeys(fn ($t) => [$t->statut => $t->count]);

        // 8. Ponctualité par département (NOUVEAU)
        $punctuality = Department::with(['users' => fn ($q) => $q->where('role', 'employee')])->get()->map(function ($dept) use ($startDate, $endDate) {
            $userIds = $dept->users->pluck('id');
            if ($userIds->isEmpty()) {
                return ['label' => $dept->name, 'on_time' => 0, 'late' => 0];
            }

            $onTime = Presence::whereIn('user_id', $userIds)
                ->whereBetween('date', [$startDate, $endDate])
                ->where('is_late', false)
                ->count();
            $late = Presence::whereIn('user_id', $userIds)
                ->whereBetween('date', [$startDate, $endDate])
                ->where('is_late', true)
                ->count();

            return ['label' => $dept->name, 'on_time' => $onTime, 'late' => $late];
        })->filter(fn ($d) => $d['on_time'] > 0 || $d['late'] > 0);

        // 9. Evolution des stagiaires (recrutements vs fins de stage - 12 derniers mois)
        $internEvolution = collect();
        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $newInterns = User::where('role', 'employee')
                ->where('contract_type', 'stage')
                ->whereMonth('hire_date', $date->month)
                ->whereYear('hire_date', $date->year)
                ->when($departmentId, fn ($q) => $q->where('department_id', $departmentId))
                ->count();
            
            $endedInterns = User::where('role', 'employee')
                ->where('contract_type', 'stage')
                ->whereMonth('contract_end_date', $date->month)
                ->whereYear('contract_end_date', $date->year)
                ->when($departmentId, fn ($q) => $q->where('department_id', $departmentId))
                ->count();

            $internEvolution->push([
                'label' => $date->translatedFormat('M Y'),
                'new' => $newInterns,
                'ended' => $endedInterns,
            ]);
        }

        // 10. Performance moyenne des stagiaires (radar chart data)
        $internPerformance = InternEvaluation::submitted()
            ->whereBetween('week_start', [$startDate, $endDate])
            ->when($departmentId, fn ($q) => $q->whereHas('intern', fn ($u) => $u->where('department_id', $departmentId)))
            ->selectRaw('AVG(discipline_score) as discipline, AVG(behavior_score) as behavior, AVG(skills_score) as skills, AVG(communication_score) as communication')
            ->first();

        // 11. Répartition des stagiaires par département
        $internDepDistribution = Department::withCount(['users' => fn ($q) => $q->where('role', 'employee')->where('contract_type', 'stage')])
            ->get()
            ->map(fn ($d) => ['label' => $d->name, 'value' => $d->users_count])
            ->filter(fn ($d) => $d['value'] > 0);

        return response()->json([
            'presence_trend' => [
                'labels' => $presenceTrend->pluck('day')->map(fn ($d) => Carbon::parse($d)->format('d/m')),
                'data' => $presenceTrend->pluck('count'),
            ],
            'department_distribution' => [
                'labels' => $deptDistribution->pluck('label'),
                'data' => $deptDistribution->pluck('value'),
                'colors' => ['#3B82F6', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6', '#EC4899', '#14B8A6', '#F97316'],
            ],
            'recruitment_turnover' => [
                'labels' => $months->pluck('label'),
                'recrutements' => $months->pluck('recruits'),
                'departs' => $months->pluck('departures'),
            ],
            'absenteisme_par_service' => [
                'labels' => $absenteism->pluck('label'),
                'rates' => $absenteism->pluck('rate'),
            ],
            'heures_travaillees_semaine' => [
                'labels' => $weeklyHours->pluck('label'),
                'data' => $weeklyHours->pluck('hours'),
                'total' => round($totalWeeklyHours, 1),
            ],
            'contract_types' => [
                'labels' => $contractTypes->pluck('label'),
                'data' => $contractTypes->pluck('value'),
                'colors' => ['#6366F1', '#22C55E', '#F59E0B', '#EC4899', '#8B5CF6', '#06B6D4'],
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
                'late' => $punctuality->pluck('late'),
            ],
            'intern_evolution' => [
                'labels' => $internEvolution->pluck('label'),
                'new' => $internEvolution->pluck('new'),
                'ended' => $internEvolution->pluck('ended'),
            ],
            'intern_performance' => [
                'labels' => ['Discipline', 'Comportement', 'Compétences', 'Communication'],
                'data' => [
                    round($internPerformance->discipline ?? 0, 1),
                    round($internPerformance->behavior ?? 0, 1),
                    round($internPerformance->skills ?? 0, 1),
                    round($internPerformance->communication ?? 0, 1),
                ],
            ],
            'intern_department_distribution' => [
                'labels' => $internDepDistribution->pluck('label'),
                'data' => $internDepDistribution->pluck('value'),
                'colors' => ['#6366F1', '#EC4899', '#8B5CF6', '#10B981', '#F59E0B'],
            ],
            // Advanced Task Analytics
            'task_status_distribution' => [
                'labels' => ['Terminée', 'En cours', 'Approuvée', 'En attente', 'Annulée'],
                'data' => [
                    $taskStats['completed'] ?? 0,
                    $taskStats['in_progress'] ?? 0,
                    $taskStats['approved'] ?? 0,
                    $taskStats['pending'] ?? 0,
                    $taskStats['cancelled'] ?? 0,
                ],
                'colors' => ['#10B981', '#3B82F6', '#6366F1', '#F59E0B', '#EF4444'],
            ],
            'task_completion_evolution' => $this->getTaskCompletionEvolution($departmentId),
            'tasks_by_department' => Department::withCount(['users as tasks_count' => function ($query) {
                $query->join('tasks', 'users.id', '=', 'tasks.user_id')
                      ->whereIn('tasks.statut', ['pending', 'in_progress', 'approved']);
            }])->get()->map(fn($d) => ['label' => $d->name, 'value' => $d->tasks_count]),
            
            // Employee Demographics
            'gender_distribution' => User::where('role', 'employee')
                ->when($departmentId, fn ($q) => $q->where('department_id', $departmentId))
                ->selectRaw('gender, COUNT(*) as count')
                ->groupBy('gender')
                ->get()
                ->map(fn($g) => [
                    'label' => $g->gender === 'male' ? 'Hommes' : ($g->gender === 'female' ? 'Femmes' : 'Non spécifié'),
                    'value' => $g->count
                ]),
            'age_pyramid' => $this->getAgePyramid($departmentId),
            'seniority_distribution' => $this->getSeniorityDistribution($departmentId),
        ]);
    }

    public function getRecentActivities(Request $request): JsonResponse
    {
        // Cache pour 1 minute
        $data = Cache::remember('analytics_activities', 60, function () {
            $activities = collect();

            // Recent presences
            $presences = Presence::with('user:id,name,avatar')
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get()
                ->map(function ($p) {
                    return [
                        'type' => 'presence',
                        'user' => $p->user->name ?? 'Inconnu',
                        'avatar' => $p->user && $p->user->avatar ? avatar_url($p->user->avatar) : null,
                        'description' => 'a pointé son arrivée à '.($p->check_in_formatted ?? $p->created_at->format('H:i')),
                        'time' => $p->created_at->diffForHumans(),
                    ];
                });

            // Recent leaves
            $leaves = Leave::with('user:id,name,avatar')
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get()
                ->map(function ($l) {
                    return [
                        'type' => 'leave',
                        'user' => $l->user->name ?? 'Inconnu',
                        'avatar' => $l->user && $l->user->avatar ? avatar_url($l->user->avatar) : null,
                        'description' => 'a demandé un congé ('.$l->type_label.')',
                        'time' => $l->created_at->diffForHumans(),
                    ];
                });

            // Recent tasks completed
            $tasks = Task::with('user:id,name,avatar')
                ->where('statut', 'completed')
                ->orderBy('updated_at', 'desc')
                ->take(5)
                ->get()
                ->map(function ($t) {
                    $user = $t->user;

                    return [
                        'type' => 'task',
                        'user' => $user ? $user->name : 'Système',
                        'avatar' => $user && $user->avatar ? avatar_url($user->avatar) : null,
                        'description' => 'a terminé la tâche "'.$t->titre.'"',
                        'time' => $t->updated_at->diffForHumans(),
                    ];
                });

            return $presences->merge($leaves)->merge($tasks)->sortByDesc('time')->take(10)->values();
        });

        return response()->json($data);
    }

    public function getPendingRequests(Request $request): JsonResponse
    {
        // Cache pour 2 minutes
        $data = Cache::remember('analytics_pending', 120, function () {
            return Leave::with('user:id,name')
                ->pending()
                ->take(5)
                ->get()
                ->map(function ($l) {
                    return [
                        'id' => $l->id,
                        'type' => 'Congé',
                        'user' => $l->user->name ?? 'Inconnu',
                        'details' => $l->type_label.' ('.$l->duree.' jours)',
                        'date' => $l->created_at->format('d/m/Y'),
                    ];
                });
        });

        return response()->json($data);
    }

    public function getTopLatecomers(Request $request): JsonResponse
    {
        $month = now()->month;
        $year = now()->year;

        // Cache pour 5 minutes
        // BUGFIX: $year doit être utilisé dans la requête aussi, pas seulement dans la clé de cache
        $data = Cache::remember("analytics_latecomers_{$month}_{$year}", 300, function () use ($month, $year) {
            return Presence::where('is_late', true)
                ->whereMonth('date', $month)
                ->whereYear('date', $year)
                ->select('user_id', DB::raw('COUNT(*) as late_count'), DB::raw('SUM(late_minutes) as total_minutes'))
                ->groupBy('user_id')
                ->orderByDesc('late_count')
                ->limit(5)
                ->with('user:id,name,department_id', 'user.department:id,name')
                ->get()
                ->map(fn ($p, $i) => [
                    'rank' => $i + 1,
                    'user_id' => $p->user_id,
                    'name' => $p->user->name ?? 'Inconnu',
                    'department' => $p->user->department->name ?? '-',
                    'count' => $p->late_count,
                    'avg_minutes' => $p->late_count > 0 ? round($p->total_minutes / $p->late_count) : 0,
                ]);
        });

        return response()->json($data);
    }

    public function getHrAlerts(Request $request): JsonResponse
    {
        // Cache pour 10 minutes
        $data = Cache::remember('analytics_hr_alerts', 600, function () {
            return [
                'contracts' => User::expiringContracts()->with('department:id,name')->limit(5)->get()->map(fn ($u) => [
                    'name' => $u->name,
                    'department' => $u->department->name ?? '-',
                    'end_date' => $u->contract_end_date ? $u->contract_end_date->format('d/m/Y') : '-',
                    'days' => $u->contract_end_date ? now()->diffInDays($u->contract_end_date) : 0,
                ]),
                'birthdays' => User::upcomingBirthdays()->limit(5)->get()->map(fn ($u) => [
                    'name' => $u->name,
                    'date' => $u->date_of_birth ? $u->date_of_birth->format('d/m') : '-',
                    'age' => $u->date_of_birth ? $u->date_of_birth->age + 1 : 0,
                ]),
            ];
        });

        return response()->json($data);
    }

    /**
     * Get top performers based on evaluations
     */
    public function getTopPerformers(Request $request): JsonResponse
    {
        $month = (int) $request->get('custom_month', now()->month);
        $year = (int) $request->get('custom_year', now()->year);
        $departmentId = $request->get('department_id');

        // Cache pour 5 minutes avec clé unique
        $cacheKey = "analytics_top_performers_{$month}_{$year}_{$departmentId}";

        $data = Cache::remember($cacheKey, 300, function () use ($month, $year, $departmentId) {
            // Top employés par évaluation
            $topEmployees = EmployeeEvaluation::with(['user:id,name,avatar,department_id', 'user.department:id,name'])
                ->forPeriod($month, $year)
                ->validated()
                ->when($departmentId, fn ($q) => $q->whereHas('user', fn ($u) => $u->where('department_id', $departmentId)))
                ->orderByDesc('total_score')
                ->limit(5)
                ->get()
                ->map(fn ($eval, $index) => [
                    'rank' => $index + 1,
                    'name' => $eval->user->name ?? '-',
                    'avatar' => $eval->user && $eval->user->avatar ? avatar_url($eval->user->avatar) : null,
                    'department' => $eval->user->department->name ?? '-',
                    'score' => $eval->total_score,
                    'max_score' => EmployeeEvaluation::MAX_SCORE,
                    'percentage' => $eval->score_percentage,
                ]);

            // Top stagiaires par évaluation (dernières 4 semaines)
            $weekStart = now()->startOfWeek();
            $topInternsData = InternEvaluation::submitted()
                ->where('week_start', '>=', $weekStart->copy()->subWeeks(4))
                ->when($departmentId, fn ($q) => $q->whereHas('intern', fn ($u) => $u->where('department_id', $departmentId)))
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
                    'department' => $intern && $intern->department ? $intern->department->name : '-',
                    'score' => round($eval->avg_score ?? 0, 1),
                    'max_score' => 10,
                    'percentage' => $eval->avg_score ? round(($eval->avg_score / 10) * 100, 1) : 0,
                ];
            });

            return [
                'employees' => $topEmployees,
                'interns' => $topInterns,
            ];
        });

        return response()->json($data);
    }

    /**
     * Get best attendance rankings
     */
    public function getBestAttendance(Request $request): JsonResponse
    {
        $month = (int) $request->get('custom_month', now()->month);
        $year = (int) $request->get('custom_year', now()->year);
        $departmentId = $request->get('department_id');

        // Cache pour 5 minutes avec clé unique
        $cacheKey = "analytics_best_attendance_{$month}_{$year}_{$departmentId}";

        $data = Cache::remember($cacheKey, 300, function () use ($month, $year, $departmentId) {
            // Meilleure assiduité (plus de présences, moins de retards)
            $attendanceData = Presence::whereMonth('date', $month)
                ->whereYear('date', $year)
                ->when($departmentId, fn ($q) => $q->forDepartment($departmentId))
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

            return $attendanceData->map(function ($p, $index) use ($users, $totalHours) {
                $user = $users->get($p->user_id);

                return [
                    'rank' => $index + 1,
                    'name' => $user->name ?? '-',
                    'avatar' => $user && $user->avatar ? avatar_url($user->avatar) : null,
                    'department' => $user && $user->department ? $user->department->name : '-',
                    'presence_count' => (int) $p->presence_count,
                    'on_time_count' => (int) $p->on_time_count,
                    'late_count' => (int) $p->late_count,
                    'punctuality_rate' => $p->presence_count > 0
                        ? round(($p->on_time_count / $p->presence_count) * 100, 1)
                        : 0,
                    'total_hours' => round($totalHours[$p->user_id] ?? 0, 1),
                ];
            });
        });

        return response()->json($data);
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
            ->when($departmentId, fn ($q) => $q->whereHas('user', fn ($u) => $u->where('department_id', $departmentId)))
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
            ->when($departmentId, fn ($q) => $q->where('department_id', $departmentId))
            ->whereNotIn('id', $evaluatedUserIds)
            ->count();

        // Distribution des notes (pour graphique)
        $scoreDistribution = EmployeeEvaluation::forPeriod($month, $year)
            ->validated()
            ->when($departmentId, fn ($q) => $q->whereHas('user', fn ($u) => $u->where('department_id', $departmentId)))
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
            ->when($departmentId, fn ($q) => $q->whereHas('intern', fn ($u) => $u->where('department_id', $departmentId)))
            ->selectRaw('COUNT(*) as total')
            ->selectRaw('AVG(discipline_score + behavior_score + skills_score + communication_score) as avg_score')
            ->first();

        // Stagiaires non évalués cette semaine
        $currentWeekStart = now()->startOfWeek();
        $evaluatedInternIds = InternEvaluation::where('week_start', $currentWeekStart)->pluck('intern_id');
        $internsNotEvaluated = User::where('role', 'employee')
            ->where('contract_type', 'stage')
            ->when($departmentId, fn ($q) => $q->where('department_id', $departmentId))
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
        $start = match ($period) {
            'today' => now(),
            'week' => now()->startOfWeek(),
            'month' => now()->startOfMonth(),
            'year' => now()->startOfYear(),
            default => now()->subMonth()
        };

        return ['start' => $start, 'end' => $end];
    }

    private function getTaskCompletionEvolution($departmentId)
    {
        $data = collect();
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $completed = Task::where('statut', 'completed')
                ->whereMonth('updated_at', $month->month)
                ->whereYear('updated_at', $month->year)
                ->when($departmentId, fn ($q) => $q->whereHas('user', fn ($u) => $u->where('department_id', $departmentId)))
                ->count();
            $created = Task::whereMonth('created_at', $month->month)
                ->whereYear('created_at', $month->year)
                ->when($departmentId, fn ($q) => $q->whereHas('user', fn ($u) => $u->where('department_id', $departmentId)))
                ->count();

            $data->push([
                'label' => $month->translatedFormat('M'),
                'completed' => $completed,
                'created' => $created,
            ]);
        }
        return [
            'labels' => $data->pluck('label'),
            'completed' => $data->pluck('completed'),
            'created' => $data->pluck('created'),
        ];
    }

    private function getAgePyramid($departmentId)
    {
        // Buckets: <25, 25-30, 30-40, 40-50, 50+
        $users = User::where('role', 'employee')
            ->when($departmentId, fn ($q) => $q->where('department_id', $departmentId))
            ->whereNotNull('date_of_birth')
            ->get();
        
        $buckets = [
            '< 25' => 0,
            '25-30' => 0,
            '30-40' => 0,
            '40-50' => 0,
            '50+' => 0,
        ];

        foreach ($users as $user) {
            $age = $user->date_of_birth->age;
            if ($age < 25) $buckets['< 25']++;
            elseif ($age < 30) $buckets['25-30']++;
            elseif ($age < 40) $buckets['30-40']++;
            elseif ($age < 50) $buckets['40-50']++;
            else $buckets['50+']++;
        }

        return [
            'labels' => array_keys($buckets),
            'data' => array_values($buckets),
        ];
    }

    private function getSeniorityDistribution($departmentId)
    {
        // Buckets: < 1 an, 1-3 ans, 3-5 ans, 5+ ans
        $users = User::where('role', 'employee')
            ->when($departmentId, fn ($q) => $q->where('department_id', $departmentId))
            ->whereNotNull('hire_date')
            ->get();

        $buckets = [
            '< 1 an' => 0,
            '1-3 ans' => 0,
            '3-5 ans' => 0,
            '5+ ans' => 0,
        ];

        foreach ($users as $user) {
            $years = $user->hire_date->diffInYears(now());
            if ($years < 1) $buckets['< 1 an']++;
            elseif ($years < 3) $buckets['1-3 ans']++;
            elseif ($years < 5) $buckets['3-5 ans']++;
            else $buckets['5+ ans']++;
        }

        return [
            'labels' => array_keys($buckets),
            'data' => array_values($buckets),
        ];
    }

    /**
     * Export analytics data to PDF
     */
    public function exportPdf(Request $request)
    {
        $data = $this->getExportData($request);

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.analytics-report', $data);
        $pdf->setPaper('a4', 'portrait');

        return $pdf->download('rapport-analytics-'.now()->format('Y-m-d').'.pdf');
    }

    /**
     * Export analytics data to CSV
     */
    public function exportExcel(Request $request)
    {
        $data = $this->getExportData($request);
        $kpis = $data['kpis'];
        $charts = $data['charts'];

        $filename = 'rapport-analytics-' . now()->format('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($data, $kpis, $charts) {
            $file = fopen('php://output', 'w');
            // BOM UTF-8 pour Excel
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // ============================
            // 1. INDICATEURS CLÉS (KPIs)
            // ============================
            fputcsv($file, ['=== INDICATEURS CLÉS ==='], ';');
            fputcsv($file, ['Indicateur', 'Valeur', 'Détail'], ';');
            fputcsv($file, ['Effectif total', $kpis['effectif_total']['value'] ?? 0, 'Variation: ' . ($kpis['effectif_total']['variation'] ?? 0) . '%'], ';');
            fputcsv($file, ['Taux de ponctualité', ($kpis['punctuality_today']['percentage'] ?? 0) . '%', 'Présences à l\'heure: ' . ($kpis['punctuality_today']['on_time'] ?? 0)], ';');
            fputcsv($file, ['En congé', $kpis['en_conge']['value'] ?? 0, 'Congé: ' . ($kpis['en_conge']['types']['conge'] ?? 0) . ' | Maladie: ' . ($kpis['en_conge']['types']['maladie'] ?? 0) . ' | Autre: ' . ($kpis['en_conge']['types']['autre'] ?? 0)], ';');
            fputcsv($file, ['Absents non justifiés', $kpis['absents_non_justifies']['value'] ?? 0, ''], ';');
            fputcsv($file, ['Masse salariale', $kpis['masse_salariale']['formatted'] ?? '0 FCFA', 'Variation: ' . ($kpis['masse_salariale']['variation'] ?? 0) . '%'], ';');
            fputcsv($file, ['Heures supplémentaires', ($kpis['heures_supplementaires']['value'] ?? 0) . 'h', 'Employés concernés: ' . ($kpis['heures_supplementaires']['count'] ?? 0)], ';');
            fputcsv($file, ['Turnover', ($kpis['turnover']['rate'] ?? 0) . '%', 'Entrées: ' . ($kpis['turnover']['entries'] ?? 0) . ' | Sorties: ' . ($kpis['turnover']['exits'] ?? 0)], ';');
            fputcsv($file, ['Tâches terminées', $kpis['tasks']['completed'] ?? 0, 'Total: ' . ($kpis['tasks']['total'] ?? 0)], ';');
            fputcsv($file, ['Tâches en cours', $kpis['tasks']['pending'] ?? 0, ''], ';');
            fputcsv($file, ['Stagiaires', $kpis['interns']['count'] ?? 0, 'À évaluer: ' . ($kpis['interns']['to_evaluate'] ?? 0)], ';');
            fputcsv($file, ['Retards totaux', ($kpis['late_hours']['total'] ?? 0) . 'h', 'Employés en retard: ' . ($kpis['late_hours']['employees'] ?? 0)], ';');
            fputcsv($file, [], ';');

            // ============================
            // 2. EFFECTIF PAR DÉPARTEMENT
            // ============================
            fputcsv($file, ['=== EFFECTIF PAR DÉPARTEMENT ==='], ';');
            fputcsv($file, ['Département', 'Effectif'], ';');
            foreach ($data['department_stats'] as $dept) {
                fputcsv($file, [$dept['name'], $dept['count']], ';');
            }
            fputcsv($file, [], ';');

            // ============================
            // 3. RÉPARTITION PAR TYPE DE CONTRAT
            // ============================
            if (!empty($charts['contract_types']['labels'])) {
                fputcsv($file, ['=== RÉPARTITION PAR TYPE DE CONTRAT ==='], ';');
                fputcsv($file, ['Type de contrat', 'Nombre'], ';');
                $labels = $charts['contract_types']['labels'];
                $values = $charts['contract_types']['data'];
                for ($i = 0; $i < count($labels); $i++) {
                    fputcsv($file, [$labels[$i] ?? '', $values[$i] ?? 0], ';');
                }
                fputcsv($file, [], ';');
            }

            // ============================
            // 4. ÉVOLUTION DES PRÉSENCES
            // ============================
            if (!empty($charts['presence_trend']['labels'])) {
                fputcsv($file, ['=== ÉVOLUTION DES PRÉSENCES ==='], ';');
                fputcsv($file, ['Date', 'Nombre de présences'], ';');
                $labels = $charts['presence_trend']['labels'];
                $values = $charts['presence_trend']['data'];
                for ($i = 0; $i < count($labels); $i++) {
                    fputcsv($file, [$labels[$i] ?? '', $values[$i] ?? 0], ';');
                }
                fputcsv($file, [], ';');
            }

            // ============================
            // 5. HEURES TRAVAILLÉES PAR SEMAINE
            // ============================
            if (!empty($charts['heures_travaillees_semaine']['labels'])) {
                fputcsv($file, ['=== HEURES TRAVAILLÉES PAR SEMAINE ==='], ';');
                fputcsv($file, ['Semaine', 'Heures'], ';');
                $labels = $charts['heures_travaillees_semaine']['labels'];
                $values = $charts['heures_travaillees_semaine']['data'];
                for ($i = 0; $i < count($labels); $i++) {
                    fputcsv($file, [$labels[$i] ?? '', $values[$i] ?? 0], ';');
                }
                fputcsv($file, ['Total', $charts['heures_travaillees_semaine']['total'] ?? 0], ';');
                fputcsv($file, [], ';');
            }

            // ============================
            // 6. ABSENTÉISME PAR SERVICE
            // ============================
            if (!empty($charts['absenteisme_par_service']['labels'])) {
                fputcsv($file, ['=== TAUX D\'ABSENTÉISME PAR SERVICE ==='], ';');
                fputcsv($file, ['Département', 'Taux (%)'], ';');
                $labels = $charts['absenteisme_par_service']['labels'];
                $rates = $charts['absenteisme_par_service']['rates'];
                for ($i = 0; $i < count($labels); $i++) {
                    fputcsv($file, [$labels[$i] ?? '', ($rates[$i] ?? 0) . '%'], ';');
                }
                fputcsv($file, [], ';');
            }

            // ============================
            // 7. PONCTUALITÉ PAR DÉPARTEMENT
            // ============================
            if (!empty($charts['punctuality']['labels'])) {
                fputcsv($file, ['=== PONCTUALITÉ PAR DÉPARTEMENT ==='], ';');
                fputcsv($file, ['Département', 'À l\'heure', 'En retard'], ';');
                $labels = $charts['punctuality']['labels'];
                $onTime = $charts['punctuality']['on_time'];
                $late = $charts['punctuality']['late'];
                for ($i = 0; $i < count($labels); $i++) {
                    fputcsv($file, [$labels[$i] ?? '', $onTime[$i] ?? 0, $late[$i] ?? 0], ';');
                }
                fputcsv($file, [], ';');
            }

            // ============================
            // 8. RECRUTEMENTS VS DÉPARTS (12 mois)
            // ============================
            if (!empty($charts['recruitment_turnover']['labels'])) {
                fputcsv($file, ['=== RECRUTEMENTS VS DÉPARTS (12 MOIS) ==='], ';');
                fputcsv($file, ['Mois', 'Recrutements', 'Départs'], ';');
                $labels = $charts['recruitment_turnover']['labels'];
                $recruits = $charts['recruitment_turnover']['recrutements'];
                $departs = $charts['recruitment_turnover']['departs'];
                for ($i = 0; $i < count($labels); $i++) {
                    fputcsv($file, [$labels[$i] ?? '', $recruits[$i] ?? 0, $departs[$i] ?? 0], ';');
                }
                fputcsv($file, [], ';');
            }

            // ============================
            // 9. ÉTAT DES TÂCHES
            // ============================
            if (!empty($charts['task_status_distribution']['labels'])) {
                fputcsv($file, ['=== ÉTAT DES TÂCHES ==='], ';');
                fputcsv($file, ['Statut', 'Nombre'], ';');
                $labels = $charts['task_status_distribution']['labels'];
                $values = $charts['task_status_distribution']['data'];
                for ($i = 0; $i < count($labels); $i++) {
                    fputcsv($file, [$labels[$i] ?? '', $values[$i] ?? 0], ';');
                }
                fputcsv($file, [], ';');
            }

            // ============================
            // 10. TÂCHES CRÉÉES VS TERMINÉES (6 mois)
            // ============================
            if (!empty($charts['task_completion_evolution']['labels'])) {
                fputcsv($file, ['=== TÂCHES CRÉÉES VS TERMINÉES (6 MOIS) ==='], ';');
                fputcsv($file, ['Mois', 'Créées', 'Terminées'], ';');
                $labels = $charts['task_completion_evolution']['labels'];
                $created = $charts['task_completion_evolution']['created'];
                $completed = $charts['task_completion_evolution']['completed'];
                for ($i = 0; $i < count($labels); $i++) {
                    fputcsv($file, [$labels[$i] ?? '', $created[$i] ?? 0, $completed[$i] ?? 0], ';');
                }
                fputcsv($file, [], ';');
            }

            // ============================
            // 11. CHARGE DE TRAVAIL PAR DÉPARTEMENT
            // ============================
            if (!empty($charts['tasks_by_department'])) {
                fputcsv($file, ['=== CHARGE DE TRAVAIL PAR DÉPARTEMENT ==='], ';');
                fputcsv($file, ['Département', 'Tâches actives'], ';');
                foreach ($charts['tasks_by_department'] as $item) {
                    fputcsv($file, [$item['label'] ?? '', $item['value'] ?? 0], ';');
                }
                fputcsv($file, [], ';');
            }

            // ============================
            // 12. STAGIAIRES - ÉVOLUTION
            // ============================
            if (!empty($charts['intern_evolution']['labels'])) {
                fputcsv($file, ['=== STAGIAIRES - RECRUTEMENTS VS FINS DE STAGE (12 MOIS) ==='], ';');
                fputcsv($file, ['Mois', 'Nouveaux', 'Fins de stage'], ';');
                $labels = $charts['intern_evolution']['labels'];
                $newI = $charts['intern_evolution']['new'];
                $ended = $charts['intern_evolution']['ended'];
                for ($i = 0; $i < count($labels); $i++) {
                    fputcsv($file, [$labels[$i] ?? '', $newI[$i] ?? 0, $ended[$i] ?? 0], ';');
                }
                fputcsv($file, [], ';');
            }

            // ============================
            // 13. STAGIAIRES - PERFORMANCE MOYENNE
            // ============================
            if (!empty($charts['intern_performance']['labels'])) {
                fputcsv($file, ['=== PERFORMANCE MOYENNE DES STAGIAIRES ==='], ';');
                fputcsv($file, ['Critère', 'Note (/10)'], ';');
                $labels = $charts['intern_performance']['labels'];
                $values = $charts['intern_performance']['data'];
                for ($i = 0; $i < count($labels); $i++) {
                    fputcsv($file, [$labels[$i] ?? '', $values[$i] ?? 0], ';');
                }
                fputcsv($file, [], ';');
            }

            // ============================
            // 14. DÉMOGRAPHIE - PARITÉ H/F
            // ============================
            if (!empty($charts['gender_distribution'])) {
                fputcsv($file, ['=== PARITÉ (H/F) ==='], ';');
                fputcsv($file, ['Genre', 'Nombre'], ';');
                foreach ($charts['gender_distribution'] as $g) {
                    fputcsv($file, [$g['label'] ?? '', $g['value'] ?? 0], ';');
                }
                fputcsv($file, [], ';');
            }

            // ============================
            // 15. DÉMOGRAPHIE - PYRAMIDE DES ÂGES
            // ============================
            if (!empty($charts['age_pyramid']['labels'])) {
                fputcsv($file, ['=== PYRAMIDE DES ÂGES ==='], ';');
                fputcsv($file, ['Tranche d\'âge', 'Nombre'], ';');
                $labels = $charts['age_pyramid']['labels'];
                $values = $charts['age_pyramid']['data'];
                for ($i = 0; $i < count($labels); $i++) {
                    fputcsv($file, [$labels[$i] ?? '', $values[$i] ?? 0], ';');
                }
                fputcsv($file, [], ';');
            }

            // ============================
            // 16. DÉMOGRAPHIE - ANCIENNETÉ
            // ============================
            if (!empty($charts['seniority_distribution']['labels'])) {
                fputcsv($file, ['=== DISTRIBUTION PAR ANCIENNETÉ ==='], ';');
                fputcsv($file, ['Ancienneté', 'Nombre'], ';');
                $labels = $charts['seniority_distribution']['labels'];
                $values = $charts['seniority_distribution']['data'];
                for ($i = 0; $i < count($labels); $i++) {
                    fputcsv($file, [$labels[$i] ?? '', $values[$i] ?? 0], ';');
                }
                fputcsv($file, [], ';');
            }

            // ============================
            // 17. TOP RETARDATAIRES
            // ============================
            fputcsv($file, ['=== TOP RETARDATAIRES ==='], ';');
            fputcsv($file, ['Rang', 'Nom', 'Département', 'Nb retards', 'Moy. minutes'], ';');
            if (!empty($data['latecomers'])) {
                foreach ($data['latecomers'] as $l) {
                    fputcsv($file, [$l['rank'], $l['name'], $l['department'], $l['count'], $l['avg_minutes']], ';');
                }
            }
            fputcsv($file, [], ';');

            // ============================
            // 18. MEILLEURE ASSIDUITÉ
            // ============================
            fputcsv($file, ['=== MEILLEURE ASSIDUITÉ ==='], ';');
            fputcsv($file, ['Rang', 'Nom', 'Département', 'Présences', 'À l\'heure', 'Retards', 'Taux ponctualité (%)', 'Heures totales'], ';');
            if (!empty($data['best_attendance'])) {
                foreach ($data['best_attendance'] as $a) {
                    fputcsv($file, [$a['rank'], $a['name'], $a['department'], $a['presence_count'], $a['on_time_count'], $a['late_count'], $a['punctuality_rate'] . '%', $a['total_hours']], ';');
                }
            }
            fputcsv($file, [], ';');

            // ============================
            // 19. TOP PERFORMERS
            // ============================
            fputcsv($file, ['=== TOP PERFORMERS (EMPLOYÉS) ==='], ';');
            fputcsv($file, ['Rang', 'Nom', 'Département', 'Score', 'Pourcentage (%)'], ';');
            if (!empty($data['top_performers']['employees'])) {
                foreach ($data['top_performers']['employees'] as $p) {
                    fputcsv($file, [$p['rank'], $p['name'], $p['department'], $p['score'], $p['percentage'] . '%'], ';');
                }
            }
            fputcsv($file, [], ';');

            fputcsv($file, ['=== TOP PERFORMERS (STAGIAIRES) ==='], ';');
            fputcsv($file, ['Rang', 'Nom', 'Département', 'Score', 'Pourcentage (%)'], ';');
            if (!empty($data['top_performers']['interns'])) {
                foreach ($data['top_performers']['interns'] as $p) {
                    fputcsv($file, [$p['rank'], $p['name'], $p['department'], $p['score'], $p['percentage'] . '%'], ';');
                }
            }
            fputcsv($file, [], ';');

            // ============================
            // 20. STATISTIQUES ÉVALUATIONS
            // ============================
            if (!empty($data['evaluation_stats'])) {
                $evalEmp = $data['evaluation_stats']['employees'] ?? [];
                $evalInt = $data['evaluation_stats']['interns'] ?? [];

                fputcsv($file, ['=== STATISTIQUES ÉVALUATIONS EMPLOYÉS ==='], ';');
                fputcsv($file, ['Indicateur', 'Valeur'], ';');
                fputcsv($file, ['Total évaluations', $evalEmp['total'] ?? 0], ';');
                fputcsv($file, ['Validées', $evalEmp['validated'] ?? 0], ';');
                fputcsv($file, ['Brouillons', $evalEmp['draft'] ?? 0], ';');
                fputcsv($file, ['Non évalués', $evalEmp['not_evaluated'] ?? 0], ';');
                fputcsv($file, ['Score moyen', $evalEmp['avg_score'] ?? 0], ';');
                fputcsv($file, ['Score max', $evalEmp['max_score'] ?? 0], ';');
                fputcsv($file, ['Score min', $evalEmp['min_score'] ?? 0], ';');
                fputcsv($file, [], ';');

                fputcsv($file, ['=== STATISTIQUES ÉVALUATIONS STAGIAIRES ==='], ';');
                fputcsv($file, ['Indicateur', 'Valeur'], ';');
                fputcsv($file, ['Total évaluations (4 sem.)', $evalInt['total_evaluations'] ?? 0], ';');
                fputcsv($file, ['Score moyen', $evalInt['avg_score'] ?? 0], ';');
                fputcsv($file, ['Non évalués cette semaine', $evalInt['not_evaluated_this_week'] ?? 0], ';');
                fputcsv($file, [], ';');
            }

            // ============================
            // 21. CONGÉS EN ATTENTE
            // ============================
            fputcsv($file, ['=== CONGÉS EN ATTENTE ==='], ';');
            fputcsv($file, ['Employé', 'Type', 'Date début', 'Date fin', 'Durée (jours)'], ';');
            foreach ($data['pending_leaves'] as $leave) {
                fputcsv($file, [
                    $leave->user->name ?? 'Inconnu',
                    $leave->type_label ?? $leave->type,
                    $leave->date_debut?->format('d/m/Y') ?? '-',
                    $leave->date_fin?->format('d/m/Y') ?? '-',
                    $leave->duree ?? '-',
                ], ';');
            }
            fputcsv($file, [], ';');

            // ============================
            // 22. LISTE COMPLÈTE DES EMPLOYÉS
            // ============================
            fputcsv($file, ['=== LISTE DES EMPLOYÉS ==='], ';');
            fputcsv($file, ['Nom', 'Email', 'Département', 'Poste', 'Type contrat', 'Date embauche', 'Statut'], ';');
            foreach ($data['employees'] as $emp) {
                fputcsv($file, [
                    $emp->name,
                    $emp->email,
                    $emp->department->name ?? '-',
                    $emp->position->name ?? '-',
                    strtoupper($emp->contract_type ?? '-'),
                    $emp->hire_date?->format('d/m/Y') ?? '-',
                    $emp->status === 'active' ? 'Actif' : ($emp->status ?? '-'),
                ], ';');
            }
            fputcsv($file, [], ';');

            // ============================
            // PIED DE PAGE
            // ============================
            fputcsv($file, ['=== INFORMATIONS DU RAPPORT ==='], ';');
            fputcsv($file, ['Rapport généré le', $data['generated_at']], ';');
            fputcsv($file, ['Période', $data['period_label']], ';');
            if ($data['department']) {
                fputcsv($file, ['Département filtré', $data['department']->name], ';');
            }
            fputcsv($file, ['Application', 'ManageX - Analytics RH'], ';');

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Get data for export (PDF/Excel)
     */
    private function getExportData(Request $request): array
    {
        $period = $request->get('period', 'month');
        $departmentId = $request->get('department_id');

        // Période
        $periodLabel = match ($period) {
            'today' => "Aujourd'hui",
            'week' => 'Cette semaine',
            'month' => 'Ce mois',
            'year' => 'Cette année',
            default => 'Ce mois'
        };

        // Récupérer les KPIs via la méthode existante
        $kpisResponse = $this->getKpiData($request);
        $kpis = json_decode($kpisResponse->getContent(), true);

        // Récupérer les données des graphiques
        $chartsResponse = $this->getChartsData($request);
        $charts = json_decode($chartsResponse->getContent(), true);

        // Département sélectionné
        $department = $departmentId ? Department::find($departmentId) : null;

        // Effectif par département
        $departmentStats = Department::withCount(['users' => fn ($q) => $q->where('role', 'employee')])
            ->get()
            ->map(fn ($d) => [
                'name' => $d->name,
                'count' => $d->users_count,
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

        // Liste complète des employés
        $employees = User::where('role', 'employee')
            ->with(['department:id,name', 'position:id,name'])
            ->when($departmentId, fn ($q) => $q->where('department_id', $departmentId))
            ->orderBy('name')
            ->get();

        return [
            'title' => 'Rapport Analytics RH',
            'period_label' => $periodLabel,
            'department' => $department,
            'generated_at' => now()->format('d/m/Y H:i'),
            'kpis' => $kpis,
            'charts' => $charts,
            'department_stats' => $departmentStats,
            'latecomers' => $latecomers,
            'pending_leaves' => $pendingLeaves,
            'top_performers' => $topPerformers,
            'best_attendance' => $bestAttendance,
            'evaluation_stats' => $evaluationStats,
            'employees' => $employees,
        ];
    }

    /**
     * Get AI-generated insights based on current KPIs.
     */
    public function getAiInsights(Request $request, AnalyticsInsightService $insightService): JsonResponse
    {
        $kpisResponse = $this->getKpiData($request);
        $kpis = json_decode($kpisResponse->getContent(), true);

        $filters = [
            'period' => $request->get('period', 'month'),
            'department_id' => $request->get('department_id'),
            'contract_type' => $request->get('contract_type'),
        ];

        $insights = $insightService->generateInsights($kpis, $filters);

        return response()->json([
            'insights' => $insights,
            'available' => $insights !== null,
        ]);
    }
}
