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

        if ($request->filled('search')) {
            $query->where('titre', 'like', '%' . $request->search . '%');
        }

        $tasks = $query->orderBy('created_at', 'desc')->paginate(15);
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

    public function destroy(Task $task)
    {
        $task->delete();

        return redirect()->route('admin.tasks.index')
            ->with('success', 'Tâche supprimée avec succès.');
    }
}
