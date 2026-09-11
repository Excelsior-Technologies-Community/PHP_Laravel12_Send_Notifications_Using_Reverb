<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\Post;
use Illuminate\Http\JsonResponse;

class NotificationDashboardController extends Controller
{
    /**
     * Display dashboard.
     */
    public function index()
    {
        abort_unless(auth()->user()->is_admin, 403);

        return view('notification-dashboard');
    }

    /**
     * Get real-time statistics.
     */
    public function stats(): JsonResponse
    {
        abort_unless(auth()->user()->is_admin, 403);

        $totalPosts = Post::count();

        $totalNotifications = Notification::count();

        $unreadNotifications = Notification::where('is_read', false)
            ->count();

        $todayPosts = Post::whereDate('created_at', today())
            ->count();

        return response()->json([
            'total_posts' => $totalPosts,
            'total_notifications' => $totalNotifications,
            'unread_notifications' => $unreadNotifications,
            'today_posts' => $todayPosts,
            'reverb_status' => 'connected',
        ]);
    }
}