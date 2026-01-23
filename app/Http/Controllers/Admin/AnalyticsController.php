<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Leave;
use App\Models\Presence;
use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    /**
     * Afficher le tableau de bord analytique
     */
    public function index()
    {
        $departments = Department::active()->orderBy('name')->get();

        return view('admin.analytics.index', compact('departments'));
    }

    /**
     * Récupérer les données analytiques (API JSON)
     */
    public function getData(Request $request)
    {
        $period = $request->get('period', 'month'); // week, month, quarter, year
        $departmentId = $request->get('department_id');

        $startDate = $this->getStartDate($period);
        $endDate = now();

        // Statistiques de présence
        $presenceStats = $this->getPresenceStats($startDate, $endDate, $departmentId);

        // Statistiques des tâches
        $taskStats = $this->getTaskStats($startDate, $endDate, $departmentId);

        // Statistiques des congés
        $leaveStats = $this->getLeaveStats($startDate, $endDate, $departmentId);

        // Tendances de présence par jour
        $presenceTrend = $this->getPresenceTrend($startDate, $endDate, $departmentId);

        // Répartition par département
        $departmentDistribution = $this->getDepartmentDistribution();

        // Top employés (présence)
        $topEmployees = $this->getTopEmployees($startDate, $endDate, $departmentId);

        // Heures moyennes de pointage
        $averageCheckTimes = $this->getAverageCheckTimes($startDate, $endDate, $departmentId);

        // Statistiques géolocalisation
        $geolocationStats = $this->getGeolocationStats($startDate, $endDate);

        return response()->json([
            'presence' => $presenceStats,
            'tasks' => $taskStats,
            'leaves' => $leaveStats,
            'presenceTrend' => $presenceTrend,
            'departmentDistribution' => $departmentDistribution,
            'topEmployees' => $topEmployees,
            'averageCheckTimes' => $averageCheckTimes,
            'geolocation' => $geolocationStats,
        ]);
    }

    private function getStartDate(string $period): Carbon
    {
        return match($period) {
            'week' => now()->subWeek(),
            'month' => now()->subMonth(),
            'quarter' => now()->subQuarter(),
            'year' => now()->subYear(),
            default => now()->subMonth(),
        };
    }

    private function getPresenceStats(Carbon $startDate, Carbon $endDate, ?int $departmentId): array
    {
        $query = Presence::whereBetween('date', [$startDate, $endDate]);

        if ($departmentId) {
            $query->whereHas('user', fn($q) => $q->where('department_id', $departmentId));
        }

        $totalPresences = $query->count();

        // Calculer les heures moyennes à partir de check_in et check_out
        $presencesWithCheckOut = (clone $query)->whereNotNull('check_out')->get();
        $avgHoursPerDay = 0;
        if ($presencesWithCheckOut->count() > 0) {
            $totalHours = $presencesWithCheckOut->sum(function ($p) {
                $checkIn = Carbon::parse($p->check_in);
                $checkOut = Carbon::parse($p->check_out);
                // S'assurer que check_out est après check_in
                if ($checkOut->gt($checkIn)) {
                    return $checkOut->diffInMinutes($checkIn) / 60;
                }
                return 0;
            });
            $avgHoursPerDay = $totalHours / $presencesWithCheckOut->count();
        }

        // Taux de présence (présences / jours ouvrés * employés)
        $workingDays = $this->countWorkingDays($startDate, $endDate);
        $employeeCount = User::where('role', 'employee')
            ->when($departmentId, fn($q) => $q->where('department_id', $departmentId))
            ->count();

        $expectedPresences = $workingDays * $employeeCount;
        $presenceRate = $expectedPresences > 0 ? round(($totalPresences / $expectedPresences) * 100, 1) : 0;

        return [
            'total' => $totalPresences,
            'avgHoursPerDay' => round($avgHoursPerDay, 2),
            'presenceRate' => $presenceRate,
            'workingDays' => $workingDays,
        ];
    }

    private function getTaskStats(Carbon $startDate, Carbon $endDate, ?int $departmentId): array
    {
        $query = Task::whereBetween('created_at', [$startDate, $endDate]);

        if ($departmentId) {
            $query->whereHas('user', fn($q) => $q->where('department_id', $departmentId));
        }

        $total = $query->count();
        $completed = (clone $query)->where('statut', 'completed')->count();
        $validated = (clone $query)->where('statut', 'approved')->count();
        $inProgress = (clone $query)->whereIn('statut', ['pending', 'approved'])->count();
        $pending = (clone $query)->where('statut', 'pending')->count();

        $completionRate = $total > 0 ? round(($completed / $total) * 100, 1) : 0;

        return [
            'total' => $total,
            'completed' => $completed,
            'validated' => $validated,
            'inProgress' => $inProgress,
            'pending' => $pending,
            'completionRate' => $completionRate,
        ];
    }

    private function getLeaveStats(Carbon $startDate, Carbon $endDate, ?int $departmentId): array
    {
        $query = Leave::whereBetween('created_at', [$startDate, $endDate]);

        if ($departmentId) {
            $query->whereHas('user', fn($q) => $q->where('department_id', $departmentId));
        }

        $total = $query->count();
        $approved = (clone $query)->where('statut', 'approved')->count();
        $rejected = (clone $query)->where('statut', 'rejected')->count();
        $pending = (clone $query)->where('statut', 'pending')->count();

        // Répartition par type
        $byType = Leave::whereBetween('created_at', [$startDate, $endDate])
            ->when($departmentId, fn($q) => $q->whereHas('user', fn($q2) => $q2->where('department_id', $departmentId)))
            ->select('type', DB::raw('count(*) as count'))
            ->groupBy('type')
            ->pluck('count', 'type')
            ->toArray();

        return [
            'total' => $total,
            'approved' => $approved,
            'rejected' => $rejected,
            'pending' => $pending,
            'byType' => $byType,
        ];
    }

    private function getPresenceTrend(Carbon $startDate, Carbon $endDate, ?int $departmentId): array
    {
        $query = Presence::whereBetween('date', [$startDate, $endDate])
            ->when($departmentId, fn($q) => $q->whereHas('user', fn($q2) => $q2->where('department_id', $departmentId)))
            ->select(DB::raw('DATE(date) as day'), DB::raw('count(*) as count'))
            ->groupBy('day')
            ->orderBy('day')
            ->get();

        return $query->map(fn($item) => [
            'date' => $item->day,
            'count' => $item->count,
        ])->toArray();
    }

    private function getDepartmentDistribution(): array
    {
        return Department::withCount(['users' => fn($q) => $q->where('role', 'employee')])
            ->having('users_count', '>', 0)
            ->get()
            ->map(fn($dept) => [
                'name' => $dept->name,
                'count' => $dept->users_count,
                'color' => $dept->color,
            ])
            ->toArray();
    }

    private function getTopEmployees(Carbon $startDate, Carbon $endDate, ?int $departmentId): array
    {
        return User::where('role', 'employee')
            ->when($departmentId, fn($q) => $q->where('department_id', $departmentId))
            ->withCount(['presences' => fn($q) => $q->whereBetween('date', [$startDate, $endDate])])
            ->orderByDesc('presences_count')
            ->take(5)
            ->get()
            ->map(fn($user) => [
                'name' => $user->name,
                'department' => $user->department?->name ?? 'N/A',
                'presences' => $user->presences_count,
            ])
            ->toArray();
    }

    private function getAverageCheckTimes(Carbon $startDate, Carbon $endDate, ?int $departmentId): array
    {
        $presences = Presence::whereBetween('date', [$startDate, $endDate])
            ->when($departmentId, fn($q) => $q->whereHas('user', fn($q2) => $q2->where('department_id', $departmentId)))
            ->whereNotNull('check_in')
            ->whereNotNull('check_out')
            ->get();

        if ($presences->isEmpty()) {
            return ['avgCheckIn' => 'N/A', 'avgCheckOut' => 'N/A'];
        }

        $checkInMinutes = $presences->map(fn($p) => Carbon::parse($p->check_in)->hour * 60 + Carbon::parse($p->check_in)->minute);
        $checkOutMinutes = $presences->map(fn($p) => Carbon::parse($p->check_out)->hour * 60 + Carbon::parse($p->check_out)->minute);

        $avgCheckIn = $checkInMinutes->avg();
        $avgCheckOut = $checkOutMinutes->avg();

        return [
            'avgCheckIn' => sprintf('%02d:%02d', floor($avgCheckIn / 60), $avgCheckIn % 60),
            'avgCheckOut' => sprintf('%02d:%02d', floor($avgCheckOut / 60), $avgCheckOut % 60),
        ];
    }

    private function getGeolocationStats(Carbon $startDate, Carbon $endDate): array
    {
        $total = Presence::whereBetween('date', [$startDate, $endDate])->count();
        $inZone = Presence::whereBetween('date', [$startDate, $endDate])
            ->where('check_in_status', 'in_zone')
            ->count();
        $outOfZone = Presence::whereBetween('date', [$startDate, $endDate])
            ->where('check_in_status', 'out_of_zone')
            ->count();
        $unknown = Presence::whereBetween('date', [$startDate, $endDate])
            ->where('check_in_status', 'unknown')
            ->count();

        return [
            'total' => $total,
            'inZone' => $inZone,
            'outOfZone' => $outOfZone,
            'unknown' => $unknown,
            'inZoneRate' => $total > 0 ? round(($inZone / $total) * 100, 1) : 0,
        ];
    }

    private function countWorkingDays(Carbon $startDate, Carbon $endDate): int
    {
        $count = 0;
        $current = $startDate->copy();

        while ($current <= $endDate) {
            if ($current->isWeekday()) {
                $count++;
            }
            $current->addDay();
        }

        return $count;
    }
}
