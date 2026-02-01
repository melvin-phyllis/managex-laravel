<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\InternEvaluation;
use Illuminate\Http\Request;

class InternEvaluationController extends Controller
{
    /**
     * List my evaluations
     */
    public function index()
    {
        $user = auth()->user();

        // Check if user is an intern
        if (!$user->isIntern()) {
            abort(403, 'Cette page est réservée aux stagiaires.');
        }

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
        $progressionData = $evaluations->reverse()->map(fn($e) => [
            'week' => $e->week_start->format('d/m'),
            'score' => $e->total_score,
            'discipline' => (float) $e->discipline_score,
            'behavior' => (float) $e->behavior_score,
            'skills' => (float) $e->skills_score,
            'communication' => (float) $e->communication_score,
        ])->values();

        // Latest evaluation
        $latestEvaluation = $evaluations->first();

        // Supervisor info
        $supervisor = $user->supervisor;

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
     * Show evaluation detail
     */
    public function show(InternEvaluation $evaluation)
    {
        $user = auth()->user();

        // Check if this evaluation belongs to the user
        if ($evaluation->intern_id !== $user->id) {
            abort(403, 'Vous n\'avez pas accès à cette évaluation.');
        }

        $criteria = InternEvaluation::CRITERIA;
        $grades = InternEvaluation::GRADES;

        return view('employee.evaluations.show', compact('evaluation', 'criteria', 'grades'));
    }
}
