<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Presence;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PresenceController extends Controller
{
    /**
     * Master View - Vue unifiée des présences (Aujourd'hui + Historique)
     */
    public function masterView(Request $request)
    {
        $departments = Department::where('is_active', true)->orderBy('name')->get();

        return view('admin.presences.master-view', compact('departments'));
    }

    /**
     * API - Données pour la Master View
     */
    public function masterViewData(Request $request)
    {
        $mode = $request->get('mode', 'today');
        $departmentId = $request->get('department_id');
        $riskOnly = $request->get('risk_only') === 'true';

        if ($mode === 'today') {
            return $this->getTodayData($departmentId);
        } else {
            return $this->getHistoricalData($request, $departmentId, $riskOnly);
        }
    }

    /**
     * Données du jour
     */
    private function getTodayData($departmentId)
    {
        $today = now()->toDateString();

        // Employés attendus aujourd'hui (jours de travail configurés, embauchés avant ou aujourd'hui)
        $expectedEmployees = User::where('role', 'employee')
            ->with(['department', 'position'])
            ->whereHas('workDays', fn ($q) => $q->where('day_of_week', now()->dayOfWeekIso))
            ->where(function ($q) {
                $q->whereNull('hire_date')
                    ->orWhere('hire_date', '<=', now()->toDateString());
            })
            ->when($departmentId, fn ($q) => $q->where('department_id', $departmentId))
            ->get();

        // Présences du jour
        $presencesToday = Presence::whereDate('date', $today)
            ->whereIn('user_id', $expectedEmployees->pluck('id'))
            ->get()
            ->keyBy('user_id');

        // Construire la liste des employés avec leur statut
        $employees = $expectedEmployees->map(function ($employee) use ($presencesToday) {
            $presence = $presencesToday->get($employee->id);

            if (! $presence) {
                $employee->status = 'absent';
                $employee->check_in = null;
                $employee->check_out = null;
                $employee->is_late = false;
                $employee->late_minutes = 0;
                $employee->is_auto_checkout = false;
            } else {
                $employee->status = $presence->is_late ? 'late' : 'present';
                $employee->check_in = $presence->check_in ? Carbon::parse($presence->check_in)->format('H:i') : null;
                $employee->check_out = $presence->check_out ? Carbon::parse($presence->check_out)->format('H:i') : null;
                $employee->is_late = $presence->is_late;
                $employee->late_minutes = $presence->late_minutes ?? 0;
                $employee->is_auto_checkout = $presence->is_auto_checkout ?? false;
            }

            return $employee;
        })->sortByDesc('is_late')->sortBy('status')->values();

        // Stats
        $stats = [
            'total' => $expectedEmployees->count(),
            'present' => $employees->where('status', 'present')->count(),
            'late' => $employees->where('status', 'late')->count(),
            'absent' => $employees->where('status', 'absent')->count(),
        ];

        return response()->json([
            'mode' => 'today',
            'date' => now()->format('d/m/Y'),
            'stats' => $stats,
            'employees' => $employees->map(fn ($e) => [
                'id' => $e->id,
                'name' => $e->name,
                'avatar' => $e->avatar ? avatar_url($e->avatar) : null,
                'department' => $e->department?->name,
                'department_color' => $e->department?->color,
                'position' => $e->position?->name,
                'status' => $e->status,
                'check_in' => $e->check_in,
                'check_out' => $e->check_out,
                'is_late' => $e->is_late,
                'late_minutes' => $e->late_minutes,
                'is_auto_checkout' => $e->is_auto_checkout,
            ]),
        ]);
    }

    /**
     * Données historiques agrégées
     */
    private function getHistoricalData(Request $request, $departmentId, $riskOnly)
    {
        $period = $request->get('period', 'month');

        // Déterminer les dates
        if ($period === 'custom') {
            $startDate = Carbon::parse($request->get('start_date', now()->startOfMonth()));
            $endDate = Carbon::parse($request->get('end_date', now()->endOfMonth()));
        } else {
            switch ($period) {
                case 'week':
                    $startDate = now()->startOfWeek();
                    $endDate = now()->endOfWeek();
                    break;
                case 'quarter':
                    $startDate = now()->startOfQuarter();
                    $endDate = now()->endOfQuarter();
                    break;
                default:
                    $startDate = now()->startOfMonth();
                    $endDate = now()->endOfMonth();
            }
        }

        // Jours ouvrés
        $workingDays = 0;
        $tempDate = $startDate->copy();
        while ($tempDate <= $endDate) {
            if (! $tempDate->isWeekend()) {
                $workingDays++;
            }
            $tempDate->addDay();
        }
        $expectedHours = $workingDays * 8;

        // Employés avec stats
        $employees = User::where('role', 'employee')
            ->with(['department', 'position'])
            ->withCount(['presences as presence_count' => fn ($q) => $q->whereBetween('date', [$startDate, $endDate])])
            ->withCount(['presences as late_count' => fn ($q) => $q->whereBetween('date', [$startDate, $endDate])->where('is_late', true)])
            ->withSum(['presences as total_late_minutes' => fn ($q) => $q->whereBetween('date', [$startDate, $endDate])->where('is_late', true)], 'late_minutes')
            ->when($departmentId, fn ($q) => $q->where('department_id', $departmentId))
            ->orderByDesc('late_count')
            ->get();

        // Calculer heures travaillées
        $presencesByUser = Presence::whereIn('user_id', $employees->pluck('id'))
            ->whereBetween('date', [$startDate, $endDate])
            ->whereNotNull('check_out')
            ->get()
            ->groupBy('user_id');

        $employees = $employees->map(function ($employee) use ($presencesByUser, $expectedHours) {
            $userPresences = $presencesByUser->get($employee->id, collect());
            $totalMinutes = $userPresences->sum(fn ($p) => $p->check_in && $p->check_out
                ? Carbon::parse($p->check_in)->diffInMinutes(Carbon::parse($p->check_out))
                : 0);

            $employee->total_worked_hours = round($totalMinutes / 60, 1);
            $employee->attendance_rate = $expectedHours > 0
                ? min(100, round(($employee->total_worked_hours / $expectedHours) * 100, 1))
                : 0;

            $lateMin = (int) abs($employee->total_late_minutes ?? 0);
            $employee->late_impact = $lateMin >= 60
                ? floor($lateMin / 60).'h '.($lateMin % 60).'min'
                : $lateMin.'min';

            // Niveau de risque
            if ($employee->attendance_rate < 80 || $employee->late_count > 10 || $lateMin > 120) {
                $employee->risk_level = 'high';
            } elseif ($employee->attendance_rate < 95 || $employee->late_count > 5 || $lateMin > 60) {
                $employee->risk_level = 'medium';
            } else {
                $employee->risk_level = 'low';
            }

            return $employee;
        });

        if ($riskOnly) {
            $employees = $employees->filter(fn ($e) => $e->risk_level !== 'low')->values();
        }

        // Stats globales
        $stats = [
            'total' => $employees->count(),
            'present' => $employees->where('risk_level', 'low')->count(),
            'late' => $employees->where('risk_level', 'medium')->count(),
            'absent' => $employees->where('risk_level', 'high')->count(),
        ];

        return response()->json([
            'mode' => 'historical',
            'period' => $period,
            'start_date' => $startDate->format('d/m/Y'),
            'end_date' => $endDate->format('d/m/Y'),
            'working_days' => $workingDays,
            'expected_hours' => $expectedHours,
            'stats' => $stats,
            'employees' => $employees->map(fn ($e) => [
                'id' => $e->id,
                'name' => $e->name,
                'avatar' => $e->avatar ? avatar_url($e->avatar) : null,
                'department' => $e->department?->name,
                'department_color' => $e->department?->color,
                'position' => $e->position?->name,
                'presence_count' => $e->presence_count,
                'late_count' => $e->late_count,
                'total_worked_hours' => $e->total_worked_hours,
                'attendance_rate' => $e->attendance_rate,
                'late_impact' => $e->late_impact,
                'risk_level' => $e->risk_level,
            ]),
        ]);
    }

    /**
     * API - Détails des retards d'un employé
     */
    public function employeeDetails(Request $request, $userId)
    {
        $period = $request->get('period', 'month');

        if ($period === 'custom') {
            $startDate = Carbon::parse($request->get('start_date', now()->startOfMonth()));
            $endDate = Carbon::parse($request->get('end_date', now()->endOfMonth()));
        } else {
            switch ($period) {
                case 'week':
                    $startDate = now()->startOfWeek();
                    $endDate = now()->endOfWeek();
                    break;
                case 'quarter':
                    $startDate = now()->startOfQuarter();
                    $endDate = now()->endOfQuarter();
                    break;
                default:
                    $startDate = now()->startOfMonth();
                    $endDate = now()->endOfMonth();
            }
        }

        $latePresences = Presence::where('user_id', $userId)
            ->whereBetween('date', [$startDate, $endDate])
            ->where('is_late', true)
            ->orderByDesc('date')
            ->get()
            ->map(fn ($p) => [
                'date' => Carbon::parse($p->date)->format('d/m/Y'),
                'day' => Carbon::parse($p->date)->translatedFormat('l'),
                'check_in' => $p->check_in ? Carbon::parse($p->check_in)->format('H:i') : null,
                'late_minutes' => $p->late_minutes,
            ]);

        return response()->json([
            'user_id' => $userId,
            'late_days' => $latePresences,
        ]);
    }

    /**
     * Page détaillée de présence d'un employé
     */
    public function showEmployeePresence(Request $request, $userId)
    {
        $user = User::with('department')->findOrFail($userId);

        // Période
        $period = $request->get('period', 'month');
        if ($period === 'custom') {
            $startDate = Carbon::parse($request->get('start_date', now()->startOfMonth()));
            $endDate = Carbon::parse($request->get('end_date', now()->endOfMonth()));
        } else {
            switch ($period) {
                case 'week':
                    $startDate = now()->startOfWeek();
                    $endDate = now()->endOfWeek();
                    break;
                case 'quarter':
                    $startDate = now()->startOfQuarter();
                    $endDate = now()->endOfQuarter();
                    break;
                case 'year':
                    $startDate = now()->startOfYear();
                    $endDate = now()->endOfYear();
                    break;
                default:
                    $startDate = now()->startOfMonth();
                    $endDate = now()->endOfMonth();
            }
        }

        // Récupérer toutes les présences de la période
        $presences = Presence::where('user_id', $userId)
            ->whereBetween('date', [$startDate, $endDate])
            ->orderByDesc('date')
            ->get();

        // Calculer les jours ouvrés
        $workDays = 0;
        $current = $startDate->copy();
        while ($current <= $endDate && $current <= now()) {
            if (! $current->isWeekend()) {
                $workDays++;
            }
            $current->addDay();
        }

        // Statistiques
        $totalPresent = $presences->whereNotNull('check_in')->count();
        $totalLate = $presences->where('is_late', true)->count();
        $totalAbsent = max(0, $workDays - $totalPresent);
        $attendanceRate = $workDays > 0 ? round(($totalPresent / $workDays) * 100, 1) : 0;

        // Retards
        $cumulativeLateMinutes = $presences->sum('late_minutes');
        $averageLateMinutes = $totalLate > 0 ? round($cumulativeLateMinutes / $totalLate, 0) : 0;

        // Heures supplémentaires (si overtime_minutes existe)
        $totalOvertimeMinutes = $presences->sum('overtime_minutes');

        // Temps de travail total
        $totalWorkMinutes = $presences->sum(function ($p) {
            if (! $p->check_in || ! $p->check_out) {
                return 0;
            }
            $checkIn = Carbon::parse($p->check_in);
            $checkOut = Carbon::parse($p->check_out);

            return $checkIn->diffInMinutes($checkOut);
        });

        // Liste des retards détaillés (pour les stats, pas affichée)
        $latePresences = $presences->where('is_late', true)->map(fn ($p) => [
            'date' => Carbon::parse($p->date)->format('d/m/Y'),
            'day' => Carbon::parse($p->date)->translatedFormat('l'),
            'check_in' => $p->check_in ? Carbon::parse($p->check_in)->format('H:i') : '-',
            'late_minutes' => $p->late_minutes,
        ])->values();

        // Pagination des présences (configurable, max 200)
        $allowedPerPage = [10, 20, 30, 40, 50, 100, 150, 200];
        $perPage = (int) $request->get('per_page', 30);
        $perPage = in_array($perPage, $allowedPerPage) ? $perPage : 30;

        $paginatedPresences = Presence::where('user_id', $userId)
            ->whereBetween('date', [$startDate, $endDate])
            ->orderByDesc('date')
            ->paginate($perPage)
            ->withQueryString();

        // Transformer les données paginées
        $allPresences = $paginatedPresences->getCollection()->map(fn ($p) => [
            'date' => Carbon::parse($p->date)->format('d/m/Y'),
            'day' => Carbon::parse($p->date)->translatedFormat('l'),
            'check_in' => $p->check_in ? Carbon::parse($p->check_in)->format('H:i') : '-',
            'check_out' => $p->check_out ? Carbon::parse($p->check_out)->format('H:i') : '-',
            'is_late' => $p->is_late,
            'late_minutes' => $p->late_minutes,
            'overtime_minutes' => $p->overtime_minutes ?? 0,
            'is_auto_checkout' => $p->is_auto_checkout ?? false,
            'work_hours' => $p->check_in && $p->check_out
                ? round(Carbon::parse($p->check_in)->diffInMinutes(Carbon::parse($p->check_out)) / 60, 1)
                : 0,
        ]);

        return view('admin.presences.employee-detail', [
            'user' => $user,
            'period' => $period,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'stats' => [
                'work_days' => $workDays,
                'total_present' => $totalPresent,
                'total_late' => $totalLate,
                'total_absent' => $totalAbsent,
                'attendance_rate' => $attendanceRate,
                'cumulative_late_minutes' => $cumulativeLateMinutes,
                'average_late_minutes' => $averageLateMinutes,
                'total_overtime_minutes' => $totalOvertimeMinutes,
                'total_work_minutes' => $totalWorkMinutes,
            ],
            'latePresences' => $latePresences,
            'allPresences' => $allPresences,
            'pagination' => $paginatedPresences,
        ]);
    }

    public function index(Request $request)
    {
        $query = Presence::with('user');

        // Filtres
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('date_debut')) {
            $query->where('date', '>=', $request->date_debut);
        }

        if ($request->filled('date_fin')) {
            $query->where('date', '<=', $request->date_fin);
        }

        if ($request->filled('mois')) {
            $date = Carbon::parse($request->mois);
            $query->month($date->month, $date->year);
        }

        $presences = $query->orderBy('date', 'desc')->paginate(15);
        $employees = User::where('role', 'employee')->orderBy('name')->get();

        return view('admin.presences.index', compact('presences', 'employees'));
    }

    public function exportCsv(Request $request)
    {
        $presences = $this->getFilteredPresences($request);

        $filename = 'presences_'.now()->format('Y-m-d').'.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function () use ($presences) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Employé', 'Date', 'Arrivée', 'Départ', 'Heures travaillées']);

            foreach ($presences as $presence) {
                fputcsv($file, [
                    $presence->user->name,
                    $presence->date->format('d/m/Y'),
                    $presence->check_in_formatted ?? '-',
                    $presence->check_out_formatted ?? '-',
                    $presence->hours_worked ? number_format($presence->hours_worked, 2).'h' : '-',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportPdf(Request $request)
    {
        $presences = $this->getFilteredPresences($request);

        $pdf = Pdf::loadView('pdf.presences-report', [
            'presences' => $presences,
            'generatedAt' => now(),
        ]);

        return $pdf->download('presences_'.now()->format('Y-m-d').'.pdf');
    }

    public function exportExcel(Request $request)
    {
        return redirect()->back()->with('error', 'L\'export Excel n\'est pas disponible sur cet hébergement.');
    }

    private function getFilteredPresences(Request $request)
    {
        $query = Presence::with('user');

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('date_debut')) {
            $query->where('date', '>=', $request->date_debut);
        }

        if ($request->filled('date_fin')) {
            $query->where('date', '<=', $request->date_fin);
        }

        return $query->orderBy('date', 'desc')->get();
    }
}
