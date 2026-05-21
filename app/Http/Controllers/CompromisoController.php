<?php

namespace App\Http\Controllers;

use App\Models\Reunion;
use App\Models\Compromiso;
use Illuminate\Http\Request;
use App\Helpers\AuditoriaHelper;

class CompromisoController extends Controller
{
    public function store(Request $request, Reunion $reunion)
    {
        $data = $request->validate([
            'descripcion' => 'required|string',
            'responsable' => 'required|string',
            'fecha'       => 'required|date',
            'resultado'   => 'nullable|string'
        ]);

        $compromiso = $reunion->compromisos()->create($data);

        AuditoriaHelper::registrar(
    'Agregó compromiso: ' . $data['descripcion'],
    'Compromiso',
    $compromiso->id,
    $reunion->id,
    null,
    ['descripcion' => $data['descripcion'], 'responsable' => $data['responsable']]
);

        return response()->json([
            'ok'         => true,
            'compromiso' => $compromiso
        ]);
    }

    public function update(Request $request, Compromiso $compromiso)
    {
        $data = $request->validate([
            'descripcion' => 'required|string',
            'responsable' => 'required|string',
            'fecha'       => 'required|date',
            'resultado'   => 'nullable|string',
            'estado'      => 'nullable|in:pendiente,cumplido,vencido',
        ]);

        $compromiso->update($data);

        AuditoriaHelper::registrar(
    'Editó compromiso: ' . $compromiso->descripcion,
    'Compromiso',
    $compromiso->id,
    $compromiso->reunion_id,
    null,
    $data
);

        return response()->json([
            'ok'         => true,
            'compromiso' => [
                'id'          => $compromiso->id,
                'descripcion' => $compromiso->descripcion,
                'responsable' => $compromiso->responsable,
                'fecha'       => \Carbon\Carbon::parse($compromiso->fecha)->format('d/m/Y'),
                'resultado'   => $compromiso->resultado,
                'estado'      => $compromiso->estado,
            ]
        ]);
    }

    // ✅ Actualizar solo el estado (para el dropdown)
    public function updateEstado(Request $request, Compromiso $compromiso)
    {
        $request->validate([
            'estado' => 'required|in:pendiente,cumplido,vencido'
        ]);

        $estadoAnterior = $compromiso->estado;
        $compromiso->update(['estado' => $request->estado]);

        AuditoriaHelper::registrar(
    'Cambió estado de "' . $compromiso->descripcion . '" a: ' . $request->estado,
    'Compromiso',
    $compromiso->id,
    $compromiso->reunion_id,
    ['estado' => $estadoAnterior],
    ['estado' => $request->estado]
);

        return response()->json([
            'ok'     => true,
            'estado' => $compromiso->estado
        ]);
    }

    public function destroy(Compromiso $compromiso)
    {
        AuditoriaHelper::registrar(
    'Eliminó compromiso: ' . $compromiso->descripcion,
    'Compromiso',
    $compromiso->id,
    $compromiso->reunion_id,
    ['descripcion' => $compromiso->descripcion],
    null
);

        $compromiso->delete();

        return response()->json([
            'ok'      => true,
            'message' => 'Compromiso eliminado'
        ]);
    }
}