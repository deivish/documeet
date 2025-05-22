<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Aquí puedes registrar todos los canales de transmisión de eventos
| que tu aplicación admite. El sistema de autorización para los
| canales de transmisión se define usando la función `Broadcast::channel`.
|
*/

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});


Broadcast::channel('usuario.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});