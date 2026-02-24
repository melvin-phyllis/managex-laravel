<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Training;
use App\Models\TrainingParticipant;
use Illuminate\Http\Request;

class TrainingController extends Controller
{
    /**
     * Catalogue de formations disponibles + historique.
     */
    public function index()
    {
        $user = auth()->user();

        // Formations publiées à venir
        $availableTrainings = Training::published()
            ->where(function ($q) {
                $q->whereNull('start_date')
                  ->orWhere('start_date', '>=', now());
            })
            ->withCount(['participants as enrolled_count' => fn ($q) => $q->where('status', '!=', 'cancelled')])
            ->latest()
            ->get();

        // Mes inscriptions
        $myTrainings = TrainingParticipant::where('user_id', $user->id)
            ->with('training')
            ->latest()
            ->get();

        $enrolledIds = $myTrainings->pluck('training_id')->toArray();

        // Stats
        $completedCount = $myTrainings->where('status', 'completed')->count();
        $enrolledCount = $myTrainings->where('status', 'enrolled')->count();
        $totalHours = $myTrainings->where('status', 'completed')
            ->sum(fn ($p) => $p->training->duration_hours ?? 0);

        return view('employee.trainings.index', compact(
            'availableTrainings', 'myTrainings', 'enrolledIds',
            'completedCount', 'enrolledCount', 'totalHours'
        ));
    }

    /**
     * S'inscrire à une formation.
     */
    public function enroll(Training $training)
    {
        $user = auth()->user();

        // Already enrolled?
        if (TrainingParticipant::where('training_id', $training->id)->where('user_id', $user->id)->exists()) {
            return back()->with('error', 'Vous êtes déjà inscrit à cette formation.');
        }

        // Full?
        if ($training->is_full) {
            return back()->with('error', 'Cette formation est complète.');
        }

        TrainingParticipant::create([
            'training_id' => $training->id,
            'user_id' => $user->id,
            'status' => 'enrolled',
        ]);

        return back()->with('success', 'Inscription confirmée pour : ' . $training->title);
    }

    /**
     * Se désinscrire.
     */
    public function unenroll(Training $training)
    {
        TrainingParticipant::where('training_id', $training->id)
            ->where('user_id', auth()->id())
            ->where('status', 'enrolled')
            ->delete();

        return back()->with('success', 'Désinscription effectuée.');
    }
}
