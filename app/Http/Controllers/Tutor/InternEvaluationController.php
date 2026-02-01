<?php

namespace App\Http\Controllers\Tutor;

use App\Http\Controllers\Controller;
use App\Models\InternEvaluation;
use App\Models\User;
use App\Notifications\NewEvaluationNotification;
use Illuminate\Http\Request;

class InternEvaluationController extends Controller
{
    /**
     * List interns to evaluate
     */
    public function index()
    {
        $user = auth()->user();

        // Get interns supervised by current user
        $interns = $user->supervisees()
            ->interns()
            ->with(['department', 'position'])
            ->get()
            ->map(function ($intern) {
                // Check if evaluation exists for current week
                $currentWeekStart = now()->startOfWeek();
                $evaluation = InternEvaluation::where('intern_id', $intern->id)
                    ->where('week_start', $currentWeekStart)
                    ->first();

                $intern->current_week_evaluation = $evaluation;
                $intern->needs_evaluation = !$evaluation || $evaluation->status === 'draft';

                return $intern;
            });

        // Stats
        $stats = [
            'total_interns' => $interns->count(),
            'evaluated_this_week' => $interns->filter(fn($i) => $i->current_week_evaluation?->status === 'submitted')->count(),
            'pending' => $interns->filter(fn($i) => $i->needs_evaluation)->count(),
        ];

        return view('tutor.evaluations.index', compact('interns', 'stats'));
    }

    /**
     * Show evaluation form
     */
    public function create(User $intern)
    {
        $user = auth()->user();

        // Check if user is the supervisor
        if ($intern->supervisor_id !== $user->id) {
            abort(403, 'Vous n\'êtes pas le tuteur de ce stagiaire.');
        }

        $currentWeekStart = now()->startOfWeek();

        // Get or create draft evaluation
        $evaluation = InternEvaluation::getOrCreateDraft(
            $intern->id,
            $user->id,
            $currentWeekStart
        );

        // Get previous evaluations for reference
        $previousEvaluations = $intern->internEvaluations()
            ->submitted()
            ->orderBy('week_start', 'desc')
            ->limit(4)
            ->get();

        $criteria = InternEvaluation::CRITERIA;

        return view('tutor.evaluations.create', compact(
            'intern',
            'evaluation',
            'previousEvaluations',
            'criteria'
        ));
    }

    /**
     * Store evaluation
     */
    public function store(Request $request, User $intern)
    {
        $user = auth()->user();

        if ($intern->supervisor_id !== $user->id) {
            abort(403, 'Vous n\'êtes pas le tuteur de ce stagiaire.');
        }

        $validated = $request->validate([
            'discipline_score' => 'required|numeric|min:0|max:2.5',
            'behavior_score' => 'required|numeric|min:0|max:2.5',
            'skills_score' => 'required|numeric|min:0|max:2.5',
            'communication_score' => 'required|numeric|min:0|max:2.5',
            'discipline_comment' => 'nullable|string|max:1000',
            'behavior_comment' => 'nullable|string|max:1000',
            'skills_comment' => 'nullable|string|max:1000',
            'communication_comment' => 'nullable|string|max:1000',
            'general_comment' => 'nullable|string|max:2000',
            'objectives_next_week' => 'nullable|string|max:1000',
            'action' => 'required|in:draft,submit',
        ]);

        $currentWeekStart = now()->startOfWeek();

        $evaluation = InternEvaluation::updateOrCreate(
            [
                'intern_id' => $intern->id,
                'week_start' => $currentWeekStart,
            ],
            [
                'tutor_id' => $user->id,
                'discipline_score' => $validated['discipline_score'],
                'behavior_score' => $validated['behavior_score'],
                'skills_score' => $validated['skills_score'],
                'communication_score' => $validated['communication_score'],
                'discipline_comment' => $validated['discipline_comment'],
                'behavior_comment' => $validated['behavior_comment'],
                'skills_comment' => $validated['skills_comment'],
                'communication_comment' => $validated['communication_comment'],
                'general_comment' => $validated['general_comment'],
                'objectives_next_week' => $validated['objectives_next_week'],
                'status' => $validated['action'] === 'submit' ? 'submitted' : 'draft',
                'submitted_at' => $validated['action'] === 'submit' ? now() : null,
            ]
        );

        if ($validated['action'] === 'submit') {
            // Notify intern
            $intern->notify(new NewEvaluationNotification($evaluation));

            return redirect()->route('tutor.evaluations.index')
                ->with('success', 'Évaluation soumise avec succès. Le stagiaire a été notifié.');
        }

        return redirect()->route('tutor.evaluations.index')
            ->with('success', 'Brouillon sauvegardé.');
    }

