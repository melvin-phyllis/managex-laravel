<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;

class NotificationController extends Controller
{
    /**
     * Get all notifications for authenticated user
     */
    public function index(): JsonResponse
    {
        $notifications = auth()->user()->notifications()
            ->latest()
            ->take(20)
            ->get()
            ->map(function ($notification) {
                return [
                    'id' => $notification->id,
                    'type' => $notification->data['type'] ?? 'general',
                    'message' => $notification->data['message'] ?? '',
                    'url' => $notification->data['url'] ?? '#',
                    'read_at' => $notification->read_at,
                    'created_at' => $notification->created_at->diffForHumans(),
                ];
            });

        return response()->json([
            'notifications' => $notifications,
            'unread_count' => auth()->user()->unreadNotifications()->count(),
        ]);
    }

    /**
     * Mark a notification as read
     */
    public function markAsRead(string $id): JsonResponse
    {
        $notification = auth()->user()->notifications()->findOrFail($id);
        $notification->markAsRead();

        return response()->json([
            'success' => true,
            'unread_count' => auth()->user()->unreadNotifications()->count(),
        ]);
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead(): JsonResponse
    {
        auth()->user()->unreadNotifications->markAsRead();

        return response()->json([
            'success' => true,
            'unread_count' => 0,
        ]);
    }
}
