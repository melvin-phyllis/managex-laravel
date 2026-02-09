<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $query = $user->tasks();

        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        if ($request->filled('priorite')) {
            $query->where('priorite', $request->priorite);
        }

        $tasks = $query->orderBy('created_at', 'desc')->paginate(10);

        // Statistiques
        $stats = [
            'total' => $user->tasks()->count(),
            'pending' => $user->tasks()->pending()->count(),
            'approved' => $user->tasks()->approved()->count(),
            'completed' => $user->tasks()->completed()->count(),
        ];

        return view('employee.tasks.index', compact('tasks', 'stats'));
    }

    public function create()
    {
        return view('employee.tasks.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'titre' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'date_debut' => ['nullable', 'date'],
            'date_fin' => ['nullable', 'date', 'after_or_equal:date_debut'],
            'priorite' => ['required', 'in:low,medium,high'],
        ]);

        auth()->user()->tasks()->create([
            'titre' => $request->titre,
            'description' => $request->description,
            'date_debut' => $request->date_debut,
            'date_fin' => $request->date_fin,
            'priorite' => $request->priorite,
            'statut' => 'pending',
            'progression' => 0,
        ]);

        return redirect()->route('employee.tasks.index')
            ->with('success', 'Tâche créée avec succès. Elle est en attente de validation.');
    }

    public function show(Task $task)
    {
        $this->authorize('view', $task);
        $task->load('documents');

        return view('employee.tasks.show', compact('task'));
    }

    public function edit(Task $task)
    {
        $this->authorize('update', $task);

        if ($task->statut !== 'approved') {
            return redirect()->back()
                ->with('error', 'Vous ne pouvez modifier que les tâches approuvées.');
        }

        return view('employee.tasks.edit', compact('task'));
    }

    public function update(Request $request, Task $task)
    {
        $this->authorize('update', $task);

        if ($task->statut !== 'approved') {
            return redirect()->back()
                ->with('error', 'Vous ne pouvez modifier que les tâches approuvées.');
        }

        $request->validate([
            'progression' => ['required', 'integer', 'min:0', 'max:100'],
        ]);

        $task->update([
            'progression' => $request->progression,
            'statut' => $request->progression >= 100 ? 'completed' : 'approved',
        ]);

        return redirect()->route('employee.tasks.index')
            ->with('success', 'Progression mise à jour avec succès.');
    }

    public function updateProgress(Request $request, Task $task)
    {
        try {
            $this->authorize('update', $task);
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return response()->json(['success' => false, 'error' => 'Non autorisé : Cette tâche ne vous appartient pas.'], 403);
        }

        if (! in_array($task->statut, ['approved', 'completed'])) {
            return response()->json(['success' => false, 'error' => 'Action non autorisée : Statut incorrect (' . $task->statut . ')'], 403);
        }

        $request->validate([
            'progression' => ['required', 'integer', 'min:0', 'max:100'],
        ]);

        $task->update([
            'progression' => $request->progression,
            'statut' => $request->progression >= 100 ? 'completed' : 'approved',
        ]);

        return response()->json([
            'success' => true,
            'progression' => $task->progression,
            'statut' => $task->statut,
        ]);
    }
}
