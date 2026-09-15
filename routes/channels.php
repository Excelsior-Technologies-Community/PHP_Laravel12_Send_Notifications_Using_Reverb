<?php

use App\Models\User;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('posts', function () {
    return true;
});

Broadcast::channel('dashboard.stats', function (User $user) {
    return $user->is_admin;
});

Broadcast::channel('notification.{userId}', function (User $user, $userId) {
    return (int) $user->id === (int) $userId;
});

Broadcast::channel('presence.online', function (User $user) {
    if (! $user->is_admin) {
        return false;
    }

    return [
        'id' => $user->id,
        'name' => $user->name,
    ];
});
