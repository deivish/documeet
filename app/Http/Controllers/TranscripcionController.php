<?php

namespace App\Http\Controllers;

use App\Models\Reunion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TranscripcionController extends Controller
{
    public function store(Request $request, Reunion $reunion)
    {
        // Validar acceso (organizador o invitado)
        if ($reunion->user_id !== Auth::id() && !$reunion->invitados->contains(Auth::id())) {
            abort(403, 'No tienes permiso para esta reunión');
        }

        // Validación de campos
        $data = $request->validate([
            'contenido' => 'required|string',
            'fuente'    => 'in:manual,stt'
        ]);

        // Guardar transcripción
        $trans = $reunion->transcripciones()->create([
            'user_id'   => Auth::id(),
            'contenido' => $data['contenido'],
            'fuente'    => $data['fuente'] ?? 'stt'
        ]);

        return response()->json([
            'ok' => true,
            'transcripcion' => $trans
        ]);
    }
}
