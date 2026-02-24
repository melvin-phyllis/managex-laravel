<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BtsEvaluation;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;

class BtsEvaluationController extends Controller
{
    /**
     * List all BTS evaluations
     */
    public function index()
    {
        $evaluations = BtsEvaluation::with(['intern.department', 'evaluator'])
            ->latest()
            ->get();

        $internsWithoutEval = User::where('is_intern', true)
            ->where('status', 'active')
            ->where('intern_type', 'bts')
            ->whereDoesntHave('btsEvaluations')
            ->with('department')
            ->get();

        return view('admin.bts-evaluations.index', compact('evaluations', 'internsWithoutEval'));
    }

    /**
     * Show form to create BTS evaluation for an intern
     */
    public function create(User $intern)
    {
        $start = Carbon::parse($intern->contract_start_date ?? now()->subMonths(3));
        $end = Carbon::parse($intern->contract_end_date ?? now());

        $autoScores = [
            'assiduity' => BtsEvaluation::calculateAssiduity($intern, $start, $end),
            'execution' => BtsEvaluation::calculateExecution($intern, $start, $end),
            'initiative' => BtsEvaluation::calculateInitiative($intern, $start, $end),
            'presentation' => BtsEvaluation::calculatePresentation($intern, $start, $end),
        ];

        return view('admin.bts-evaluations.create', compact('intern', 'autoScores', 'start', 'end'));
    }

    /**
     * Store a new BTS evaluation
     */
    public function store(Request $request, User $intern)
    {
        $validated = $request->validate([
            'intern_bts_number' => 'nullable|string|max:50',
            'intern_field' => 'nullable|string|max:100',
            'stage_start_date' => 'required|date',
            'stage_end_date' => 'required|date|after:stage_start_date',
            'assiduity_score' => 'required|numeric|min:0|max:3',
            'relations_teamwork' => 'boolean',
            'relations_hierarchy' => 'boolean',
            'relations_courtesy' => 'boolean',
            'relations_listening' => 'boolean',
            'execution_score' => 'required|numeric|min:0|max:6',
            'initiative_score' => 'required|numeric|min:0|max:4',
            'presentation_score' => 'required|numeric|min:0|max:3',
            'appreciation' => 'nullable|string|max:2000',
            'justification_report' => 'nullable|string|max:5000',
        ]);

        // Calculate relations from sub-criteria
        $relationsScore = ($request->boolean('relations_teamwork') ? 1 : 0)
            + ($request->boolean('relations_hierarchy') ? 1 : 0)
            + ($request->boolean('relations_courtesy') ? 1 : 0)
            + ($request->boolean('relations_listening') ? 1 : 0);

        $totalScore = round(
            $validated['assiduity_score'] +
            $relationsScore +
            $validated['execution_score'] +
            $validated['initiative_score'] +
            $validated['presentation_score'],
            1
        );

        if ($totalScore > BtsEvaluation::JUSTIFICATION_THRESHOLD && empty($validated['justification_report'])) {
            return back()->withInput()->withErrors([
                'justification_report' => 'Un rapport justificatif est obligatoire pour une note supérieure à 16/20.',
            ]);
        }

        $evaluation = BtsEvaluation::create([
            'intern_id' => $intern->id,
            'evaluator_id' => auth()->id(),
            'intern_bts_number' => $validated['intern_bts_number'] ?? null,
            'intern_field' => $validated['intern_field'] ?? null,
            'stage_start_date' => $validated['stage_start_date'],
            'stage_end_date' => $validated['stage_end_date'],
            'assiduity_score' => $validated['assiduity_score'],
            'assiduity_details' => $request->input('assiduity_details'),
            'relations_score' => $relationsScore,
            'relations_teamwork' => $request->boolean('relations_teamwork'),
            'relations_hierarchy' => $request->boolean('relations_hierarchy'),
            'relations_courtesy' => $request->boolean('relations_courtesy'),
            'relations_listening' => $request->boolean('relations_listening'),
            'execution_score' => $validated['execution_score'],
            'execution_details' => $request->input('execution_details'),
            'initiative_score' => $validated['initiative_score'],
            'initiative_details' => $request->input('initiative_details'),
            'presentation_score' => $validated['presentation_score'],
            'total_score' => $totalScore,
            'appreciation' => $validated['appreciation'] ?? null,
            'justification_report' => $validated['justification_report'] ?? null,
            'status' => 'draft',
        ]);

        return redirect()->route('admin.bts-evaluations.show', $evaluation)
            ->with('success', 'Fiche BTS créée avec succès. Note : ' . $totalScore . '/20');
    }

    /**
     * Show BTS evaluation detail
     */
    public function show(BtsEvaluation $evaluation)
    {
        $evaluation->load(['intern.department', 'evaluator']);
        return view('admin.bts-evaluations.show', compact('evaluation'));
    }

