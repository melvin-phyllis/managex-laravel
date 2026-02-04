<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Leave;
use Illuminate\Http\Request;

class LeaveController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $query = $user->leaves();

        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $leaves = $query->orderBy('created_at', 'desc')->paginate(10);

        // Statistiques
        $stats = [
            'total' => $user->leaves()->count(),
            'pending' => $user->leaves()->pending()->count(),
            'approved' => $user->leaves()->approved()->count(),
            'total_days_approved' => $user->leaves()
                ->approved()
                ->get()
                ->sum('duree'),
        ];

        return view('employee.leaves.index', compact('leaves', 'stats'));
    }

    public function create()
    {
        return view('employee.leaves.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'type' => ['required', 'in:conge,maladie,autre'],
            'date_debut' => ['required', 'date', 'after_or_equal:today'],
            'date_fin' => ['required', 'date', 'after_or_equal:date_debut'],
            'motif' => ['nullable', 'string', 'max:1000'],
        ]);

        auth()->user()->leaves()->create([
            'type' => $request->type,
            'date_debut' => $request->date_debut,
            'date_fin' => $request->date_fin,
            'motif' => $request->motif,
            'statut' => 'pending',
        ]);

        return redirect()->route('employee.leaves.index')
            ->with('success', 'Demande de congé envoyée avec succès.');
    }

    public function show(Leave $leave)
    {
        $this->authorize('view', $leave);

        return view('employee.leaves.show', compact('leave'));
    }

    public function destroy(Leave $leave)
    {
        $this->authorize('delete', $leave);

        if ($leave->statut !== 'pending') {
            return redirect()->back()
                ->with('error', 'Vous ne pouvez annuler que les demandes en attente.');
        }

        $leave->delete();

        return redirect()->route('employee.leaves.index')
            ->with('success', 'Demande de congé annulée.');
    }
}
