<?php

use App\Models\Rider;
use App\Models\User;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('stores.{userId}', function (User $user, int $userId): bool {
    return $user->id === $userId;
});

Broadcast::channel('riders.{riderId}', function (User $user, int $riderId): bool {
    return Rider::whereKey($riderId)->where('user_id', $user->id)->exists();
});