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

        return view('admin.leaves.index', compact('leaves', 'employees'));
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
