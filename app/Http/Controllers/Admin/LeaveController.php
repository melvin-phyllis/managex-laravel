<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Leave;
use App\Notifications\LeaveStatusNotification;
use Illuminate\Http\Request;

class LeaveController extends Controller
{
    public function index(Request $request)
    {
        $query = Leave::with('user');

        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $leaves = $query->orderBy('created_at', 'desc')->paginate(15);
        $employees = \App\Models\User::where('role', 'employee')->orderBy('name')->get();

        // Statistiques pour le dashboard (une seule requête optimisée)
        $stats = Leave::selectRaw("
            COUNT(*) as total,
            SUM(CASE WHEN statut = 'pending' THEN 1 ELSE 0 END) as pending_count,
            SUM(CASE WHEN statut = 'approved' THEN 1 ELSE 0 END) as approved_count,
            SUM(CASE WHEN statut = 'rejected' THEN 1 ELSE 0 END) as rejected_count
        ")->first();

        $pendingCount = $stats->pending_count ?? 0;
        $approvedCount = $stats->approved_count ?? 0;
        $rejectedCount = $stats->rejected_count ?? 0;
        $totalCount = $stats->total ?? 0;

        return view('admin.leaves.index', compact(
            'leaves', 
            'employees', 
            'pendingCount', 
            'approvedCount', 
            'rejectedCount', 
            'totalCount'
        ));
    }

    public function show(Leave $leave)
    {
        $leave->load('user');
        return view('admin.leaves.show', compact('leave'));
    }

    public function approve(Request $request, Leave $leave)
    {
        $request->validate([
            'commentaire_admin' => ['nullable', 'string', 'max:500'],
        ]);

        $leave->update([
            'statut' => 'approved',
            'commentaire_admin' => $request->commentaire_admin,
        ]);

        // Envoyer notification
        $leave->user->notify(new LeaveStatusNotification($leave, 'approved'));

        return redirect()->back()->with('success', 'Demande de congé approuvée.');
    }

    public function reject(Request $request, Leave $leave)
    {
        $request->validate([
            'commentaire_admin' => ['nullable', 'string', 'max:500'],
        ]);

        $leave->update([
            'statut' => 'rejected',
            'commentaire_admin' => $request->commentaire_admin,
        ]);

        // Envoyer notification
        $leave->user->notify(new LeaveStatusNotification($leave, 'rejected'));

        return redirect()->back()->with('success', 'Demande de congé refusée.');
    }

    public function destroy(Leave $leave)
    {
        $leave->delete();

        return redirect()->route('admin.leaves.index')
            ->with('success', 'Demande de congé supprimée.');
    }
}
