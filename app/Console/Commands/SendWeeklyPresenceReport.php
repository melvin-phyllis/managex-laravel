<?php

namespace App\Console\Commands;

use App\Models\EmployeeWorkDay;
use App\Models\Leave;
use App\Models\Presence;
use App\Models\Setting;
use App\Models\Task;
use App\Models\User;
use App\Notifications\WeeklyPresenceReportNotification;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;

class SendWeeklyPresenceReport extends Command
{
    protected $signature = 'report:weekly';

    protected $description = 'Envoie le rapport hebdomadaire de présence aux administrateurs (Lundi → Vendredi)';

    public function handle(): int
    {
        $today = Carbon::today();

        // Déterminer la semaine en cours (lundi → vendredi)
        $weekStart = $today->copy()->startOfWeek(Carbon::MONDAY);
        $weekEnd = $weekStart->copy()->addDays(4); // Vendredi

        // Générer les jours ouvrables (Lun → Ven)
        $workDays = collect(CarbonPeriod::create($weekStart, $weekEnd))
            ->map(fn (Carbon $day) => $day->copy());

        $dayLabels = $workDays->map(fn (Carbon $d) => $d->translatedFormat('D d/m'));

        // Employés actifs
        $activeEmployees = User::where('role', 'employee')
            ->where('status', 'active')
            ->with('department')
            ->orderBy('name')
            ->get();

        $activeIds = $activeEmployees->pluck('id');

        // Présences de la semaine
        $presences = Presence::whereBetween('date', [$weekStart->toDateString(), $weekEnd->toDateString()])
            ->whereIn('user_id', $activeIds)
            ->get()
            ->groupBy('user_id');

        // Congés approuvés qui chevauchent la semaine
        $leaves = Leave::where('statut', 'approved')
            ->where('date_debut', '<=', $weekEnd->toDateString())
            ->where('date_fin', '>=', $weekStart->toDateString())
            ->whereIn('user_id', $activeIds)
            ->get()
            ->groupBy('user_id');

        // Jours de travail planifiés par employé
        $workDaysByUser = EmployeeWorkDay::whereIn('user_id', $activeIds)
            ->get()
            ->groupBy('user_id');

        // Construire le rapport par employé
        $employeeRows = [];
        $totalPresent = 0;
        $totalAbsent = 0;
        $totalLeave = 0;
        $totalLateMinutes = 0;

        foreach ($activeEmployees as $emp) {
            $empPresences = $presences->get($emp->id, collect());
            $empLeaves = $leaves->get($emp->id, collect());
            $empWorkDays = $workDaysByUser->get($emp->id, collect())
                ->pluck('day_of_week')
                ->toArray();

            $days = [];
            $empPresentsCount = 0;
            $empAbsentsCount = 0;
            $empLeaveCount = 0;
            $empLateMinutes = 0;

            foreach ($workDays as $day) {
                $dayOfWeek = (int) $day->isoFormat('E'); // 1=Lun ... 5=Ven
                $dateStr = $day->toDateString();

                // Si ce jour n'est pas dans le planning de l'employé
                if (! in_array($dayOfWeek, $empWorkDays)) {
                    $days[] = ['status' => 'off', 'icon' => '⬜', 'detail' => 'Repos'];
                    continue;
                }

                // Vérifier si en congé ce jour
                $onLeave = $empLeaves->first(function ($leave) use ($dateStr) {
                    return Carbon::parse($leave->date_debut)->lte($dateStr)
                        && Carbon::parse($leave->date_fin)->gte($dateStr);
                });

                if ($onLeave) {
                    $days[] = ['status' => 'leave', 'icon' => '🏖️', 'detail' => 'Congé ' . $onLeave->type_label];
                    $empLeaveCount++;
                    $totalLeave++;
                    continue;
                }

                // Vérifier la présence
                $presence = $empPresences->first(fn ($p) => $p->date === $dateStr || Carbon::parse($p->date)->toDateString() === $dateStr);

                // Si le jour est dans le futur (pas encore passé), marquer comme « à venir »
                if ($day->isFuture()) {
                    $days[] = ['status' => 'future', 'icon' => '⏳', 'detail' => 'À venir'];
                    continue;
                }

                if ($presence && $presence->check_in) {
                    $lateInfo = '';
                    if ($presence->is_late && $presence->late_minutes > 0) {
                        $lateInfo = " (Retard {$presence->late_minutes}m)";
                        $empLateMinutes += $presence->late_minutes;
                        $totalLateMinutes += $presence->late_minutes;
                    }
                    $checkIn = Carbon::parse($presence->check_in)->format('H:i');
                    $checkOut = $presence->check_out ? Carbon::parse($presence->check_out)->format('H:i') : 'En cours';
                    $days[] = [
                        'status' => 'present',
                        'icon' => $presence->is_late ? '⚠️' : '✅',
                        'detail' => "{$checkIn}→{$checkOut}{$lateInfo}",
                    ];
                    $empPresentsCount++;
                    $totalPresent++;
                } else {
                    $days[] = ['status' => 'absent', 'icon' => '❌', 'detail' => 'Absent'];
                    $empAbsentsCount++;
                    $totalAbsent++;
                }
            }

            $employeeRows[] = [
                'name' => $emp->name,
                'department' => $emp->department->name ?? '—',
                'days' => $days,
                'total_present' => $empPresentsCount,
                'total_absent' => $empAbsentsCount,
                'total_leave' => $empLeaveCount,
                'late_minutes' => $empLateMinutes,
            ];
        }

        // Tâches complétées durant la semaine
        $completedTasks = Task::where('statut', 'completed')
            ->whereBetween('updated_at', [$weekStart, $weekEnd->copy()->endOfDay()])
            ->with('user')
            ->get()
            ->map(fn ($t) => [
                'title' => $t->titre,
                'employee' => $t->user->name ?? 'Inconnu',
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

        // Tâches en attente de validation
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

        $data = [
            'week_label' => $weekStart->translatedFormat('d F') . ' — ' . $weekEnd->translatedFormat('d F Y'),
            'week_start' => $weekStart->toDateString(),
            'week_end' => $weekEnd->toDateString(),
            'day_labels' => $dayLabels->toArray(),
            'employee_rows' => $employeeRows,
            'total_employees' => $activeEmployees->count(),
            'total_present' => $totalPresent,
            'total_absent' => $totalAbsent,
            'total_leave' => $totalLeave,
            'total_late_minutes' => $totalLateMinutes,
            'completed_tasks' => $completedTasks,
            'pending_leaves' => $pendingLeaves,
            'pending_tasks' => $pendingTasks,
        ];

        // Envoyer le rapport
        $reportEmail = Setting::get('report_email');

        if ($reportEmail) {
            Notification::route('mail', $reportEmail)
                ->notify(new WeeklyPresenceReportNotification($data));

            $this->info("Rapport hebdomadaire envoyé à {$reportEmail}.");
        } else {
            $admins = User::where('role', 'admin')->get();

            if ($admins->isEmpty()) {
                $this->warn('Aucun administrateur trouvé et aucun email de rapport configuré.');
                return self::FAILURE;
            }

            foreach ($admins as $admin) {
                $admin->notify(new WeeklyPresenceReportNotification($data));
            }

            $this->info("Rapport hebdomadaire envoyé à {$admins->count()} administrateur(s).");
        }

        return self::SUCCESS;
    }
}
