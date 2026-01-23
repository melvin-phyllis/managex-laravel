<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Survey;
use App\Models\SurveyResponse;
use Illuminate\Http\Request;

class SurveyController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $filter = $request->get('filter', 'pending');

        // Sondages actifs
        $activeSurveys = Survey::active()
            ->with('questions')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($survey) use ($user) {
                $survey->has_responded = $survey->hasUserResponded($user);
                return $survey;
            });

        // Séparer les sondages répondus et non répondus
        $pendingSurveys = $activeSurveys->filter(fn($s) => !$s->has_responded);
        $completedSurveys = $activeSurveys->filter(fn($s) => $s->has_responded);

        // Sélectionner les sondages selon le filtre
        $surveys = $filter === 'completed' ? $completedSurveys : $pendingSurveys;

        return view('employee.surveys.index', compact('surveys', 'pendingSurveys', 'completedSurveys'));
    }

    public function show(Survey $survey)
    {
        if (!$survey->is_active) {
            return redirect()->route('employee.surveys.index')
                ->with('error', 'Ce sondage n\'est plus actif.');
        }

        $survey->load('questions');
        $hasResponded = $survey->hasUserResponded(auth()->user());

        return view('employee.surveys.show', compact('survey', 'hasResponded'));
    }

    public function respond(Request $request, Survey $survey)
    {
        if (!$survey->is_active) {
            return redirect()->route('employee.surveys.index')
                ->with('error', 'Ce sondage n\'est plus actif.');
        }

        if ($survey->hasUserResponded(auth()->user())) {
            return redirect()->route('employee.surveys.index')
                ->with('error', 'Vous avez déjà répondu à ce sondage.');
        }

        $survey->load('questions');

        // Valider les réponses
        $rules = [];
        foreach ($survey->questions as $question) {
            $key = "responses.{$question->id}";
            if ($question->is_required) {
                $rules[$key] = ['required'];
            } else {
                $rules[$key] = ['nullable'];
            }

            // Validation spécifique selon le type
            if ($question->type === 'rating') {
                $rules[$key][] = 'integer';
                $rules[$key][] = 'min:1';
                $rules[$key][] = 'max:5';
            } elseif ($question->type === 'choice' && $question->options) {
                $rules[$key][] = 'in:' . implode(',', $question->options);
            } elseif ($question->type === 'yesno') {
                $rules[$key][] = 'in:oui,non';
            }
        }

        $request->validate($rules);

        // Enregistrer les réponses
        foreach ($request->responses as $questionId => $response) {
            if ($response !== null) {
                SurveyResponse::create([
                    'survey_question_id' => $questionId,
                    'user_id' => auth()->id(),
                    'reponse' => $response,
                ]);
            }
        }

        return redirect()->route('employee.surveys.index')
            ->with('success', 'Merci pour votre participation au sondage !');
    }
}
