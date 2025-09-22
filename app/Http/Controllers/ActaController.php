<?php

namespace App\Http\Controllers;

use App\Models\Acta;
use App\Models\Reunion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;
use App\Notifications\ActaGenerada;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Actividad;

class ActaController extends Controller
{
    public function store(Request $request, Reunion $reunion)
    {
        $data = $request->validate([
            'contenido' => 'required|string'
        ]);

        $acta = $reunion->acta()->updateOrCreate(
            ['reunion_id' => $reunion->id],
            [
                'contenido' => $data['contenido'],
                'estado' => 'borrador',
                'creada_por' => Auth::id()
            ]
        );

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Notas guardadas como borrador.',
                'acta_id' => $acta->id
            ]);
        }

        return redirect()
            ->route('reuniones.show', $reunion->id)
            ->with('success', 'Acta guardada como borrador.');
    }

    /**
     * Finalizar un acta: cambiar estado, generar PDF y DOCX, notificar invitados.
     */
    public function finalizar(Acta $acta)
    {
        if ($acta->reunion->user_id !== Auth::id()) {
            abort(403, 'No tienes permiso para finalizar esta acta.');
        }

        $acta->estado = 'final';
        $acta->aprobada_at = now();
        $acta->aprobada_por = Auth::id();
        $acta->save();

        // Cargar actividades
        $acta->load('reunion.actividades');

        /** ----------------- PDF ----------------- */
        $htmlView = view('actas.pdf', compact('acta'))->render();
        $pdf = PDF::loadHTML($htmlView);
        $path = 'actas/acta-' . $acta->id . '.pdf';
        Storage::disk('public')->put($path, $pdf->output());
        $acta->archivo_pdf = $path;

        /** ----------------- DOCX ----------------- */
        $phpWord = new PhpWord();
        $section = $phpWord->addSection();
        $section->addText("Acta de la Reunión");
        $section->addText("Título: " . ($acta->reunion->titulo ?? 'Reunión'));
        $section->addText("Descripción: " . ($acta->reunion->descripcion ?? 'N/A'));
        $section->addText("Fecha: " . \Carbon\Carbon::parse($acta->reunion->fecha_hora)->format('d/m/Y H:i'));
        $section->addTextBreak(1);

        $section->addText("Desarrollo:");
        $section->addText($acta->contenido ?? 'Sin desarrollo registrado.');
        $section->addTextBreak(1);

        $section->addText("Actividades:");
        foreach ($acta->reunion->actividades as $actividad) {
            $section->addListItem(
                $actividad->nombre .
                " — Responsable: " . ($actividad->responsable ?? 'Sin asignar') .
                " — Fecha límite: " . \Carbon\Carbon::parse($actividad->fecha_entrega)->format('d/m/Y') .
                " — " . ($actividad->descripcion ?? '')
            );
        }

        $docxPath = storage_path('app/public/actas/acta-' . $acta->id . '.docx');
        $objWriter = IOFactory::createWriter($phpWord, 'Word2007');
        $objWriter->save($docxPath);

        $acta->archivo_docx = 'actas/acta-' . $acta->id . '.docx';
        $acta->save();

        // Notificar a invitados
        foreach ($acta->reunion->invitados as $usuario) {
            $usuario->notify(new ActaGenerada($acta));
        }

        return redirect()->route('actas.show', $acta)
            ->with('success', 'El acta fue finalizada y generada correctamente.');
    }

    public function show(Acta $acta)
    {
        $reunion = $acta->reunion;
        $reunion->load('actividades');
        return view('actas.show', compact('acta', 'reunion'));
    }

    public function descargarPdf(Acta $acta)
{
    // Cargar relaciones que usará la vista
    $acta->load(['reunion.actividades']);

    // Renderizar la vista con los datos actuales
    $html = view('actas.pdf', compact('acta'))->render();

    // Generar y descargar
    $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadHTML($html);

    // Opción A: forzar descarga
    return $pdf->stream('acta-'.$acta->id.'.pdf', [
        'Attachment' => false // 👈 false = inline (no descarga)
    ]);

    // Opción B (si prefieres ver en navegador):
    // return $pdf->stream('acta-'.$acta->id.'.pdf');
}

// Guardar una actividad desde el acta
public function storeActividad(Request $request, Acta $acta)
{
    $data = $request->validate([
        'nombre' => 'required|string',
        'responsable' => 'required|string',
        'fecha_entrega' => 'required|date',
        'descripcion' => 'nullable|string',
    ]);

    $actividad = $acta->reunion->actividades()->create($data);

    return response()->json([
        'ok' => true,
        'actividad' => [
            'id' => $actividad->id,
            'nombre' => $actividad->nombre,
            'responsable' => $actividad->responsable,
            'fecha_entrega' => \Carbon\Carbon::parse($actividad->fecha_entrega)->format('d/m/Y'),
            'descripcion' => $actividad->descripcion,
        ]
    ]);
}

// Eliminar actividad
public function destroyActividad($id)
{
    $actividad = Actividad::find($id);

    if (!$actividad) {
        return response()->json(['ok' => false, 'message' => 'Actividad no encontrada'], 404);
    }

    $actividad->delete();
    return response()->json(['ok' => true]);
}

public function updateActividad(Request $request, $id)
{
    $actividad = \App\Models\Actividad::findOrFail($id);

    $data = $request->validate([
        'nombre' => 'required|string',
        'responsable' => 'required|string',
        'fecha_entrega' => 'required|date',
        'descripcion' => 'nullable|string',
    ]);

    $actividad->update($data);

    return response()->json([
        'ok' => true,
        'actividad' => [
            'id' => $actividad->id,
            'nombre' => $actividad->nombre,
            'responsable' => $actividad->responsable,
            'fecha_entrega' => \Carbon\Carbon::parse($actividad->fecha_entrega)->format('d/m/Y'),
            'descripcion' => $actividad->descripcion,
        ]
    ]);
}

}
