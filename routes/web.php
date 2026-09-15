<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\NotificationDashboardController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

/*
|--------------------------------------------------------------------------
| Home
|--------------------------------------------------------------------------
*/

Route::get(
    '/home',
    [HomeController::class, 'index']
)->name('home');

/*
|--------------------------------------------------------------------------
| Posts
|--------------------------------------------------------------------------
*/

Route::get(
    '/posts',
    [PostController::class, 'index']
)->name('posts.index');

Route::post(
    '/posts',
    [PostController::class, 'store']
)
    ->middleware('auth')
    ->name('posts.store');

Route::get(
    '/posts/{post}/edit',
    [PostController::class, 'edit']
)
    ->middleware('auth')
    ->name('posts.edit');

Route::put(
    '/posts/{post}',
    [PostController::class, 'update']
)
    ->middleware('auth')
    ->name('posts.update');

Route::delete(
    '/posts/{post}',
    [PostController::class, 'destroy']
)
    ->middleware('auth')
    ->name('posts.destroy');

/*
|--------------------------------------------------------------------------
| Notifications
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    Route::get(
        '/notifications',
        [NotificationController::class, 'index']
    )->name('notifications.index');

    Route::post(
        '/notifications/{notification}/read',
        [NotificationController::class, 'markAsRead']
    )->name('notifications.read');

    Route::post(
        '/notifications/{notification}/unread',
        [NotificationController::class, 'markAsUnread']
    )->name('notifications.unread');

    Route::post(
        '/notifications/read-all',
        [NotificationController::class, 'markAllAsRead']
    )->name('notifications.read-all');

    Route::delete(
        '/notifications/{notification}',
        [NotificationController::class, 'destroy']
    )->name('notifications.destroy');

    Route::delete(
        '/notifications/clear',
        [NotificationController::class, 'clear']
    )->name('notifications.clear');

    /*
    |--------------------------------------------------------------------------
    | Admin Dashboard
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/notification-dashboard',
        [NotificationDashboardController::class, 'index']
    )->name('notification.dashboard');

    Route::get(
        '/notification-dashboard/stats',
        [NotificationDashboardController::class, 'stats']
    )->name('notification.dashboard.stats');

    Route::get(
        '/notification-dashboard/online-admins',
        [NotificationDashboardController::class, 'onlineAdmins']
    )->name('notification.dashboard.online-admins');

    /*
    |--------------------------------------------------------------------------
    | User Profile
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/profile',
        [ProfileController::class, 'show']
    )->name('profile.show');

    Route::get(
        '/profile/edit',
        [ProfileController::class, 'edit']
    )->name('profile.edit');

    Route::put(
        '/profile',
        [ProfileController::class, 'update']
    )->name('profile.update');

    /*
    |--------------------------------------------------------------------------
    | User Presence (Online Status)
    |--------------------------------------------------------------------------
    */

    Route::post(
        '/user/online',
        [\App\Http\Controllers\UserPresenceController::class, 'markOnline']
    )->name('user.online');

    Route::post(
        '/user/offline',
        [\App\Http\Controllers\UserPresenceController::class, 'markOffline']
    )->name('user.offline');
});
