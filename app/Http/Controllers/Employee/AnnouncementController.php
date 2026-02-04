<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $filter = $request->get('filter', 'all');

        $query = Announcement::published()
            ->forUser($user)
            ->with(['department', 'position', 'creator'])
            ->orderByPriority();

        // Filtres
        if ($filter === 'unread') {
            $query->unreadBy($user);
        } elseif ($filter === 'acknowledgment') {
            $query->where('requires_acknowledgment', true)
                ->whereDoesntHave('reads', function ($q) use ($user) {
                    $q->where('user_id', $user->id)
                        ->whereNotNull('acknowledged_at');
                });
        }

        $announcements = $query->paginate(15);

        // Ajouter le statut de lecture pour chaque annonce
        $announcements->each(function ($announcement) use ($user) {
            $announcement->is_read = $announcement->isReadBy($user);
            $announcement->is_acknowledged = $announcement->isAcknowledgedBy($user);
        });

        // Stats
        $stats = [
            'total' => Announcement::published()->forUser($user)->count(),
            'unread' => Announcement::published()->forUser($user)->unreadBy($user)->count(),
            'pending_ack' => Announcement::published()
                ->forUser($user)
                ->where('requires_acknowledgment', true)
                ->whereDoesntHave('reads', function ($q) use ($user) {
                    $q->where('user_id', $user->id)
                        ->whereNotNull('acknowledged_at');
                })->count(),
        ];

        return view('employee.announcements.index', compact('announcements', 'stats', 'filter'));
    }

    public function show(Announcement $announcement)
    {
        $user = auth()->user();

        // Vérifier que l'utilisateur peut voir cette annonce
        $canView = Announcement::published()
            ->forUser($user)
            ->where('id', $announcement->id)
            ->exists();

        if (! $canView) {
            abort(403, 'Vous n\'avez pas accès à cette annonce.');
        }

        // Marquer comme lu si pas déjà lu
        if (! $announcement->isReadBy($user)) {
            $announcement->markAsReadBy($user);
            $announcement->increment('view_count');
        }

        $announcement->load(['department', 'position', 'creator']);
        $announcement->is_read = true;
        $announcement->is_acknowledged = $announcement->isAcknowledgedBy($user);

        return view('employee.announcements.show', compact('announcement'));
    }

    /**
     * Mark announcement as read (AJAX)
     */
    public function markAsRead(Announcement $announcement)
    {
        $user = auth()->user();

        // Vérifier l'accès
        $canView = Announcement::published()
            ->forUser($user)
            ->where('id', $announcement->id)
            ->exists();

        if (! $canView) {
            return response()->json(['error' => 'Accès refusé'], 403);
        }

        $read = $announcement->markAsReadBy($user);

        return response()->json([
            'success' => true,
            'message' => 'Annonce marquée comme lue',
            'read_at' => $read->read_at->format('d/m/Y H:i'),
        ]);
    }

    /**
     * Acknowledge announcement (AJAX)
     */
    public function acknowledge(Announcement $announcement)
    {
        $user = auth()->user();

        // Vérifier l'accès
        $canView = Announcement::published()
            ->forUser($user)
            ->where('id', $announcement->id)
            ->exists();

        if (! $canView) {
            return response()->json(['error' => 'Accès refusé'], 403);
        }

        if (! $announcement->requires_acknowledgment) {
            return response()->json(['error' => 'Cette annonce ne nécessite pas d\'accusé'], 400);
        }

        $announcement->acknowledgeBy($user);

        return response()->json([
            'success' => true,
            'message' => 'Accusé de réception enregistré',
            'acknowledged_at' => now()->format('d/m/Y H:i'),
        ]);
    }
}
