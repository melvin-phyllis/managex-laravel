<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InternEvaluation;
use App\Models\User;
use App\Models\Department;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class InternEvaluationController extends Controller
{
    /**
     * Dashboard with global stats
     */
    public function index(Request $request)
    {
        // Get all interns
        $interns = User::interns()
            ->with(['supervisor', 'department', 'internEvaluations' => function ($q) {
                $q->submitted()->latest('week_start');
            }])
            ->get();

        // Statistics
        $stats = [
            'total_interns' => $interns->count(),
            'interns_with_supervisor' => $interns->filter(fn($i) => $i->supervisor_id)->count(),
            'evaluations_this_week' => InternEvaluation::currentWeek()->submitted()->count(),
            'evaluations_total' => InternEvaluation::submitted()->count(),
            'average_score' => round(InternEvaluation::submitted()->get()->avg('total_score'), 1),
            'pending_evaluations' => $this->getPendingEvaluationsCount(),
        ];

        // Recent evaluations
        $recentEvaluations = InternEvaluation::with(['intern', 'tutor'])
            ->submitted()
            ->latest('submitted_at')
            ->limit(10)
            ->get();

        // Score distribution for chart
        $scoreDistribution = [
            'A' => InternEvaluation::submitted()->get()->filter(fn($e) => $e->grade_letter === 'A')->count(),
            'B' => InternEvaluation::submitted()->get()->filter(fn($e) => $e->grade_letter === 'B')->count(),
            'C' => InternEvaluation::submitted()->get()->filter(fn($e) => $e->grade_letter === 'C')->count(),
            'D' => InternEvaluation::submitted()->get()->filter(fn($e) => $e->grade_letter === 'D')->count(),
            'E' => InternEvaluation::submitted()->get()->filter(fn($e) => $e->grade_letter === 'E')->count(),
        ];

        // Departments for filter
        $departments = Department::orderBy('name')->get();

        return view('admin.intern-evaluations.index', compact(
            'interns',
            'stats',
            'recentEvaluations',
            'scoreDistribution',
            'departments'
        ));
    }

    /**
     * Show intern details with evaluation history
     */
    public function show(User $intern)
    {
        if (!$intern->isIntern()) {
            abort(404, 'Cet utilisateur n\'est pas un stagiaire.');
        }

        $intern->load(['supervisor', 'department', 'position']);

        $evaluations = $intern->internEvaluations()
            ->with('tutor')
            ->submitted()
            ->orderBy('week_start', 'desc')
            ->get();

        // Calculate averages
        $averages = [
            'discipline' => round($evaluations->avg('discipline_score'), 1),
            'behavior' => round($evaluations->avg('behavior_score'), 1),
            'skills' => round($evaluations->avg('skills_score'), 1),
            'communication' => round($evaluations->avg('communication_score'), 1),
            'total' => round($evaluations->avg('total_score'), 1),
        ];

        // Progression data for chart
        $progressionData = $evaluations->reverse()->map(fn($e) => [
            'week' => $e->week_start->format('d/m'),
            'score' => $e->total_score,
        ])->values();

        // Available tutors for assignment (admins and employees, excluding interns)
        $tutors = User::where('status', 'active')
            ->where(function ($q) {
                $q->where('role', 'admin')
                  ->orWhere(function ($q2) {
                      $q2->where('role', 'employee')
                         ->where(function ($q3) {
                             $q3->whereNull('contract_type')
                                ->orWhere('contract_type', '!=', 'stage');
                         });
                  });
            })
            ->orderBy('name')
            ->get();

        return view('admin.intern-evaluations.show', compact(
            'intern',
            'evaluations',
            'averages',
            'progressionData',
            'tutors'
        ));
    }

    /**
     * Global report with filters
     */
    public function report(Request $request)
    {
        $query = InternEvaluation::with(['intern.department', 'tutor'])
            ->submitted();

        // Apply filters
        if ($request->filled('department_id')) {
            $query->whereHas('intern', function ($q) use ($request) {
                $q->where('department_id', $request->department_id);
            });
        }

        if ($request->filled('tutor_id')) {
            $query->where('tutor_id', $request->tutor_id);
        }

        if ($request->filled('date_from')) {
            $query->where('week_start', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('week_start', '<=', $request->date_to);
        }

        if ($request->filled('grade')) {
            $query->get()->filter(fn($e) => $e->grade_letter === $request->grade);
        }

        $evaluations = $query->orderBy('week_start', 'desc')->paginate(25);

        // Filters data
        $departments = Department::orderBy('name')->get();
        $tutors = User::whereHas('givenEvaluations')->orderBy('name')->get();

        return view('admin.intern-evaluations.report', compact(
            'evaluations',
            'departments',
            'tutors'
        ));
    }

    /**
     * Export PDF report
     */
    public function exportPdf(Request $request)
    {
        $request->validate([
            'intern_id' => 'required|exists:users,id',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date',
        ]);

        $intern = User::findOrFail($request->intern_id);

        $query = $intern->internEvaluations()->with('tutor')->submitted();

        if ($request->filled('date_from')) {
            $query->where('week_start', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('week_start', '<=', $request->date_to);
        }

        $evaluations = $query->orderBy('week_start')->get();

        // Calculate averages
        $averages = [
            'discipline' => round($evaluations->avg('discipline_score'), 1),
            'behavior' => round($evaluations->avg('behavior_score'), 1),
            'skills' => round($evaluations->avg('skills_score'), 1),
            'communication' => round($evaluations->avg('communication_score'), 1),
            'total' => round($evaluations->avg('total_score'), 1),
        ];

        $pdf = Pdf::loadView('pdf.intern-progress-report', [
            'intern' => $intern,
            'evaluations' => $evaluations,
            'averages' => $averages,
            'dateFrom' => $request->date_from,
            'dateTo' => $request->date_to,
        ]);

        $filename = 'rapport-stagiaire-' . str_replace(' ', '-', strtolower($intern->name)) . '-' . now()->format('Y-m-d') . '.pdf';

        return $pdf->download($filename);
    }

    /**
     * List missing evaluations
     */
    public function missingEvaluations()
    {
        $lastWeekStart = now()->subWeek()->startOfWeek();

        // Get interns with supervisors who don't have last week's evaluation
        $internsWithMissing = User::interns()
            ->withSupervisor()
            ->with(['supervisor', 'department'])
            ->get()
            ->filter(function ($intern) use ($lastWeekStart) {
                return !InternEvaluation::where('intern_id', $intern->id)
                    ->forWeek($lastWeekStart)
                    ->submitted()
                    ->exists();
            });

        // Group by tutor
        $missingByTutor = $internsWithMissing->groupBy('supervisor_id');

        return view('admin.intern-evaluations.missing', compact(
            'internsWithMissing',
            'missingByTutor',
            'lastWeekStart'
        ));
    }

    /**
     * Assign supervisor to intern
     */
    public function assignSupervisor(Request $request, User $intern)
    {
        if (!$intern->isIntern()) {
            abort(404, 'Cet utilisateur n\'est pas un stagiaire.');
        }

        $request->validate([
            'supervisor_id' => 'required|exists:users,id',
        ]);

        $intern->update([
            'supervisor_id' => $request->supervisor_id,
        ]);

        return redirect()->back()->with('success', 'Tuteur assigné avec succès.');
    }

    /**
     * Remove supervisor from intern
     */
    public function removeSupervisor(User $intern)
    {
        if (!$intern->isIntern()) {
            abort(404, 'Cet utilisateur n\'est pas un stagiaire.');
        }

        $intern->update([
            'supervisor_id' => null,
        ]);

        return redirect()->back()->with('success', 'Tuteur retiré avec succès.');
    }

    /**
     * Get pending evaluations count
     */
    private function getPendingEvaluationsCount(): int
    {
        $currentWeekStart = now()->startOfWeek();

        $internsWithSupervisor = User::interns()->withSupervisor()->count();

        $evaluationsThisWeek = InternEvaluation::currentWeek()->submitted()->count();

        return max(0, $internsWithSupervisor - $evaluationsThisWeek);
    }
}
