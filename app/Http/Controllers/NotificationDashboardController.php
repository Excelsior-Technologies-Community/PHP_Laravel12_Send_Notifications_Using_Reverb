<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\Post;
use Illuminate\Http\JsonResponse;

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
            'notification-dashboard'
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

        $totalPosts = Post::count();

        $totalNotifications =
            Notification::count();

        $unreadNotifications =
            Notification::where(
                'is_read',
                false
            )->count();

        $readNotifications =
            Notification::where(
                'is_read',
                true
            )->count();

        $todayPosts =
            Post::whereDate(
                'created_at',
                today()
            )->count();

        $todayNotifications =
            Notification::whereDate(
                'created_at',
                today()
            )->count();

        $last24Hours =
            Notification::where(
                'created_at',
                '>=',
                now()->subDay()
            )->count();

        $last7Days =
            Notification::where(
                'created_at',
                '>=',
                now()->subDays(7)
            )->count();

        $readPercentage =
            $totalNotifications > 0
                ? round(
                    ($readNotifications /
                        $totalNotifications) * 100,
                    1
                )
                : 0;

        $recentNotifications =
            Notification::with([
                'user',
                'post',
            ])
                ->oldest()
                ->take(5)
                ->get()
                ->map(function ($notification) {

                    return [
                        'id' =>
                            $notification->id,

                        'title' =>
                            $notification->title,

                        'message' =>
                            $notification->message,

                        'user' =>
                            $notification->user?->name
                            ?? 'Unknown',

                        'is_read' =>
                            $notification->is_read,

                        'created_at' =>
                            $notification
                                ->created_at
                                ?->format(
                                    'd M Y, h:i A'
                                ),
                    ];
                });

        return response()->json([
            'total_posts' =>
                $totalPosts,

            'total_notifications' =>
                $totalNotifications,

            'unread_notifications' =>
                $unreadNotifications,

            'read_notifications' =>
                $readNotifications,

            'today_posts' =>
                $todayPosts,

            'today_notifications' =>
                $todayNotifications,

            'last_24_hours' =>
                $last24Hours,

            'last_7_days' =>
                $last7Days,

            'read_percentage' =>
                $readPercentage,

            'reverb_status' =>
                'connected',

            'recent_notifications' =>
                $recentNotifications,
        ]);
    }
}