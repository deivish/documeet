<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
*/

// Canal privado para notificaciones de usuario
Broadcast::channel('users.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});