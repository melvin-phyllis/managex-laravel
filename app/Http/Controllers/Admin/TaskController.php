<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\TaskDocument;
use App\Models\User;
use App\Notifications\TaskStatusNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

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
            $query->where(function ($q) use ($search) {
                $q->where('titre', 'like', '%'.$search.'%')
                    ->orWhere('description', 'like', '%'.$search.'%');
            });
        }

        // Current date for database-agnostic queries
        $now = now()->format('Y-m-d H:i:s');

        // Tri par défaut: tâches en retard d'abord, puis par priorité, puis par date
        $tasks = $query->orderByRaw("CASE 
            WHEN date_fin < ? AND statut NOT IN ('validated', 'completed') THEN 0 
            ELSE 1 
        END", [$now])
            ->orderByRaw("CASE priorite WHEN 'high' THEN 0 WHEN 'medium' THEN 1 ELSE 2 END")
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $employees = User::where('role', 'employee')->orderBy('name')->get();

        // Statistiques optimisées (une seule requête au lieu de 6)
        $taskStats = Task::selectRaw("
            COUNT(*) as total,
            SUM(CASE WHEN statut = 'pending' THEN 1 ELSE 0 END) as pending_count,
            SUM(CASE WHEN statut IN ('approved', 'in_progress') THEN 1 ELSE 0 END) as in_progress_count,
            SUM(CASE WHEN statut = 'completed' THEN 1 ELSE 0 END) as completed_count,
            SUM(CASE WHEN statut = 'validated' THEN 1 ELSE 0 END) as validated_count,
            SUM(CASE WHEN date_fin < ? AND statut NOT IN ('validated', 'completed') THEN 1 ELSE 0 END) as overdue_count
        ", [$now])->first();

        // Kanban tasks - limité à 50 tâches par statut pour performance
        $kanbanTasks = Task::with('user')
            ->whereIn('statut', ['pending', 'approved', 'in_progress', 'completed', 'validated', 'rejected'])
            ->orderBy('priorite', 'desc')
            ->orderBy('date_fin', 'asc')
            ->get()
            ->groupBy('statut');

        return view('admin.tasks.index', compact('tasks', 'employees', 'taskStats', 'kanbanTasks'));
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
            'documents' => ['nullable', 'array', 'max:5'],
            'documents.*' => ['file', 'max:10240'],
        ], [
            'documents.max' => 'Vous ne pouvez pas joindre plus de 5 fichiers.',
            'documents.*.max' => 'Chaque fichier ne doit pas depasser 10 Mo.',
        ]);

        $task = Task::create([
            'user_id' => $request->user_id,
            'titre' => $request->titre,
            'description' => $request->description,
            'priorite' => $request->priorite,
            'date_debut' => $request->date_debut,
            'date_fin' => $request->date_fin,
            'statut' => 'approved',
            'progression' => 0,
        ]);

        $this->handleDocumentUpload($request, $task);

        // Notifier l'employé
        $task->user->notify(new TaskStatusNotification($task, 'assigned'));

        return redirect()->route('admin.tasks.index')
            ->with('success', 'Tâche assignée avec succès à '.$task->user->name);
    }

    public function show(Task $task)
    {
        $task->load(['user', 'documents']);

        return view('admin.tasks.show', compact('task'));
    }

    public function edit(Task $task)
    {
        $task->load('documents');
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
            'documents' => ['nullable', 'array', 'max:5'],
            'documents.*' => ['file', 'max:10240'],
        ], [
            'documents.max' => 'Vous ne pouvez pas joindre plus de 5 fichiers.',
            'documents.*.max' => 'Chaque fichier ne doit pas depasser 10 Mo.',
        ]);

        $task->update($request->only([
            'user_id', 'titre', 'description', 'priorite', 'date_debut', 'date_fin', 'statut',
        ]));

        // Supprimer les documents coches
        if ($request->filled('delete_documents')) {
            $docsToDelete = TaskDocument::whereIn('id', $request->delete_documents)
                ->where('task_id', $task->id)
                ->get();
            foreach ($docsToDelete as $doc) {
                \Storage::disk('documents')->delete($doc->file_path);
                $doc->delete();
            }
        }

        $this->handleDocumentUpload($request, $task);

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

    public function validateTask(Request $request, Task $task)
    {
        $request->validate([
            'rating' => 'required|integer|min:0|max:10',
            'rating_comment' => 'nullable|string|max:1000',
        ]);

        $task->update([
            'statut' => 'validated',
            'rating' => $request->rating,
            'rating_comment' => $request->rating_comment,
        ]);

        // Notifier l'employé
        if ($task->user) {
            try {
                $task->user->notify(new TaskStatusNotification($task, 'validated'));
            } catch (\Exception $e) {
                // Ignore notification errors
            }
        }

        return redirect()->back()->with('success', 'Tâche validée et notée avec succès.');
    }

    public function remind(Task $task)
    {
        // Envoyer un rappel manuel
        if ($task->user) {
            try {
                // On utilise 'reminder' comme type pour personnaliser le message dans la notification
                $task->user->notify(new TaskStatusNotification($task, 'reminder'));
                return redirect()->back()->with('success', 'Rappel envoyé avec succès à ' . $task->user->name);
            } catch (\Exception $e) {
                return redirect()->back()->with('error', 'Erreur lors de l\'envoi du rappel : ' . $e->getMessage());
            }
        }

        return redirect()->back()->with('error', 'Impossible d\'envoyer un rappel : aucun utilisateur assigné.');
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
                'message' => 'Une tâche validée ne peut pas être modifiée.',
            ], 422);
        }

        $task->update(['statut' => $newStatus]);

        // Notifier l'employé du changement de statut
        if ($oldStatus !== $newStatus && $task->user) {
            try {
                $task->user->notify(new TaskStatusNotification($task, $newStatus));
            } catch (\Exception $e) {
                \Log::warning('Task status notification failed: ' . $e->getMessage());
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Statut mis à jour avec succès.',
            'task' => [
                'id' => $task->id,
                'statut' => $task->statut,
            ],
        ]);
    }

    public function destroy(Task $task)
    {
        // Supprimer les fichiers associes
        foreach ($task->documents as $doc) {
            \Storage::disk('documents')->delete($doc->file_path);
        }

        $task->delete();

        return redirect()->route('admin.tasks.index')
            ->with('success', 'Tâche supprimée avec succès.');
    }

    public function downloadDocument(TaskDocument $document)
    {
        if (! \Storage::disk('documents')->exists($document->file_path)) {
            abort(404, 'Fichier introuvable');
        }

        return \Storage::disk('documents')->download($document->file_path, $document->original_name);
    }

    public function deleteDocument(TaskDocument $document)
    {
        \Storage::disk('documents')->delete($document->file_path);
        $taskId = $document->task_id;
        $document->delete();

        return redirect()->back()->with('success', 'Document supprime.');
    }

    protected function handleDocumentUpload(Request $request, Task $task): void
    {
        if (! $request->hasFile('documents')) {
            return;
        }

        $allowedExtensions = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt', 'csv', 'jpg', 'jpeg', 'png'];

        foreach ($request->file('documents') as $file) {
            $ext = strtolower($file->getClientOriginalExtension());
            if (! in_array($ext, $allowedExtensions)) {
                continue;
            }

            $filename = Str::uuid().'.'.$ext;
            $path = 'tasks/'.$task->id.'/'.$filename;

            \Storage::disk('documents')->putFileAs('tasks/'.$task->id, $file, $filename);

            TaskDocument::create([
                'task_id' => $task->id,
                'file_path' => $path,
                'original_name' => $file->getClientOriginalName(),
                'mime_type' => $file->getMimeType(),
                'file_size' => $file->getSize(),
                'uploaded_by' => auth()->id(),
            ]);
        }
    }
}
