<?php

namespace App\Http\Controllers;

use App\Models\Reunion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AsistenciaController extends Controller
{
    //
    public function entrada(Reunion $reunion)
{
    $user = Auth::user();
    $reunion->invitados()->syncWithoutDetaching([$user->id => [
        'asistio' => true,
        'hora_entrada' => now()
    ]]);
    return response()->json(['ok'=>true]);
}

}
