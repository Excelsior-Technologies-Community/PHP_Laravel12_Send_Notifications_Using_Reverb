<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Notification Center
    |--------------------------------------------------------------------------
    */

    public function index(Request $request): JsonResponse
    {
        $query = Notification::where(
            'user_id',
            auth()->id()
        )
            ->with('post')
            ->latest();

        /*
        |--------------------------------------------------------------------------
        | Read / Unread Filter
        |--------------------------------------------------------------------------
        */

        if ($request->filled('status')) {

            if ($request->status === 'unread') {

                $query->where(
                    'is_read',
                    false
                );
            }

            if ($request->status === 'read') {

                $query->where(
                    'is_read',
                    true
                );
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Search Notifications
        |--------------------------------------------------------------------------
        */

        if ($request->filled('search')) {

            $search = trim(
                $request->search
            );

            $query->where(function ($q) use ($search) {

                $q->where(
                    'title',
                    'like',
                    "%{$search}%"
                )
                    ->orWhere(
                        'message',
                        'like',
                        "%{$search}%"
                    );
            });
        }

        $notifications = $query
            ->paginate(5)
            ->withQueryString();

        $unreadCount = Notification::where(
            'user_id',
            auth()->id()
        )
            ->where(
                'is_read',
                false
            )
            ->count();

        return response()->json([
            'notifications' => $notifications,
            'unread_count' => $unreadCount,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Mark Single Read
    |--------------------------------------------------------------------------
    */

    public function markAsRead(
        Notification $notification
    ): JsonResponse {

        $this->authorizeNotification(
            $notification
        );

        $notification->markAsRead();

        return $this->countsResponse();
    }

    /*
    |--------------------------------------------------------------------------
    | Mark Single Unread
    |--------------------------------------------------------------------------
    */

    public function markAsUnread(
        Notification $notification
    ): JsonResponse {

        $this->authorizeNotification(
            $notification
        );

        $notification->markAsUnread();

        return $this->countsResponse();
    }

    /*
    |--------------------------------------------------------------------------
    | Mark All Read
    |--------------------------------------------------------------------------
    */

    public function markAllAsRead(): JsonResponse
    {
        Notification::where(
            'user_id',
            auth()->id()
        )
            ->where(
                'is_read',
                false
            )
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);

        return response()->json([
            'success' => true,
            'unread_count' => 0,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Delete Single
    |--------------------------------------------------------------------------
    */

    public function destroy(
        Notification $notification
    ): JsonResponse {

        $this->authorizeNotification(
            $notification
        );

        $notification->delete();

        return response()->json([
            'success' => true,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Delete All
    |--------------------------------------------------------------------------
    */

    public function clear(): JsonResponse
    {
        Notification::where(
            'user_id',
            auth()->id()
        )->delete();

        return response()->json([
            'success' => true,
            'unread_count' => 0,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    private function authorizeNotification(
        Notification $notification
    ): void {

        abort_unless(
            $notification->user_id === auth()->id(),
            403
        );
    }

    private function countsResponse(): JsonResponse
    {
        $unreadCount = Notification::where(
            'user_id',
            auth()->id()
        )
            ->where(
                'is_read',
                false
            )
            ->count();

        return response()->json([
            'success' => true,
            'unread_count' => $unreadCount,
        ]);
    }
}