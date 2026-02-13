<?php

namespace App\Console\Commands;

use App\Models\EmployeeInvitation;
use App\Models\EmployeeWorkDay;
use App\Models\InternEvaluation;
use App\Models\Leave;
use App\Models\Presence;
use App\Models\Setting;
use App\Models\Task;
use App\Models\User;
use App\Notifications\DailyReportNotification;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;

class SendDailyReport extends Command
{
    protected $signature = 'report:daily';

    protected $description = 'Envoie le compte rendu quotidien aux administrateurs';

    public function handle(): int
    {
        $today = Carbon::today();
        $todayStr = $today->toDateString();

        $activeEmployees = User::where('role', 'employee')
            ->where('status', 'active')
            ->with('department')
            ->get();

        $activeIds = $activeEmployees->pluck('id');

        // Présences du jour
        $presences = Presence::where('date', $todayStr)
            ->whereIn('user_id', $activeIds)
            ->with('user')
            ->get()
            ->keyBy('user_id');

        // Jours de travail prévus aujourd'hui
        $todayDayOfWeek = (int) $today->isoFormat('E');
        $employeesScheduledToday = EmployeeWorkDay::where('day_of_week', $todayDayOfWeek)
            ->whereIn('user_id', $activeIds)
            ->pluck('user_id')
            ->toArray();

        // Congés actifs aujourd'hui
        $activeLeaves = Leave::where('statut', 'approved')
            ->where('date_debut', '<=', $todayStr)
            ->where('date_fin', '>=', $todayStr)
            ->whereIn('user_id', $activeIds)
            ->with('user')
            ->get()
            ->keyBy('user_id');

        // Construire les listes
        $presents = [];
        $absents = [];
        $totalLateMinutes = 0;
        $lateDetails = [];

        foreach ($activeEmployees as $emp) {
            $isScheduled = in_array($emp->id, $employeesScheduledToday);
            $onLeave = $activeLeaves->has($emp->id);
            $presence = $presences->get($emp->id);

            if ($onLeave) {
                $leave = $activeLeaves->get($emp->id);
                $absents[] = [
                    'name' => $emp->name,
                    'reason' => 'Congé ' . $leave->type_label . ' (jusqu\'au ' . Carbon::parse($leave->date_fin)->format('d/m') . ')',
                ];
                continue;
            }

            if (! $isScheduled) {
                continue; // Pas prévu aujourd'hui, on ignore
            }

            if ($presence) {
                $checkIn = $presence->check_in ? Carbon::parse($presence->check_in)->format('H:i') : '?';
                $checkOut = $presence->check_out ? Carbon::parse($presence->check_out)->format('H:i') : 'En cours';
                $lateInfo = '';

                if ($presence->is_late && $presence->late_minutes > 0) {
                    $lateInfo = " (Retard: {$presence->late_minutes} min)";
                    $totalLateMinutes += $presence->late_minutes;
                    $lateDetails[] = [
                        'name' => $emp->name,
                        'minutes' => $presence->late_minutes,
                    ];
                }

                $presents[] = [
                    'name' => $emp->name,
                    'check_in' => $checkIn,
                    'check_out' => $checkOut,
                    'late_info' => $lateInfo,
                ];
            } else {
                $absents[] = [
                    'name' => $emp->name,
                    'reason' => 'Absent sans justification',
                ];
            }
        }

        // Tâches terminées aujourd'hui
        $completedTasks = Task::where('statut', 'completed')
            ->whereDate('updated_at', $todayStr)
            ->with('user')
            ->get()
            ->map(fn ($t) => [
                'title' => $t->titre,
                'employee' => $t->user->name ?? 'Inconnu',
            ]);

        // Nouvelles demandes de congés aujourd'hui
        $newLeaveRequests = Leave::whereDate('created_at', $todayStr)
            ->with('user')
            ->get()
            ->map(fn ($l) => [
                'employee' => $l->user->name ?? 'Inconnu',
                'type' => $l->type_label,
                'dates' => Carbon::parse($l->date_debut)->format('d/m') . ' - ' . Carbon::parse($l->date_fin)->format('d/m'),
                'status' => $l->statut_label,
            ]);

        // Congés en attente
        $pendingLeaves = Leave::where('statut', 'pending')
            ->with('user')
            ->get()
            ->map(fn ($l) => [
                'employee' => $l->user->name ?? 'Inconnu',
                'type' => $l->type_label,
                'dates' => Carbon::parse($l->date_debut)->format('d/m') . ' - ' . Carbon::parse($l->date_fin)->format('d/m'),
            ]);

        // Nouvelles invitations envoyées aujourd'hui
        $newInvitations = EmployeeInvitation::whereDate('created_at', $todayStr)->get();

        // Nouveaux employés (comptes créés aujourd'hui)
        $newEmployees = User::where('role', 'employee')
            ->whereDate('created_at', $todayStr)
            ->get();

        // Tâches en attente de validation (soumises par les employés)
        $pendingTasks = Task::where('statut', 'pending')
            ->with('user')
            ->orderByDesc('created_at')
            ->get()
            ->map(fn ($t) => [
                'title' => $t->titre,
                'employee' => $t->user->name ?? 'Inconnu',
                'priority' => $t->priorite_label,
                'deadline' => $t->date_fin ? Carbon::parse($t->date_fin)->format('d/m/Y') : 'Non définie',
            ]);

        // Tâches terminées en attente de validation finale
        $tasksAwaitingValidation = Task::where('statut', 'completed')
            ->with('user')
            ->orderByDesc('updated_at')
            ->get()
            ->map(fn ($t) => [
                'title' => $t->titre,
                'employee' => $t->user->name ?? 'Inconnu',
                'completed_at' => Carbon::parse($t->updated_at)->format('d/m/Y'),
            ]);

        // Évaluations de stagiaires en attente (draft = non soumises)
        $weekStart = $today->copy()->startOfWeek();
        $internIds = User::where('status', 'active')
            ->where('contract_type', 'stage')
            ->pluck('id');

        $pendingInternEvals = [];
        if ($internIds->isNotEmpty()) {
            // Stagiaires sans évaluation cette semaine
            $evaluatedThisWeek = InternEvaluation::whereIn('intern_id', $internIds)
                ->where('week_start', $weekStart)
                ->pluck('intern_id')
                ->unique();

            $internsWithoutEval = User::whereIn('id', $internIds->diff($evaluatedThisWeek))
                ->with('supervisor')
                ->get()
                ->map(fn ($i) => [
                    'intern' => $i->name,
                    'tutor' => $i->supervisor->name ?? 'Non assigné',
                    'status' => 'Non évalué cette semaine',
                ]);

            // Évaluations en brouillon (draft)
            $draftEvals = InternEvaluation::where('status', 'draft')
                ->with(['intern', 'tutor'])
                ->orderByDesc('week_start')
                ->get()
                ->map(fn ($e) => [
                    'intern' => $e->intern->name ?? 'Inconnu',
                    'tutor' => $e->tutor->name ?? 'Inconnu',
                    'status' => 'Brouillon (sem. ' . $e->week_start->format('d/m') . ')',
                ]);

            $pendingInternEvals = $internsWithoutEval->merge($draftEvals);
        }

        $data = [
            'date' => $today->translatedFormat('l d F Y'),
            'presents' => $presents,
            'absents' => $absents,
            'total_scheduled' => count($employeesScheduledToday),
            'total_presents' => count($presents),
            'total_absents' => count($absents),
            'total_late' => count($lateDetails),
            'total_late_minutes' => $totalLateMinutes,
            'late_details' => $lateDetails,
            'completed_tasks' => $completedTasks,
            'new_leave_requests' => $newLeaveRequests,
            'pending_leaves' => $pendingLeaves,
            'new_invitations' => $newInvitations,
            'new_employees' => $newEmployees,
            'pending_tasks' => $pendingTasks,
            'tasks_awaiting_validation' => $tasksAwaitingValidation,
            'pending_intern_evals' => $pendingInternEvals,
        ];

        // Déterminer le destinataire
        $reportEmail = Setting::get('report_email');

        if ($reportEmail) {
            // Envoyer à l'email configuré
            Notification::route('mail', $reportEmail)
                ->notify(new DailyReportNotification($data));

            $this->info("Rapport quotidien envoyé à {$reportEmail}.");
        } else {
            // Fallback : envoyer aux admins directement
            $admins = User::where('role', 'admin')->get();

            if ($admins->isEmpty()) {
                $this->warn('Aucun administrateur trouvé et aucun email de rapport configuré.');
                return self::FAILURE;
            }

            foreach ($admins as $admin) {
                $admin->notify(new DailyReportNotification($data));
            }

            $this->info("Rapport quotidien envoyé à {$admins->count()} administrateur(s) (email de rapport non configuré).");
        }

        return self::SUCCESS;
    }
}
