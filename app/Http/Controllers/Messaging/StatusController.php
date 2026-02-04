<?php

namespace App\Http\Controllers\Messaging;

use App\Http\Controllers\Controller;
use App\Models\Messaging\UserStatus;
use Illuminate\Http\Request;

class StatusController extends Controller
{
    /**
     * Get current user's status
     */
    public function show()
    {
        $user = auth()->user();
        $status = UserStatus::firstOrCreate(
            ['user_id' => $user->id],
            ['status' => 'online', 'last_seen_at' => now()]
        );

        return response()->json([
            'status' => $status->status,
            'custom_message' => $status->custom_message,
            'until' => $status->until?->toIso8601String(),
            'last_seen_at' => $status->last_seen_at?->diffForHumans(),
        ]);
    }

    /**
     * Update current user's status
     */
    public function update(Request $request)
    {
        $request->validate([
            'status' => 'required|in:online,away,busy,dnd,offline',
            'custom_message' => 'nullable|string|max:255',
            'until' => 'nullable|date|after:now',
        ]);

        $user = auth()->user();

        $status = UserStatus::updateOrCreate(
            ['user_id' => $user->id],
            [
                'status' => $request->status,
                'custom_message' => $request->custom_message,
                'until' => $request->until,
                'last_seen_at' => now(),
            ]
        );

        return response()->json([
            'status' => $status->status,
            'status_label' => $status->status_label,
            'status_color' => $status->status_color,
            'custom_message' => $status->custom_message,
        ]);
    }

    /**
     * Get status of multiple users
     */
    public function batch(Request $request)
    {
        $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'integer|exists:users,id',
        ]);

        $statuses = UserStatus::whereIn('user_id', $request->user_ids)
            ->get()
            ->keyBy('user_id')
            ->map(fn ($s) => [
                'status' => $s->status,
                'status_label' => $s->status_label,
                'status_color' => $s->status_color,
                'custom_message' => $s->custom_message,
                'last_seen_at' => $s->last_seen_at?->diffForHumans(),
            ]);

        return response()->json($statuses);
    }

    /**
     * Heartbeat - keep user online
     */
    public function heartbeat()
    {
        $user = auth()->user();

        UserStatus::updateOrCreate(
            ['user_id' => $user->id],
            [
                'status' => 'online',
                'last_seen_at' => now(),
            ]
        );

        return response()->json(['success' => true]);
    }

    /**
     * Set user offline
     */
    public function offline()
    {
        $user = auth()->user();

        UserStatus::where('user_id', $user->id)->update([
            'status' => 'offline',
            'last_seen_at' => now(),
        ]);

        return response()->json(['success' => true]);
    }
}
