<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Training;
use App\Models\TrainingParticipant;
use App\Models\User;
use Illuminate\Http\Request;

class TrainingController extends Controller
{
    public function index(Request $request)
    {
        $query = Training::withCount(['participants as enrolled_count' => function ($q) {
            $q->where('status', '!=', 'cancelled');
        }]);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }
        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        $trainings = $query->latest()->paginate(12);

        return view('admin.trainings.index', compact('trainings'));
    }

    public function create()
    {
        $employees = User::where('role', 'employee')->orderBy('name')->get();
        return view('admin.trainings.create', compact('employees'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'nullable|string|max:50',
            'type' => 'required|in:interne,externe,en_ligne',
            'duration_hours' => 'nullable|numeric|min:0.5',
            'instructor' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'max_participants' => 'nullable|integer|min:1',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'status' => 'required|in:draft,published',
            'participants' => 'nullable|array',
            'participants.*' => 'exists:users,id',
        ]);

        $training = Training::create(array_merge($validated, [
            'created_by' => auth()->id(),
        ]));

        // Enroll selected participants
        if (!empty($validated['participants'])) {
            foreach ($validated['participants'] as $userId) {
                TrainingParticipant::create([
                    'training_id' => $training->id,
                    'user_id' => $userId,
                    'status' => 'enrolled',
                ]);
            }
        }

        return redirect()->route('admin.trainings.index')
            ->with('success', 'Formation créée avec succès.');
    }

    public function show(Training $training)
    {
        $training->load(['participants.user', 'creator']);
        return view('admin.trainings.show', compact('training'));
    }

    public function edit(Training $training)
    {
        $employees = User::where('role', 'employee')->orderBy('name')->get();
        $enrolledIds = $training->participants()->pluck('user_id')->toArray();
        return view('admin.trainings.edit', compact('training', 'employees', 'enrolledIds'));
    }

    public function update(Request $request, Training $training)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'nullable|string|max:50',
            'type' => 'required|in:interne,externe,en_ligne',
            'duration_hours' => 'nullable|numeric|min:0.5',
            'instructor' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'max_participants' => 'nullable|integer|min:1',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'status' => 'required|in:draft,published,archived',
        ]);

        $training->update($validated);

        return redirect()->route('admin.trainings.show', $training)
            ->with('success', 'Formation mise à jour.');
    }

    public function destroy(Training $training)
    {
        $training->delete();
        return redirect()->route('admin.trainings.index')
            ->with('success', 'Formation supprimée.');
    }

    public function markCompleted(Request $request, Training $training, User $user)
    {
        $participant = TrainingParticipant::where('training_id', $training->id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        $participant->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        return back()->with('success', $user->name . ' a terminé la formation.');
    }
}
