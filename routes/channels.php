<?php

use Illuminate\Support\Facades\Broadcast;
use App\Models\User;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Channel private admin.{adminId} hanya bisa diakses oleh admin yang bersangkutan
| atau kasir yang berada di bawah admin tersebut.
|
*/

Broadcast::channel('admin.{adminId}', function (User $user, int $adminId) {
    // Admin bisa akses channel-nya sendiri
    if ($user->isAdmin() && $user->id === $adminId) {
        return true;
    }

    // Kasir bisa akses channel admin-nya
    if ($user->isKasir() && $user->admin_id === $adminId) {
        return true;
    }

    return false;
});
