<?php

namespace App\Http\Controllers;

use App\Models\Reunion;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class VideoCallController extends Controller
{
    public function join(Reunion $reunion)
    {
        // Opcional: política/permiso para ver la reunión
        // $this->authorize('view', $reunion);

        // Nombre de sala determinístico y poco “adivinable”
        // (puedes ajustarlo con un hash más fuerte si quieres)
        $roomName = 'documeet-' . $reunion->id . '-' . Str::slug($reunion->titulo) . '-' . substr(sha1($reunion->id . config('app.key')),0,8);

        return view('reuniones.videollamada', [
            'reunion'  => $reunion,
            'roomName' => $roomName,
            'userName' => Auth::user()->name ?? 'Invitado',
        ]);
    }
}
