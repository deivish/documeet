<?php

namespace App\Http\Controllers;

use App\Models\Reunion;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class VideoCallController extends Controller
{
    public function join(Reunion $reunion)
{
    $roomName = 'sala-'.$reunion->id.'-'.Str::random(8);

    // Fallback: si tu tabla "users" no tiene "name", usa "fullName" o "email"
    $userName = Auth::user()->name ?? Auth::user()->fullName ?? Auth::user()->email;

    // Crear acta en borrador automáticamente si no existe
    if (!$reunion->acta) {
        $reunion->acta()->create([
            'contenido' => '',
            'estado' => 'borrador',
            'creada_por' => Auth::id(),
        ]);
    }

    return view('reuniones.videollamada', compact('reunion', 'roomName', 'userName'));
}

}
