<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\JsonResponse;

class NotificationController extends Controller
{
    /**
     * Get notifications for authenticated user.
     */
    public function index(): JsonResponse
    {
        $notifications = Notification::where('user_id', auth()->id())
            ->with('post')
            ->latest()
            ->take(20)
            ->get();

        $unreadCount = Notification::where('user_id', auth()->id())
            ->where('is_read', false)
            ->count();

        return response()->json([
            'notifications' => $notifications,
            'unread_count' => $unreadCount,
        ]);
    }

    /**
     * Mark one notification as read.
     */
    public function markAsRead(Notification $notification): JsonResponse
    {
        abort_unless(
            $notification->user_id === auth()->id(),
            403
        );

        $notification->update([
            'is_read' => true,
            'read_at' => now(),
        ]);

        $unreadCount = Notification::where('user_id', auth()->id())
            ->where('is_read', false)
            ->count();

        return response()->json([
            'success' => true,
            'unread_count' => $unreadCount,
        ]);
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllAsRead(): JsonResponse
    {
        Notification::where('user_id', auth()->id())
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);

        return response()->json([
            'success' => true,
            'unread_count' => 0,
        ]);
    }

    /**
     * Delete all notifications for authenticated user.
     */
    public function clear(): JsonResponse
    {
        Notification::where('user_id', auth()->id())->delete();

        return response()->json([
            'success' => true,
        ]);
    }
}