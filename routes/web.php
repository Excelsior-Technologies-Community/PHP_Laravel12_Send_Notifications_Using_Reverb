<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\PostController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\NotificationDashboardController;
use App\Http\Controllers\HomeController;

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
});