    /**
     * Edit BTS evaluation
     */
    public function edit(BtsEvaluation $evaluation)
    {
        if (!$evaluation->canBeEdited()) {
            return back()->with('error', 'Cette fiche a déjà été soumise et ne peut plus être modifiée.');
        }

        $evaluation->load(['intern.department', 'evaluator']);
        $intern = $evaluation->intern;

        $start = Carbon::parse($evaluation->stage_start_date);
        $end = Carbon::parse($evaluation->stage_end_date);

        $autoScores = [
            'assiduity' => BtsEvaluation::calculateAssiduity($intern, $start, $end),
            'execution' => BtsEvaluation::calculateExecution($intern, $start, $end),
            'initiative' => BtsEvaluation::calculateInitiative($intern, $start, $end),
            'presentation' => BtsEvaluation::calculatePresentation($intern, $start, $end),
        ];

        return view('admin.bts-evaluations.edit', compact('evaluation', 'intern', 'autoScores', 'start', 'end'));
    }

    /**
     * Update BTS evaluation
     */
    public function update(Request $request, BtsEvaluation $evaluation)
    {
        if (!$evaluation->canBeEdited()) {
            return back()->with('error', 'Cette fiche a déjà été soumise.');
        }

        $validated = $request->validate([
            'intern_bts_number' => 'nullable|string|max:50',
            'intern_field' => 'nullable|string|max:100',
            'stage_start_date' => 'required|date',
            'stage_end_date' => 'required|date|after:stage_start_date',
            'assiduity_score' => 'required|numeric|min:0|max:3',
            'relations_teamwork' => 'boolean',
            'relations_hierarchy' => 'boolean',
            'relations_courtesy' => 'boolean',
            'relations_listening' => 'boolean',
            'execution_score' => 'required|numeric|min:0|max:6',
            'initiative_score' => 'required|numeric|min:0|max:4',
            'presentation_score' => 'required|numeric|min:0|max:3',
            'appreciation' => 'nullable|string|max:2000',
            'justification_report' => 'nullable|string|max:5000',
        ]);

        $relationsScore = ($request->boolean('relations_teamwork') ? 1 : 0)
            + ($request->boolean('relations_hierarchy') ? 1 : 0)
            + ($request->boolean('relations_courtesy') ? 1 : 0)
            + ($request->boolean('relations_listening') ? 1 : 0);

        $totalScore = round(
            $validated['assiduity_score'] + $relationsScore +
            $validated['execution_score'] + $validated['initiative_score'] + $validated['presentation_score'],
            1
        );

        if ($totalScore > BtsEvaluation::JUSTIFICATION_THRESHOLD && empty($validated['justification_report'])) {
            return back()->withInput()->withErrors([
                'justification_report' => 'Un rapport justificatif est obligatoire pour une note supérieure à 16/20.',
            ]);
        }

        $evaluation->update([
            'intern_bts_number' => $validated['intern_bts_number'],
            'intern_field' => $validated['intern_field'],
            'stage_start_date' => $validated['stage_start_date'],
            'stage_end_date' => $validated['stage_end_date'],
            'assiduity_score' => $validated['assiduity_score'],
            'relations_score' => $relationsScore,
            'relations_teamwork' => $request->boolean('relations_teamwork'),
            'relations_hierarchy' => $request->boolean('relations_hierarchy'),
            'relations_courtesy' => $request->boolean('relations_courtesy'),
            'relations_listening' => $request->boolean('relations_listening'),
            'execution_score' => $validated['execution_score'],
            'initiative_score' => $validated['initiative_score'],
            'presentation_score' => $validated['presentation_score'],
            'total_score' => $totalScore,
            'appreciation' => $validated['appreciation'],
            'justification_report' => $validated['justification_report'],
        ]);

        return redirect()->route('admin.bts-evaluations.show', $evaluation)
            ->with('success', 'Fiche BTS mise à jour. Note : ' . $totalScore . '/20');
    }

    /**
     * Submit (finalize) the evaluation
     */
    public function submit(BtsEvaluation $evaluation)
    {
        if (!$evaluation->submit()) {
            $msg = $evaluation->requires_justification && empty($evaluation->justification_report)
                ? 'Un rapport justificatif est obligatoire pour une note > 16/20.'
                : 'Cette fiche a déjà été soumise.';
            return back()->with('error', $msg);
        }

        return back()->with('success', 'Fiche BTS soumise officiellement.');
    }

    /**
     * Export PDF in official MEPS format
     */
    public function exportPdf(BtsEvaluation $evaluation)
    {
        $evaluation->load(['intern.department', 'evaluator']);

        $pdf = Pdf::loadView('pdf.bts-evaluation', [
            'evaluation' => $evaluation,
            'intern' => $evaluation->intern,
            'evaluator' => $evaluation->evaluator,
        ]);

        $pdf->setPaper('A4', 'portrait');
        $filename = 'Fiche_BTS_' . str_replace(' ', '_', $evaluation->intern->name) . '_' . now()->format('Y-m-d') . '.pdf';

        return $pdf->download($filename);
    }
}
