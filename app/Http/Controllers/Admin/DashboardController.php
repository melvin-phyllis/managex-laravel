<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Leave;
use App\Models\Presence;
use App\Models\Setting;
use App\Models\Survey;
use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = $this->getBaseStats();
        $advancedStats = $this->getAdvancedStats();

        // Données pour les graphiques
        $presenceData = $this->getMonthlyPresenceData();
        $taskData = $this->getTaskStatusData();
        $leaveData = $this->getMonthlyLeaveData();

        // Nouvelles données
        $alerts = $this->getAlerts();
        $recentActivities = $this->getRecentActivities();
        $calendarEvents = $this->getCalendarEvents();
        $departmentStats = $this->getDepartmentStats();

        $hasReportEmail = ! empty(Setting::get('report_email'));

        return view('admin.dashboard', compact(
            'stats',
            'advancedStats',
            'presenceData',
            'taskData',
            'leaveData',
            'alerts',
            'recentActivities',
            'calendarEvents',
            'departmentStats',
            'hasReportEmail'
        ));
    }

    public function getStats()
    {
        // Endpoint pour le polling AJAX
        return response()->json([
            'stats' => $this->getBaseStats(),
            'advancedStats' => $this->getAdvancedStats(),
            'presence_data' => $this->getMonthlyPresenceData(),
            'task_data' => $this->getTaskStatusData(),
            'leave_data' => $this->getMonthlyLeaveData(),
        ]);
    }

    /**
     * Get recent activities for the activity feed
     */
    public function getRecentActivity()
    {
        return response()->json($this->getRecentActivities());
    }

    /**
     * Get alerts for the alert center
     */
    public function getAlertsData()
    {
        return response()->json($this->getAlerts());
    }

    /**
     * Get calendar events
     */
    public function getCalendarEventsData(Request $request)
    {
        $month = $request->input('month', now()->month);
        $year = $request->input('year', now()->year);

        return response()->json($this->getCalendarEvents($month, $year));
    }

    private function getBaseStats(): array
    {
        return [
            'total_employees' => User::where('role', 'employee')->count(),
            'presences_today' => Presence::today()->count(),
            'pending_tasks' => Task::pending()->count(),
            'pending_leaves' => Leave::pending()->count(),
            'active_surveys' => Survey::active()->count(),
        ];
    }

    private function getAdvancedStats(): array
    {
        $totalEmployees = User::where('role', 'employee')->count();
        $presencesToday = Presence::today()->count();

        // Taux de présence aujourd'hui
        $presenceRate = $totalEmployees > 0
            ? round(($presencesToday / $totalEmployees) * 100, 1)
            : 0;

        // Calcul du taux d'absentéisme du mois
        $workingDaysThisMonth = $this->getWorkingDaysInMonth(now()->month, now()->year);
        $expectedPresences = $totalEmployees * $workingDaysThisMonth;
        $actualPresences = Presence::whereMonth('date', now()->month)
            ->whereYear('date', now()->year)
            ->count();
        $absenteeismRate = $expectedPresences > 0
            ? round((($expectedPresences - $actualPresences) / $expectedPresences) * 100, 1)
            : 0;

        // Tendance des tâches (cette semaine vs semaine dernière)
        $tasksCompletedThisWeek = Task::where('statut', 'completed')
            ->whereBetween('updated_at', [now()->startOfWeek(), now()->endOfWeek()])
            ->count();
        $tasksCompletedLastWeek = Task::where('statut', 'completed')
            ->whereBetween('updated_at', [now()->subWeek()->startOfWeek(), now()->subWeek()->endOfWeek()])
            ->count();
        $tasksTrend = $tasksCompletedLastWeek > 0
            ? round((($tasksCompletedThisWeek - $tasksCompletedLastWeek) / $tasksCompletedLastWeek) * 100)
            : 0;

        // Tendance des présences (ce mois vs mois dernier)
        $presencesThisMonth = Presence::whereMonth('date', now()->month)
            ->whereYear('date', now()->year)
            ->count();
        $presencesLastMonth = Presence::whereMonth('date', now()->subMonth()->month)
            ->whereYear('date', now()->subMonth()->year)
            ->count();
        $presencesTrend = $presencesLastMonth > 0
            ? round((($presencesThisMonth - $presencesLastMonth) / $presencesLastMonth) * 100)
            : 0;

        return [
            'presence_rate' => $presenceRate,
            'absenteeism_rate' => $absenteeismRate,
            'tasks_completed_this_week' => $tasksCompletedThisWeek,
            'tasks_trend' => $tasksTrend,
            'presences_trend' => $presencesTrend,
            'avg_hours_today' => $this->getAverageHoursToday(),
        ];
    }

    private function getAlerts(): array
    {
        $alerts = [
            'late' => [],
            'overdue' => [],
            'pending' => [],
        ];

        // Retards du jour (arrivée après 9h)
        $lateThreshold = Carbon::today()->setTime(9, 0, 0);
        $latePresences = Presence::today()
            ->where('check_in', '>', $lateThreshold)
            ->with('user')
            ->get();

        foreach ($latePresences as $presence) {
            $lateMinutes = $presence->check_in->diffInMinutes($lateThreshold);
            $alerts['late'][] = [
                'id' => $presence->id,
                'name' => $presence->user->name,
                'initials' => $this->getInitials($presence->user->name),
                'time' => $presence->check_in->format('H:i'),
                'delay' => $this->formatDelay($lateMinutes),
                'link' => route('admin.presences.employee-show', $presence->user->id),
            ];
        }

        // Tâches en retard
        $overdueTasks = Task::whereIn('statut', ['pending', 'approved', 'in_progress'])
            ->where('date_fin', '<', now())
            ->with('user')
            ->take(10)
            ->get();

        foreach ($overdueTasks as $task) {
            $daysOverdue = (int) abs(now()->diffInDays($task->date_fin));
            $alerts['overdue'][] = [
                'id' => $task->id,
                'title' => $task->titre,
                'user' => $task->user->name,
                'daysOverdue' => $daysOverdue,
                'link' => route('admin.tasks.show', $task->id),
            ];
        }

        // Demandes en attente depuis plus de 48h
        $pendingThreshold = now()->subHours(48);

        // Congés en attente
        $pendingLeaves = Leave::pending()
            ->where('created_at', '<', $pendingThreshold)
            ->with('user')
            ->get();

        foreach ($pendingLeaves as $leave) {
            $waitingHours = now()->diffInHours($leave->created_at);
            $alerts['pending'][] = [
                'id' => 'leave_'.$leave->id,
                'type' => 'Demande de congé',
                'user' => $leave->user->name,
                'waitingTime' => $this->formatWaitingTime($waitingHours),
                'link' => route('admin.leaves.show', $leave->id),
                'approveUrl' => route('admin.leaves.approve', $leave->id),
            ];
        }

        // Tâches en attente
        $pendingTasks = Task::pending()
            ->where('created_at', '<', $pendingThreshold)
            ->with('user')
            ->get();

        foreach ($pendingTasks as $task) {
            $waitingHours = now()->diffInHours($task->created_at);
            $alerts['pending'][] = [
                'id' => 'task_'.$task->id,
                'type' => 'Tâche à valider',
                'user' => $task->user->name,
                'waitingTime' => $this->formatWaitingTime($waitingHours),
                'link' => route('admin.tasks.show', $task->id),
                'approveUrl' => route('admin.tasks.approve', $task->id),
            ];
        }

        return $alerts;
    }

    private function getRecentActivities(): array
    {
        $activities = [];

        // Pointages récents (exclure les entrées sans check_in)
        $recentPresences = Presence::with('user')
            ->whereNotNull('check_in')
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        foreach ($recentPresences as $presence) {
            if (! $presence->user || ! $presence->check_in) {
                continue;
            }
            $isCheckOut = $presence->check_out && $presence->check_out->isToday();
            $activities[] = [
                'id' => 'presence_'.$presence->id.($isCheckOut ? '_out' : '_in'),
                'type' => $isCheckOut ? 'check_out' : 'check_in',
                'user' => $presence->user->name,
                'avatar' => $presence->user->avatar ? avatar_url($presence->user->avatar) : null,
                'message' => $isCheckOut ? 'a quitté le bureau' : 'est arrivé(e) au bureau',
                'time' => $isCheckOut
                    ? $presence->check_out->diffForHumans()
                    : $presence->check_in->diffForHumans(),
                'timestamp' => $isCheckOut
                    ? $presence->check_out->timestamp
                    : $presence->check_in->timestamp,
            ];
        }

        // Tâches terminées récemment
        $recentCompletedTasks = Task::with('user')
            ->where('statut', 'completed')
            ->orderBy('updated_at', 'desc')
            ->take(5)
            ->get();

        foreach ($recentCompletedTasks as $task) {
            $activities[] = [
                'id' => 'task_'.$task->id,
                'type' => 'task_completed',
                'user' => $task->user->name,
                'avatar' => $task->user->avatar ? avatar_url($task->user->avatar) : null,
                'message' => 'a terminé la tâche "'.\Str::limit($task->titre, 30).'"',
                'time' => $task->updated_at->diffForHumans(),
                'timestamp' => $task->updated_at->timestamp,
                'badge' => 'success',
                'badgeText' => 'Terminé',
            ];
        }

        // Demandes de congés récentes
        $recentLeaves = Leave::with('user')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        foreach ($recentLeaves as $leave) {
            $activities[] = [
                'id' => 'leave_'.$leave->id,
                'type' => 'leave_requested',
                'user' => $leave->user->name,
                'avatar' => $leave->user->avatar ? avatar_url($leave->user->avatar) : null,
                'message' => 'a demandé un congé du '.$leave->date_debut->format('d/m').' au '.$leave->date_fin->format('d/m'),
                'time' => $leave->created_at->diffForHumans(),
                'timestamp' => $leave->created_at->timestamp,
                'badge' => $leave->statut === 'pending' ? 'warning' : ($leave->statut === 'approved' ? 'success' : 'danger'),
                'badgeText' => $leave->statut_label ?? ucfirst($leave->statut),
            ];
        }

        // Trier par timestamp et limiter
        usort($activities, fn ($a, $b) => $b['timestamp'] - $a['timestamp']);

        return array_slice($activities, 0, 15);
    }

    private function getCalendarEvents(?int $month = null, ?int $year = null): array
    {
        $month = $month ?? now()->month;
        $year = $year ?? now()->year;

        $events = [];

        // Congés approuvés
        $leaves = Leave::approved()
            ->where(function ($query) use ($month, $year) {
                $query->whereMonth('date_debut', $month)->whereYear('date_debut', $year)
                    ->orWhere(function ($q) use ($month, $year) {
                        $q->whereMonth('date_fin', $month)->whereYear('date_fin', $year);
                    });
            })
            ->with('user')
            ->get();

        foreach ($leaves as $leave) {
            $current = $leave->date_debut->copy();
            while ($current <= $leave->date_fin) {
                if ($current->month == $month && $current->year == $year) {
                    $events[] = [
                        'id' => 'leave_'.$leave->id.'_'.$current->format('Y-m-d'),
                        'date' => $current->format('Y-m-d'),
                        'type' => 'leave',
                        'title' => $leave->user->name.' - Congé',
                        'subtitle' => $leave->type_label ?? $leave->type,
                        'link' => route('admin.leaves.show', $leave->id),
                    ];
                }
                $current->addDay();
            }
        }

        // Deadlines de tâches
        $tasks = Task::whereMonth('date_fin', $month)
            ->whereYear('date_fin', $year)
            ->whereIn('statut', ['pending', 'approved', 'in_progress'])
            ->with('user')
            ->get();

        foreach ($tasks as $task) {
            $events[] = [
                'id' => 'task_'.$task->id,
                'date' => $task->date_fin->format('Y-m-d'),
                'type' => 'task',
                'title' => $task->titre,
                'subtitle' => $task->user->name,
                'link' => route('admin.tasks.show', $task->id),
            ];
        }

        // Anniversaires
        $employees = User::where('role', 'employee')
            ->whereMonth('date_of_birth', $month)
            ->get();

        foreach ($employees as $employee) {
            if ($employee->date_of_birth) {
                $events[] = [
                    'id' => 'birthday_'.$employee->id,
                    'date' => Carbon::create($year, $month, $employee->date_of_birth->day)->format('Y-m-d'),
                    'type' => 'birthday',
                    'title' => 'Anniversaire de '.$employee->name,
                    'subtitle' => 'Équipe '.($employee->department->name ?? 'N/A'),
                    'link' => route('admin.employees.show', $employee->id),
                ];
            }
        }

        return $events;
    }

    private function getDepartmentStats(): array
    {
        $departments = Department::withCount(['users' => function ($query) {
            $query->where('role', 'employee');
        }])->get();

        return $departments->map(function ($dept) {
            $presencesToday = Presence::today()
                ->whereHas('user', fn ($q) => $q->where('department_id', $dept->id))
                ->count();

            return [
                'id' => $dept->id,
                'name' => $dept->name,
                'employees' => $dept->users_count,
                'presences_today' => $presencesToday,
                'presence_rate' => $dept->users_count > 0
                    ? round(($presencesToday / $dept->users_count) * 100)
                    : 0,
                'color' => $dept->color ?? '#3B82F6',
            ];
        })->toArray();
    }

    private function getMonthlyPresenceData(): array
    {
        $data = [];
        $labels = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $labels[] = $date->translatedFormat('M Y');
            $data[] = Presence::month($date->month, $date->year)->count();
        }

        return [
            'labels' => $labels,
            'data' => $data,
        ];
    }

    private function getTaskStatusData(): array
    {
        return [
            'labels' => ['En attente', 'Approuvées', 'Rejetées', 'Terminées'],
            'data' => [
                Task::where('statut', 'pending')->count(),
                Task::where('statut', 'approved')->count(),
                Task::where('statut', 'rejected')->count(),
                Task::where('statut', 'completed')->count(),
            ],
            'colors' => ['#EAB308', '#3B82F6', '#EF4444', '#22C55E'],
        ];
    }

    private function getMonthlyLeaveData(): array
    {
        $data = [];
        $labels = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $labels[] = $date->translatedFormat('M Y');
            $data[] = Leave::whereMonth('date_debut', $date->month)
                ->whereYear('date_debut', $date->year)
                ->approved()
                ->count();
        }

        return [
            'labels' => $labels,
            'data' => $data,
        ];
    }

    private function getWorkingDaysInMonth(int $month, int $year): int
    {
        $start = Carbon::create($year, $month, 1);
        $end = $start->copy()->endOfMonth();
        $workingDays = 0;

        while ($start <= $end) {
            if (! $start->isWeekend()) {
                $workingDays++;
            }
            $start->addDay();
        }

        return $workingDays;
    }

    private function getAverageHoursToday(): float
    {
        $presences = Presence::today()
            ->whereNotNull('check_out')
            ->get();

        if ($presences->isEmpty()) {
            return 0;
        }

        $totalHours = $presences->sum(function ($presence) {
            return $presence->check_in->diffInMinutes($presence->check_out) / 60;
        });

        return round($totalHours / $presences->count(), 1);
    }

    private function getInitials(string $name): string
    {
        $parts = explode(' ', $name);
        $initials = '';
        foreach (array_slice($parts, 0, 2) as $part) {
            $initials .= strtoupper(substr($part, 0, 1));
        }

        return $initials;
    }

    private function formatDelay(int $minutes): string
    {
        if ($minutes < 60) {
            return $minutes.' min';
        }
        $hours = floor($minutes / 60);
        $mins = $minutes % 60;

        return $hours.'h'.($mins > 0 ? sprintf('%02d', $mins) : '');
    }

    private function formatWaitingTime(int $hours): string
    {
        if ($hours < 24) {
            return $hours.' heures';
        }
        $days = floor($hours / 24);

        return $days.' jour'.($days > 1 ? 's' : '');
    }

    public function markNotificationAsRead($id)
    {
        $notification = auth()->user()->notifications()->findOrFail($id);
        $notification->markAsRead();

        return redirect()->back();
    }

    public function markAllNotificationsAsRead()
    {
        auth()->user()->unreadNotifications->markAsRead();

        return redirect()->back()->with('success', 'Toutes les notifications ont été marquées comme lues.');
    }

    /**
     * API: Retourne le nombre de notifications non lues (pour polling)
     */
    public function getUnreadNotificationsCount()
    {
        return response()->json([
            'count' => auth()->user()->unreadNotifications()->count(),
        ]);
    }
}
