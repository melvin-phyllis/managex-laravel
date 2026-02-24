<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\BtsEvaluation;
use App\Models\InternEvaluation;

class InternEvaluationController extends Controller
{
    /**
     * List my evaluations (adapté selon le type de stagiaire)
     */
    public function index()
    {
        $user = auth()->user();

        // Check if user is an intern
        if (! $user->isIntern()) {
            abort(403, 'Cette page est réservée aux stagiaires.');
        }

        // Supervisor info
        $supervisor = $user->supervisor;

        // Détecter si c'est un stagiaire BTS
        if ($user->intern_type === 'bts') {
            return $this->btsIndex($user, $supervisor);
        }

        return $this->normalIndex($user, $supervisor);
    }

    /**
     * Évaluations pour stagiaires normaux (hebdomadaires /5)
     */
    private function normalIndex($user, $supervisor)
    {
        $evaluations = $user->internEvaluations()
            ->with('tutor')
            ->submitted()
            ->orderBy('week_start', 'desc')
            ->get();

        // Calculate averages
        $averages = null;
        if ($evaluations->isNotEmpty()) {
            $averages = [
                'discipline' => round($evaluations->avg('discipline_score'), 1),
                'behavior' => round($evaluations->avg('behavior_score'), 1),
                'skills' => round($evaluations->avg('skills_score'), 1),
                'communication' => round($evaluations->avg('communication_score'), 1),
                'total' => round($evaluations->avg('total_score'), 1),
            ];
        }

        // Progression data for chart
        $progressionData = $evaluations->reverse()->map(fn ($e) => [
            'week' => $e->week_start->format('d/m'),
            'score' => $e->total_score,
            'discipline' => (float) $e->discipline_score,
            'behavior' => (float) $e->behavior_score,
            'skills' => (float) $e->skills_score,
            'communication' => (float) $e->communication_score,
        ])->values();

        // Latest evaluation
        $latestEvaluation = $evaluations->first();

        $criteria = InternEvaluation::CRITERIA;
        $grades = InternEvaluation::GRADES;

        return view('employee.evaluations.index', compact(
            'evaluations',
            'averages',
            'progressionData',
            'latestEvaluation',
            'supervisor',
            'criteria',
            'grades'
        ));
    }

    /**
     * Évaluations pour stagiaires BTS (/20)
     */
    private function btsIndex($user, $supervisor)
    {
        $evaluations = $user->btsEvaluations()
            ->with('evaluator')
            ->submitted()
            ->orderBy('submitted_at', 'desc')
            ->get();

        // Calculate averages
        $averages = null;
        if ($evaluations->isNotEmpty()) {
            $averages = [
                'assiduity' => round($evaluations->avg('assiduity_score'), 1),
                'relations' => round($evaluations->avg('relations_score'), 1),
                'execution' => round($evaluations->avg('execution_score'), 1),
                'initiative' => round($evaluations->avg('initiative_score'), 1),
                'presentation' => round($evaluations->avg('presentation_score'), 1),
                'total' => round($evaluations->avg('total_score'), 1),
            ];
        }

        // Progression data for chart
        $progressionData = $evaluations->reverse()->map(fn ($e) => [
            'label' => $e->stage_start_date->format('d/m') . ' - ' . $e->stage_end_date->format('d/m'),
            'score' => (float) $e->total_score,
            'assiduity' => (float) $e->assiduity_score,
            'relations' => (float) $e->relations_score,
            'execution' => (float) $e->execution_score,
            'initiative' => (float) $e->initiative_score,
            'presentation' => (float) $e->presentation_score,
        ])->values();

        // Latest evaluation
        $latestEvaluation = $evaluations->first();

        $criteria = BtsEvaluation::CRITERIA;

        return view('employee.evaluations.bts-index', compact(
            'evaluations',
            'averages',
            'progressionData',
            'latestEvaluation',
            'supervisor',
            'criteria'
        ));
    }

    /**
     * Show evaluation detail
     */
    public function show($evaluationId)
    {
        $user = auth()->user();

        // BTS intern → show BTS evaluation
        if ($user->intern_type === 'bts') {
            $evaluation = BtsEvaluation::with('evaluator')->findOrFail($evaluationId);

            if ($evaluation->intern_id !== $user->id) {
                abort(403, 'Vous n\'avez pas accès à cette évaluation.');
            }

            $criteria = BtsEvaluation::CRITERIA;

            return view('employee.evaluations.bts-show', compact('evaluation', 'criteria'));
        }

        // Normal intern → show normal evaluation
        $evaluation = InternEvaluation::with('tutor')->findOrFail($evaluationId);

        if ($evaluation->intern_id !== $user->id) {
            abort(403, 'Vous n\'avez pas accès à cette évaluation.');
        }

        $criteria = InternEvaluation::CRITERIA;
        $grades = InternEvaluation::GRADES;

        return view('employee.evaluations.show', compact('evaluation', 'criteria', 'grades'));
    }
}
