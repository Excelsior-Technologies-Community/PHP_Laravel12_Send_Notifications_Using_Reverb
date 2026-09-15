<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class NotificationDashboardController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Dashboard
    |--------------------------------------------------------------------------
    */

    public function index()
    {
        abort_unless(
            auth()->user()->is_admin,
            403
        );

        return view(
            'notification-dashboard',
            [
                'userId' => auth()->id(),
            ]
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Dashboard Statistics
    |--------------------------------------------------------------------------
    */

    public function stats(): JsonResponse
    {
        abort_unless(
            auth()->user()->is_admin,
            403
        );

        $totalNotifications =
            Notification::count();

        $readNotifications =
            Notification::where(
                'is_read',
                true
            )->count();

        $onlineAdmins = $this->getOnlineAdmins();

        return response()->json([
            'total_posts' => Post::count(),

            'total_notifications' => $totalNotifications,

            'unread_notifications' => Notification::where(
                'is_read',
                false
            )->count(),

            'read_notifications' => $readNotifications,

            'today_posts' => Post::whereDate(
                'created_at',
                today()
            )->count(),

            'today_notifications' => Notification::whereDate(
                'created_at',
                today()
            )->count(),

            'last_24_hours' => Notification::where(
                'created_at',
                '>=',
                now()->subDay()
            )->count(),

            'last_7_days' => Notification::where(
                'created_at',
                '>=',
                now()->subDays(7)
            )->count(),

            'read_percentage' => $totalNotifications > 0
                    ? round(
                        ($readNotifications /
                            $totalNotifications) * 100,
                        1
                    )
                    : 0,

            'reverb_status' => 'connected',

            'online_admins' => $onlineAdmins,

            'recent_notifications' => Notification::with([
                'user',
                'post',
            ])
                ->oldest()
                ->take(5)
                ->get()
                ->map(function ($notification) {

                    return [
                        'id' => $notification->id,

                        'title' => $notification->title,

                        'message' => $notification->message,

                        'user' => $notification->user?->name
                            ?? 'Unknown',

                        'is_read' => $notification->is_read,

                        'created_at' => $notification
                            ->created_at
                            ?->format(
                                'd M Y, h:i A'
                            ),
                    ];
                }),
        ]);
    }

    public function onlineAdmins(): JsonResponse
    {
        return response()->json([
            'online_admins' => $this->getOnlineAdmins(),
        ]);
    }

    private function getOnlineAdmins(): array
    {
        $adminIds = User::where('is_admin', true)
            ->pluck('id')
            ->all();

        $onlineKeys = array_map(
            fn ($id) => 'user-online-'.$id,
            $adminIds
        );

        $onlineUsers = User::whereIn('id', $adminIds)
            ->get()
            ->filter(function ($user) {
                return Cache::has('user-online-'.$user->id);
            })
            ->values();

        return $onlineUsers->map(function ($user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ];
        })->toArray();
    }
}
