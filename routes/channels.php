<?php

use Illuminate\Support\Facades\Broadcast;
use App\Models\User;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('tenant.{tenantId}', function (User $user, int $tenantId) {
    return (int) $user->tenant_id === $tenantId;
});

Broadcast::channel('tecnico.{userId}', function (User $user, int $userId) {
    return (int) $user->id === $userId;
});

Broadcast::channel('notifiche.{userId}', function (User $user, int $userId) {
    return (int) $user->id === $userId;
});
