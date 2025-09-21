<?php

namespace App\Http\Controllers;

use App\Models\Reunion;
use App\Models\Compromiso;
use Illuminate\Http\Request;

class CompromisoController extends Controller
{
    // Guardar un compromiso
    public function store(Request $request, Reunion $reunion)
    {
        $data = $request->validate([
            'descripcion' => 'required|string',
            'responsable' => 'required|string',
            'fecha'       => 'required|date',
            'resultado'   => 'nullable|string'
        ]);

        $compromiso = $reunion->compromisos()->create($data);

        return response()->json([
            'ok' => true,
            'compromiso' => $compromiso
        ]);
    }

    // Eliminar compromiso
    public function destroy(Compromiso $compromiso)
    {
        $compromiso->delete();

        return response()->json([
            'ok' => true,
            'message' => 'Compromiso eliminado'
        ]);
    }
}
