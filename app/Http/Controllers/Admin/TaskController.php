<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\User;
use App\Notifications\TaskStatusNotification;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        $query = Task::with('user');

        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('priorite')) {
            $query->where('priorite', $request->priorite);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('titre', 'like', '%' . $search . '%')
                  ->orWhere('description', 'like', '%' . $search . '%');
            });
        }

        // Tri par défaut: tâches en retard d'abord, puis par priorité, puis par date
        $tasks = $query->orderByRaw("CASE 
            WHEN date_fin < NOW() AND statut NOT IN ('validated', 'completed') THEN 0 
            ELSE 1 
        END")
        ->orderByRaw("CASE priorite WHEN 'high' THEN 0 WHEN 'medium' THEN 1 ELSE 2 END")
        ->orderBy('created_at', 'desc')
        ->paginate(15);
        
        $employees = User::where('role', 'employee')->orderBy('name')->get();

        return view('admin.tasks.index', compact('tasks', 'employees'));
    }

    public function create()
    {
        $employees = User::where('role', 'employee')->orderBy('name')->get();
        return view('admin.tasks.create', compact('employees'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'titre' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'priorite' => ['required', 'in:low,medium,high'],
            'date_debut' => ['nullable', 'date'],
            'date_fin' => ['nullable', 'date', 'after_or_equal:date_debut'],
        ]);

        $task = Task::create([
            'user_id' => $request->user_id,
            'titre' => $request->titre,
            'description' => $request->description,
            'priorite' => $request->priorite,
            'date_debut' => $request->date_debut,
            'date_fin' => $request->date_fin,
            'statut' => 'approved', // Directement approuvée car assignée par l'admin
            'progression' => 0,
        ]);

        // Notifier l'employé
        $task->user->notify(new TaskStatusNotification($task, 'assigned'));

        return redirect()->route('admin.tasks.index')
            ->with('success', 'Tâche assignée avec succès à ' . $task->user->name);
    }

    public function show(Task $task)
    {
        $task->load('user');
        return view('admin.tasks.show', compact('task'));
    }

    public function edit(Task $task)
    {
        $employees = User::where('role', 'employee')->orderBy('name')->get();
        return view('admin.tasks.edit', compact('task', 'employees'));
    }

    public function update(Request $request, Task $task)
    {
        $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'titre' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'priorite' => ['required', 'in:low,medium,high'],
            'date_debut' => ['nullable', 'date'],
            'date_fin' => ['nullable', 'date', 'after_or_equal:date_debut'],
            'statut' => ['required', 'in:pending,approved,rejected,completed,validated'],
        ]);

        $task->update($request->only([
            'user_id', 'titre', 'description', 'priorite', 'date_debut', 'date_fin', 'statut'
        ]));

        return redirect()->route('admin.tasks.index')
            ->with('success', 'Tâche mise à jour avec succès.');
    }

    public function approve(Task $task)
    {
        $task->update(['statut' => 'approved']);

        // Envoyer notification
        $task->user->notify(new TaskStatusNotification($task, 'approved'));

        return redirect()->back()->with('success', 'Tâche approuvée avec succès.');
    }

    public function reject(Request $request, Task $task)
    {
        $task->update(['statut' => 'rejected']);

        // Envoyer notification
        $task->user->notify(new TaskStatusNotification($task, 'rejected'));

        return redirect()->back()->with('success', 'Tâche rejetée.');
    }

    public function validate(Task $task)
    {
        if ($task->statut !== 'completed') {
            return redirect()->back()->with('error', 'Seules les tâches terminées peuvent être validées.');
        }

        $task->update(['statut' => 'validated']);

        // Envoyer notification
        $task->user->notify(new TaskStatusNotification($task, 'validated'));

        return redirect()->back()->with('success', 'Tâche validée avec succès.');
    }

    /**
     * Update task status via AJAX (for Kanban drag & drop)
     */
    public function updateStatus(Request $request, Task $task)
    {
        $request->validate([
            'statut' => ['required', 'in:pending,approved,rejected,completed,validated,in_progress'],
        ]);

        $oldStatus = $task->statut;
        $newStatus = $request->statut;

        // Validation des transitions de statut
        $allowedTransitions = [
            'pending' => ['approved', 'rejected'],
            'approved' => ['in_progress', 'completed', 'pending'],
            'in_progress' => ['completed', 'approved'],
            'completed' => ['validated', 'approved', 'in_progress'],
            'validated' => [], // Tâche finale, pas de retour
            'rejected' => ['pending', 'approved'],
        ];

        // Admin peut faire toutes les transitions sauf depuis validated
        if ($oldStatus === 'validated' && $newStatus !== 'validated') {
            return response()->json([
                'success' => false,
                'message' => 'Une tâche validée ne peut pas être modifiée.'
            ], 422);
        }

        $task->update(['statut' => $newStatus]);

        // Notifier l'employé du changement de statut
        if ($oldStatus !== $newStatus) {
            $task->user->notify(new TaskStatusNotification($task, $newStatus));
        }

        return response()->json([
            'success' => true,
            'message' => 'Statut mis à jour avec succès.',
            'task' => [
                'id' => $task->id,
                'statut' => $task->statut
            ]
        ]);
    }

    public function destroy(Task $task)
    {
        $task->delete();

        return redirect()->route('admin.tasks.index')
            ->with('success', 'Tâche supprimée avec succès.');
    }
}
