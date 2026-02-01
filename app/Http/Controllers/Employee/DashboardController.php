<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Stats de base
        $stats = $this->getBaseStats($user);

        // Données pour les graphiques
        $chartData = $this->getChartData($user);

        // Streak de présence
        $streakData = $this->calculateStreak($user);

        // Événements à venir
        $upcomingEvents = $this->getUpcomingEvents($user);

        // Objectifs mensuels
        $monthlyGoals = $this->getMonthlyGoals($user);

        $todayPresence = $user->todayPresence();
        $recentTasks = $user->tasks()->latest()->take(5)->get();
        $recentLeaves = $user->leaves()->latest()->take(3)->get();
        $recentNotifications = $user->unreadNotifications()->take(5)->get();

        // Sondages actifs non répondus
        $pendingSurveys = \App\Models\Survey::active()
            ->whereDoesntHave('questions.responses', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->take(3)
            ->get();

        // Annonces urgentes/critiques non lues pour le dashboard
        $urgentAnnouncements = Announcement::published()
            ->forUser($user)
            ->where(function ($q) {
                $q->where('priority', 'critical')
                  ->orWhere('type', 'urgent');
            })
            ->unreadBy($user)
            ->orderByPriority()
            ->take(3)
            ->get();

        // Compteur d'annonces non lues
        $unreadAnnouncementsCount = Announcement::published()
            ->forUser($user)
            ->unreadBy($user)
            ->count();

        // === NOUVEAUX WIDGETS ===
        
        // Demandes de documents (les 3 dernières)
        $documentRequests = \App\Models\DocumentRequest::forUser($user->id)
            ->latest()
            ->take(3)
            ->get();
        
        // Documents globaux non lus
        $acknowledgedIds = \DB::table('global_document_acknowledgments')
            ->where('user_id', $user->id)
            ->pluck('global_document_id')
            ->toArray();
        $unreadGlobalDocs = \App\Models\GlobalDocument::active()
            ->whereNotIn('id', $acknowledgedIds)
            ->get();
        
        // Contrat de travail
        $contract = $user->currentContract;
        $hasContractDocument = $contract && $contract->document_path;

        return view('employee.dashboard', compact(
            'stats',
            'chartData',
            'streakData',
            'upcomingEvents',
            'monthlyGoals',
            'todayPresence',
            'recentTasks',
            'recentLeaves',
            'recentNotifications',
            'pendingSurveys',
            'urgentAnnouncements',
            'unreadAnnouncementsCount',
            'documentRequests',
            'unreadGlobalDocs',
            'contract',
            'hasContractDocument'
        ));
    }


    /**
     * API endpoint for chart data
     */
    public function getChartDataApi()
    {
        $user = auth()->user();
        return response()->json($this->getChartData($user));
    }

    /**
     * API endpoint for upcoming events
     */
    public function getUpcomingEventsApi()
    {
        $user = auth()->user();
        return response()->json($this->getUpcomingEvents($user));
    }

    private function getBaseStats($user): array
    {
        // Compter les présences du mois en cours
        $presencesMonth = $user->presences()
            ->whereYear('date', now()->year)
            ->whereMonth('date', now()->month)
            ->count();

        // Calculer les heures travaillées ce mois
        $heuresMonth = $user->presences()
            ->whereYear('date', now()->year)
            ->whereMonth('date', now()->month)
            ->whereNotNull('check_out')
            ->get()
            ->sum(function ($presence) {
                return $presence->check_in && $presence->check_out
                    ? $presence->check_in->diffInMinutes($presence->check_out) / 60
                    : 0;
            });

        // Calculer les jours de congé utilisés
        $usedLeaveDays = $user->leaves()
            ->where('statut', 'approved')
            ->whereYear('date_debut', now()->year)
            ->get()
            ->sum(function ($leave) {
                return $leave->date_debut->diffInDays($leave->date_fin) + 1;
            });

        // Sondages actifs non répondus
        $pendingSurveysCount = \App\Models\Survey::active()
            ->whereDoesntHave('questions.responses', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->count();

        return [
            'presences_month' => $presencesMonth,
            'heures_month' => round($heuresMonth, 1),
            'active_tasks' => $user->tasks()->whereIn('statut', ['pending', 'approved', 'in_progress'])->count(),
            'tasks_pending' => $user->tasks()->pending()->count(),
            'tasks_completed' => $user->tasks()->completed()->count(),
            'tasks_in_progress' => $user->tasks()->where('statut', 'in_progress')->count(),
            'leaves_pending' => $user->leaves()->pending()->count(),
            'leaves_approved' => $user->leaves()->approved()->count(),
            'leave_days_remaining' => max(0, ($user->leave_balance ?? 25) - $usedLeaveDays),
            'leave_days_used' => $usedLeaveDays,
            'pending_surveys' => $pendingSurveysCount,
        ];
    }

    private function getChartData($user): array
    {
        // Heures travaillées par jour cette semaine
        $weeklyHours = [];
        $weeklyLabels = [];
        $startOfWeek = now()->startOfWeek();

        for ($i = 0; $i < 5; $i++) { // Lundi à Vendredi
            $date = $startOfWeek->copy()->addDays($i);
            $weeklyLabels[] = $date->translatedFormat('D');

            $presence = $user->presences()
                ->whereDate('date', $date)
                ->whereNotNull('check_out')
                ->first();

            if ($presence) {
                $hours = $presence->check_in->diffInMinutes($presence->check_out) / 60;
                $weeklyHours[] = round($hours, 1);
            } else {
                $weeklyHours[] = 0;
            }
        }

        // Tendance de présence mensuelle (30 derniers jours)
        $monthlyPresence = [];
        $monthlyLabels = [];
        for ($i = 29; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $monthlyLabels[] = $date->format('d/m');
            $hasPresence = $user->presences()->whereDate('date', $date)->exists();
            $monthlyPresence[] = $hasPresence ? 1 : 0;
        }

        // Répartition des tâches par statut
        $tasksByStatus = [
            'labels' => ['En attente', 'En cours', 'Terminées', 'Validées'],
            'data' => [
                $user->tasks()->where('statut', 'pending')->count(),
                $user->tasks()->whereIn('statut', ['approved', 'in_progress'])->count(),
                $user->tasks()->where('statut', 'completed')->count(),
                $user->tasks()->where('statut', 'validated')->count(),
            ],
            'colors' => ['#EAB308', '#3B82F6', '#F97316', '#22C55E'],
        ];

        return [
            'weekly_hours' => [
                'labels' => $weeklyLabels,
                'data' => $weeklyHours,
            ],
            'monthly_presence' => [
                'labels' => $monthlyLabels,
                'data' => $monthlyPresence,
            ],
            'tasks_by_status' => $tasksByStatus,
        ];
    }

    private function calculateStreak($user): array
    {
        $streak = 0;
        $bestStreak = 0;
        $currentStreak = 0;
        $lastPresenceDate = null;

        // Récupérer toutes les présences triées par date
        $presences = $user->presences()
            ->orderBy('date', 'desc')
            ->pluck('date')
            ->map(fn($date) => Carbon::parse($date)->format('Y-m-d'))
            ->unique()
            ->values();

        if ($presences->isEmpty()) {
            return [
                'current' => 0,
                'best' => 0,
                'last_date' => null,
            ];
        }

        $lastPresenceDate = $presences->first();

        // Calculer la série actuelle
        $checkDate = now()->format('Y-m-d');

        // Si pas de présence aujourd'hui, vérifier hier
        if (!$presences->contains($checkDate)) {
            $checkDate = now()->subDay()->format('Y-m-d');
        }

        // Compter les jours consécutifs (en ignorant les weekends)
        while (true) {
            $carbonDate = Carbon::parse($checkDate);

            // Ignorer les weekends
            if ($carbonDate->isWeekend()) {
                $checkDate = $carbonDate->subDay()->format('Y-m-d');
                continue;
            }

            if ($presences->contains($checkDate)) {
                $currentStreak++;
                $checkDate = $carbonDate->subDay()->format('Y-m-d');
            } else {
                break;
            }

            // Limite de sécurité
            if ($currentStreak > 365) break;
        }

        // Calculer la meilleure série (simplifié)
        $bestStreak = max($currentStreak, $this->calculateBestStreak($presences));

        return [
            'current' => $currentStreak,
            'best' => $bestStreak,
            'last_date' => $lastPresenceDate,
        ];
    }

    private function calculateBestStreak($presences): int
    {
        if ($presences->isEmpty()) return 0;

        $bestStreak = 0;
        $currentStreak = 0;
        $dates = $presences->sort()->values();

        for ($i = 0; $i < count($dates); $i++) {
            if ($i === 0) {
                $currentStreak = 1;
            } else {
                $prevDate = Carbon::parse($dates[$i - 1]);
                $currDate = Carbon::parse($dates[$i]);

                // Calculer la différence en jours ouvrés
                $diff = 0;
                $checkDate = $prevDate->copy()->addDay();
                while ($checkDate < $currDate) {
                    if (!$checkDate->isWeekend()) {
                        $diff++;
                    }
                    $checkDate->addDay();
                }

                if ($diff === 0 || ($diff === 1 && $prevDate->isFriday() && $currDate->isMonday())) {
                    $currentStreak++;
                } else {
                    $bestStreak = max($bestStreak, $currentStreak);
                    $currentStreak = 1;
                }
            }
        }

        return max($bestStreak, $currentStreak);
    }

    private function getUpcomingEvents($user): array
    {
        $events = [];

        // Congés approuvés à venir
        $upcomingLeaves = $user->leaves()
            ->where('statut', 'approved')
            ->where('date_debut', '>', now())
            ->orderBy('date_debut')
            ->take(5)
            ->get();

        foreach ($upcomingLeaves as $leave) {
            $events[] = [
                'id' => 'leave_' . $leave->id,
                'type' => 'leave',
                'title' => 'Congé ' . ($leave->type_label ?? $leave->type),
                'subtitle' => 'Du ' . $leave->date_debut->format('d/m') . ' au ' . $leave->date_fin->format('d/m'),
                'date' => $leave->date_debut->format('Y-m-d'),
                'link' => route('employee.leaves.show', $leave->id),
            ];
        }

        // Deadlines de tâches
        $upcomingTasks = $user->tasks()
            ->whereIn('statut', ['pending', 'approved', 'in_progress'])
            ->where('date_fin', '>', now())
            ->orderBy('date_fin')
            ->take(5)
            ->get();

        foreach ($upcomingTasks as $task) {
            $events[] = [
                'id' => 'task_' . $task->id,
                'type' => 'task',
                'title' => $task->titre,
                'subtitle' => 'Progression: ' . $task->progression . '%',
                'date' => $task->date_fin->format('Y-m-d'),
                'link' => route('employee.tasks.show', $task->id),
            ];
        }

        // Sondages avec date limite
        $upcomingSurveys = \App\Models\Survey::active()
            ->whereNotNull('date_limite')
            ->where('date_limite', '>', now())
            ->whereDoesntHave('questions.responses', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->orderBy('date_limite')
            ->take(3)
            ->get();

        foreach ($upcomingSurveys as $survey) {
            $events[] = [
                'id' => 'survey_' . $survey->id,
                'type' => 'survey',
                'title' => $survey->titre,
                'subtitle' => $survey->questions()->count() . ' questions',
                'date' => $survey->date_limite->format('Y-m-d'),
                'link' => route('employee.surveys.show', $survey->id),
            ];
        }

        // Trier par date
        usort($events, fn($a, $b) => strcmp($a['date'], $b['date']));

        return array_slice($events, 0, 8);
    }

    private function getMonthlyGoals($user): array
    {
        // Jours ouvrés du mois
        $workingDays = $this->getWorkingDaysInMonth(now()->month, now()->year);
        $workingDaysSoFar = $this->getWorkingDaysSoFar();

        // Présences du mois
        $presencesMonth = $user->presences()
            ->whereYear('date', now()->year)
            ->whereMonth('date', now()->month)
            ->count();

        // Heures travaillées
        $hoursMonth = $user->presences()
            ->whereYear('date', now()->year)
            ->whereMonth('date', now()->month)
            ->whereNotNull('check_out')
            ->get()
            ->sum(function ($presence) {
                return $presence->check_in && $presence->check_out
                    ? $presence->check_in->diffInMinutes($presence->check_out) / 60
                    : 0;
            });

        $expectedHours = $workingDaysSoFar * 8; // 8h par jour
        $targetHours = $workingDays * 8;

        // Tâches du mois
        $tasksAssigned = $user->tasks()
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        $tasksCompleted = $user->tasks()
            ->whereIn('statut', ['completed', 'validated'])
            ->whereMonth('updated_at', now()->month)
            ->whereYear('updated_at', now()->year)
            ->count();

        return [
            'presence' => [
                'current' => $presencesMonth,
                'target' => $workingDaysSoFar,
                'total' => $workingDays,
                'percentage' => $workingDaysSoFar > 0 ? round(($presencesMonth / $workingDaysSoFar) * 100) : 0,
            ],
            'hours' => [
                'current' => round($hoursMonth, 1),
                'target' => $targetHours,
                'expected' => $expectedHours,
                'percentage' => $expectedHours > 0 ? min(100, round(($hoursMonth / $expectedHours) * 100)) : 0,
            ],
            'tasks' => [
                'completed' => $tasksCompleted,
                'assigned' => $tasksAssigned,
                'percentage' => $tasksAssigned > 0 ? round(($tasksCompleted / $tasksAssigned) * 100) : 100,
            ],
        ];
    }

    private function getWorkingDaysInMonth(int $month, int $year): int
    {
        $start = Carbon::create($year, $month, 1);
        $end = $start->copy()->endOfMonth();
        $workingDays = 0;

        while ($start <= $end) {
            if (!$start->isWeekend()) {
                $workingDays++;
            }
            $start->addDay();
        }

        return $workingDays;
    }

    private function getWorkingDaysSoFar(): int
    {
        $start = now()->startOfMonth();
        $end = now();
        $workingDays = 0;

        while ($start <= $end) {
            if (!$start->isWeekend()) {
                $workingDays++;
            }
            $start->addDay();
        }

        return $workingDays;
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
            'count' => auth()->user()->unreadNotifications()->count()
        ]);
    }
}
