<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\Department;
use App\Models\Position;
use App\Models\User;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    public function index(Request $request)
    {
        $query = Announcement::with(['department', 'position', 'creator'])
            ->withCount('reads');

        // Filtres
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%'.$request->search.'%')
                    ->orWhere('content', 'like', '%'.$request->search.'%');
            });
        }

        $announcements = $query->orderByPriority()->paginate(15);

        // Stats
        $stats = [
            'total' => Announcement::count(),
            'active' => Announcement::where('is_active', true)->count(),
            'urgent' => Announcement::where('type', 'urgent')->where('is_active', true)->count(),
            'pinned' => Announcement::where('is_pinned', true)->where('is_active', true)->count(),
        ];

        return view('admin.announcements.index', compact('announcements', 'stats'));
    }

    public function create()
    {
        $departments = Department::orderBy('name')->get();
        $positions = Position::orderBy('name')->get();
        $employees = User::where('role', 'employee')
            ->orderBy('name')
            ->get(['id', 'name', 'email', 'department_id']);

        return view('admin.announcements.create', compact('departments', 'positions', 'employees'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string|max:10000',
            'type' => 'required|in:info,success,warning,urgent,event',
            'priority' => 'required|in:normal,high,critical',
            'target_type' => 'required|in:all,department,position,custom',
            'department_id' => 'required_if:target_type,department|nullable|exists:departments,id',
            'position_id' => 'required_if:target_type,position|nullable|exists:positions,id',
            'target_user_ids' => 'required_if:target_type,custom|nullable|array',
            'target_user_ids.*' => 'exists:users,id',
            'is_pinned' => 'boolean',
            'requires_acknowledgment' => 'boolean',
            'publish_at' => 'nullable|date',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $validated['created_by'] = auth()->id();
        $validated['is_active'] = true;
        $validated['is_pinned'] = $request->boolean('is_pinned');
        $validated['requires_acknowledgment'] = $request->boolean('requires_acknowledgment');

        // Nettoyer les champs selon le type de ciblage
        if ($validated['target_type'] !== 'department') {
            $validated['department_id'] = null;
        }
        if ($validated['target_type'] !== 'position') {
            $validated['position_id'] = null;
        }
        if ($validated['target_type'] !== 'custom') {
            $validated['target_user_ids'] = null;
        }

        $announcement = Announcement::create($validated);

        return redirect()->route('admin.announcements.index')
            ->with('success', 'Annonce "'.$announcement->title.'" créée avec succès.');
    }

    public function show(Announcement $announcement)
    {
        $announcement->load(['department', 'position', 'creator', 'reads.user']);

        // Statistiques
        $stats = [
            'total_target' => $announcement->target_users_count,
            'read_count' => $announcement->read_count,
            'read_percentage' => $announcement->read_percentage,
            'acknowledged_count' => $announcement->acknowledged_count,
            'acknowledged_percentage' => $announcement->acknowledged_percentage,
        ];

        // Utilisateurs qui ont lu
        $readUsers = $announcement->reads()
            ->with('user:id,name,email,avatar')
            ->orderBy('read_at', 'desc')
            ->get();

        // Utilisateurs qui n'ont pas lu
        $unreadUsers = $announcement->getUnreadUsers();

        return view('admin.announcements.show', compact('announcement', 'stats', 'readUsers', 'unreadUsers'));
    }

    public function edit(Announcement $announcement)
    {
        $departments = Department::orderBy('name')->get();
        $positions = Position::orderBy('name')->get();
        $employees = User::where('role', 'employee')
            ->orderBy('name')
            ->get(['id', 'name', 'email', 'department_id']);

        return view('admin.announcements.edit', compact('announcement', 'departments', 'positions', 'employees'));
    }

    public function update(Request $request, Announcement $announcement)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string|max:10000',
            'type' => 'required|in:info,success,warning,urgent,event',
            'priority' => 'required|in:normal,high,critical',
            'target_type' => 'required|in:all,department,position,custom',
            'department_id' => 'required_if:target_type,department|nullable|exists:departments,id',
            'position_id' => 'required_if:target_type,position|nullable|exists:positions,id',
            'target_user_ids' => 'required_if:target_type,custom|nullable|array',
            'target_user_ids.*' => 'exists:users,id',
            'is_pinned' => 'boolean',
            'requires_acknowledgment' => 'boolean',
            'publish_at' => 'nullable|date',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $validated['is_pinned'] = $request->boolean('is_pinned');
        $validated['requires_acknowledgment'] = $request->boolean('requires_acknowledgment');

        // Nettoyer les champs selon le type de ciblage
        if ($validated['target_type'] !== 'department') {
            $validated['department_id'] = null;
        }
        if ($validated['target_type'] !== 'position') {
            $validated['position_id'] = null;
        }
        if ($validated['target_type'] !== 'custom') {
            $validated['target_user_ids'] = null;
        }

        $announcement->update($validated);

        return redirect()->route('admin.announcements.index')
            ->with('success', 'Annonce mise à jour avec succès.');
    }

    public function destroy(Announcement $announcement)
    {
        $title = $announcement->title;
        $announcement->delete();

        return redirect()->route('admin.announcements.index')
            ->with('success', 'Annonce "'.$title.'" supprimée.');
    }

    /**
     * Toggle active status
     */
    public function toggle(Announcement $announcement)
    {
        $announcement->update(['is_active' => ! $announcement->is_active]);

        return response()->json([
            'success' => true,
            'is_active' => $announcement->is_active,
            'message' => $announcement->is_active ? 'Annonce activée' : 'Annonce désactivée',
        ]);
    }

    /**
     * Toggle pinned status
     */
    public function pin(Announcement $announcement)
    {
        $announcement->update(['is_pinned' => ! $announcement->is_pinned]);

        return response()->json([
            'success' => true,
            'is_pinned' => $announcement->is_pinned,
            'message' => $announcement->is_pinned ? 'Annonce épinglée' : 'Annonce désépinglée',
        ]);
    }
}