    /**
     * Edit evaluation (draft only)
     */
    public function edit(InternEvaluation $evaluation)
    {
        $user = auth()->user();

        if ($evaluation->tutor_id !== $user->id) {
            abort(403, 'Vous n\'êtes pas l\'auteur de cette évaluation.');
        }

        if (!$evaluation->canBeEdited()) {
            abort(403, 'Cette évaluation a déjà été soumise et ne peut plus être modifiée.');
        }

        $intern = $evaluation->intern;

        $previousEvaluations = $intern->internEvaluations()
            ->where('id', '!=', $evaluation->id)
            ->submitted()
            ->orderBy('week_start', 'desc')
            ->limit(4)
            ->get();

        $criteria = InternEvaluation::CRITERIA;

        return view('tutor.evaluations.edit', compact(
            'evaluation',
            'intern',
            'previousEvaluations',
            'criteria'
        ));
    }

    /**
     * Update evaluation
     */
    public function update(Request $request, InternEvaluation $evaluation)
    {
        $user = auth()->user();

        if ($evaluation->tutor_id !== $user->id) {
            abort(403, 'Vous n\'êtes pas l\'auteur de cette évaluation.');
        }

        if (!$evaluation->canBeEdited()) {
            abort(403, 'Cette évaluation a déjà été soumise et ne peut plus être modifiée.');
        }

        $validated = $request->validate([
            'discipline_score' => 'required|numeric|min:0|max:2.5',
            'behavior_score' => 'required|numeric|min:0|max:2.5',
            'skills_score' => 'required|numeric|min:0|max:2.5',
            'communication_score' => 'required|numeric|min:0|max:2.5',
            'discipline_comment' => 'nullable|string|max:1000',
            'behavior_comment' => 'nullable|string|max:1000',
            'skills_comment' => 'nullable|string|max:1000',
            'communication_comment' => 'nullable|string|max:1000',
            'general_comment' => 'nullable|string|max:2000',
            'objectives_next_week' => 'nullable|string|max:1000',
            'action' => 'required|in:draft,submit',
        ]);

        $evaluation->update([
            'discipline_score' => $validated['discipline_score'],
            'behavior_score' => $validated['behavior_score'],
            'skills_score' => $validated['skills_score'],
            'communication_score' => $validated['communication_score'],
            'discipline_comment' => $validated['discipline_comment'],
            'behavior_comment' => $validated['behavior_comment'],
            'skills_comment' => $validated['skills_comment'],
            'communication_comment' => $validated['communication_comment'],
            'general_comment' => $validated['general_comment'],
            'objectives_next_week' => $validated['objectives_next_week'],
            'status' => $validated['action'] === 'submit' ? 'submitted' : 'draft',
            'submitted_at' => $validated['action'] === 'submit' ? now() : null,
        ]);

        if ($validated['action'] === 'submit') {
            $evaluation->intern->notify(new NewEvaluationNotification($evaluation));

            return redirect()->route('tutor.evaluations.index')
                ->with('success', 'Évaluation soumise avec succès.');
        }

        return redirect()->route('tutor.evaluations.index')
            ->with('success', 'Brouillon mis à jour.');
    }

    /**
     * View intern's evaluation history
     */
    public function history(User $intern)
    {
        $user = auth()->user();

        if ($intern->supervisor_id !== $user->id) {
            abort(403, 'Vous n\'êtes pas le tuteur de ce stagiaire.');
        }

        $evaluations = $intern->internEvaluations()
            ->where('tutor_id', $user->id)
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

        // Progression data
        $progressionData = $evaluations->reverse()->map(fn($e) => [
            'week' => $e->week_start->format('d/m'),
            'score' => $e->total_score,
        ])->values();

        return view('tutor.evaluations.history', compact(
            'intern',
            'evaluations',
            'averages',
            'progressionData'
        ));
    }

    /**
     * View a specific evaluation detail
     */
    public function show(InternEvaluation $evaluation)
    {
        $user = auth()->user();

        if ($evaluation->tutor_id !== $user->id) {
            abort(403, 'Vous n\'êtes pas l\'auteur de cette évaluation.');
        }

        $criteria = InternEvaluation::CRITERIA;

        return view('tutor.evaluations.show', compact('evaluation', 'criteria'));
    }
}
