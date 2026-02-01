<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Survey;
use App\Models\SurveyQuestion;
use App\Models\User;
use App\Notifications\NewSurveyNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;

class SurveyController extends Controller
{
    public function index(Request $request)
    {
        $query = Survey::with('admin', 'questions');

        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->active();
            } else {
                $query->where('is_active', false);
            }
        }

        $surveys = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('admin.surveys.index', compact('surveys'));
    }

    public function create()
    {
        return view('admin.surveys.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'titre' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'date_limite' => ['nullable', 'date', 'after:today'],
            'questions' => ['required', 'array', 'min:1'],
            'questions.*.question' => ['required', 'string', 'max:500'],
            'questions.*.type' => ['required', 'in:text,choice,rating,yesno'],
            'questions.*.options' => ['nullable', 'array'],
            'questions.*.is_required' => ['boolean'],
        ]);

        $survey = Survey::create([
            'admin_id' => auth()->id(),
            'titre' => $request->titre,
            'description' => $request->description,
            'is_active' => true,
            'date_limite' => $request->date_limite,
        ]);

        foreach ($request->questions as $index => $questionData) {
            SurveyQuestion::create([
                'survey_id' => $survey->id,
                'question' => $questionData['question'],
                'type' => $questionData['type'],
                'options' => $questionData['options'] ?? null,
                'is_required' => $questionData['is_required'] ?? true,
                'ordre' => $index + 1,
            ]);
        }

        // Notifier tous les employés
        $employees = User::where('role', 'employee')->get();
        Notification::send($employees, new NewSurveyNotification($survey));

        return redirect()->route('admin.surveys.index')
            ->with('success', 'Sondage créé avec succès.');
    }

    public function show(Survey $survey)
    {
        $survey->load('questions.responses.user', 'admin');
        return view('admin.surveys.show', compact('survey'));
    }

    public function results(Survey $survey)
    {
        $survey->load('questions.responses.user');

        $statistics = [];
        foreach ($survey->questions as $question) {
            $statistics[$question->id] = $question->statistics;
        }

        // Comptage des employés pour le calcul du taux de réponse
        $totalEmployees = \App\Models\User::where('role', 'employee')->count();

        return view('admin.surveys.results', compact('survey', 'statistics', 'totalEmployees'));
    }

    public function toggle(Survey $survey)
    {
        $survey->update(['is_active' => !$survey->is_active]);

        $status = $survey->is_active ? 'activé' : 'désactivé';
        return redirect()->back()->with('success', "Sondage {$status} avec succès.");
    }

    public function destroy(Survey $survey)
    {
        $survey->delete();

        return redirect()->route('admin.surveys.index')
            ->with('success', 'Sondage supprimé avec succès.');
    }
}
