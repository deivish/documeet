<?php

namespace App\Http\Controllers;

use App\Models\Acta;
use App\Models\Reunion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;
use App\Notifications\ActaGenerada;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Actividad;
use App\Services\ClaudeService;
use App\Helpers\AuditoriaHelper;

class ActaController extends Controller
{
    // ══════════════════════════════════════════════════════════════
    // GUARDAR BORRADOR DESDE VIDEOLLAMADA
    // ══════════════════════════════════════════════════════════════

    public function store(Request $request, Reunion $reunion)
    {
        $data = $request->validate([
            'contenido' => 'required|string'
        ]);

        $acta = $reunion->acta()->updateOrCreate(
            ['reunion_id' => $reunion->id],
            [
                'contenido'   => $data['contenido'],
                'estado'      => 'borrador',
                'creada_por'  => Auth::id()
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

    // ══════════════════════════════════════════════════════════════
    // FINALIZAR ACTA — genera PDF y DOCX con estructura completa
    // ══════════════════════════════════════════════════════════════

    public function finalizar(Acta $acta)
    {
        if ($acta->reunion->user_id !== Auth::id()) {
            abort(403, 'No tienes permiso para finalizar esta acta.');
        }

        // Cargar todas las relaciones necesarias
        $acta->load([
            'reunion.actividades',
            'reunion.compromisos',
            'reunion.invitados',
            'reunion.user',
            'reunion.transcripciones'
        ]);

        $reunion = $acta->reunion;

        // ── Intentar enriquecer el acta con IA si hay transcripción ──
        $transcripcion = $reunion->transcripciones()
            ->where('fuente', 'deepgram')
            ->orderBy('created_at', 'asc')
            ->pluck('contenido')
            ->implode("\n\n");

        if (!empty($transcripcion) && empty($acta->resumen)) {
            try {
                $claude  = new ClaudeService();
                $asistentes = $reunion->invitados
                    ->pluck('name')
                    ->prepend($reunion->user->name)
                    ->implode(', ');

                $datosReunion = [
                    'titulo'      => $reunion->titulo,
                    'organizador' => $reunion->user->name,
                    'fecha'       => \Carbon\Carbon::parse($reunion->fecha_hora)->format('d/m/Y H:i'),
                    'asistentes'  => $asistentes,
                ];

                $actaIA = $claude->generarActaCompleta($transcripcion, $datosReunion);

                // Guardar resumen y desarrollo generado por IA
                $acta->resumen   = $actaIA['resumen_ejecutivo'] ?? $acta->resumen;
                $acta->contenido = !empty($actaIA['desarrollo'])
                    ? $actaIA['desarrollo']
                    : $acta->contenido;

                Log::info('✅ Acta enriquecida con IA antes de finalizar');

            } catch (\Exception $e) {
                Log::warning('⚠️ No se pudo enriquecer con IA: ' . $e->getMessage());
                // Continúa sin IA — no bloquea la finalización
            }
        }

        // Cambiar estado
        $acta->estado      = 'final';
        $acta->aprobada_at = now();
        $acta->aprobada_por = Auth::id();
        $acta->save();

        // Asistentes para los documentos
        $asistentes = $reunion->invitados
            ->pluck('name')
            ->prepend($reunion->user->name . ' (Moderador)')
            ->toArray();

        // ══════════ PDF ══════════
        $htmlView = view('actas.pdf', compact('acta'))->render();
        $pdf      = Pdf::loadHTML($htmlView);
        $pdfPath  = 'actas/acta-' . $acta->id . '.pdf';
        Storage::disk('public')->put($pdfPath, $pdf->output());
        $acta->archivo_pdf = $pdfPath;

        // ══════════ DOCX ══════════
        $phpWord = new PhpWord();
        $phpWord->getDefaultFontName() !== 'Arial' && $phpWord->setDefaultFontName('Arial');
        $phpWord->setDefaultFontSize(11);

        $section = $phpWord->addSection([
            'marginTop'    => 1440,
            'marginBottom' => 1440,
            'marginLeft'   => 1800,
            'marginRight'  => 1440,
        ]);

        // — Encabezado —
        $section->addText('ACTA DE REUNIÓN', [
            'bold' => true, 'size' => 16, 'color' => '1F3864'
        ], ['alignment' => 'center', 'spaceAfter' => 200]);

        $section->addText(strtoupper($reunion->titulo), [
            'bold' => true, 'size' => 13
        ], ['alignment' => 'center', 'spaceAfter' => 400]);

        // — Datos generales —
        $section->addText('INFORMACIÓN DE LA REUNIÓN', [
            'bold' => true, 'size' => 12, 'color' => '2E75B6'
        ], ['spaceAfter' => 100]);

        $section->addText('Fecha y hora: ' . \Carbon\Carbon::parse($reunion->fecha_hora)->format('d/m/Y H:i'));
        $section->addText('Organizador: ' . ($reunion->user->name ?? 'Sin especificar'));
        $section->addText('Descripción: ' . ($reunion->descripcion ?? 'Sin descripción'));
        $section->addTextBreak(1);

        // — Asistentes —
        $section->addText('ASISTENTES', [
            'bold' => true, 'size' => 12, 'color' => '2E75B6'
        ], ['spaceAfter' => 100]);

        foreach ($asistentes as $asistente) {
            $section->addListItem($asistente);
        }
        $section->addTextBreak(1);

        // — Resumen ejecutivo —
        if (!empty($acta->resumen)) {
            $section->addText('RESUMEN EJECUTIVO', [
                'bold' => true, 'size' => 12, 'color' => '2E75B6'
            ], ['spaceAfter' => 100]);
            $section->addText($acta->resumen, ['size' => 11]);
            $section->addTextBreak(1);
        }

        // — Desarrollo —
        $section->addText('DESARROLLO DE LA REUNIÓN', [
            'bold' => true, 'size' => 12, 'color' => '2E75B6'
        ], ['spaceAfter' => 100]);
        $section->addText(
            !empty($acta->contenido)
                ? $acta->contenido
                : 'Sin desarrollo registrado.',
            ['size' => 11]
        );
        $section->addTextBreak(1);

        // — Actividades —
        $section->addText('ACTIVIDADES', [
            'bold' => true, 'size' => 12, 'color' => '2E75B6'
        ], ['spaceAfter' => 100]);

        if ($reunion->actividades->count() > 0) {
            foreach ($reunion->actividades as $actividad) {
                $estado = match($actividad->estado ?? 'pendiente') {
                    'completada' => '✓ Completada',
                    'en_curso'   => '⟳ En curso',
                    default      => '○ Pendiente'
                };
                $section->addListItem(
                    $actividad->nombre
                    . ' — Responsable: ' . ($actividad->responsable ?? 'Sin asignar')
                    . ' — Fecha límite: ' . \Carbon\Carbon::parse($actividad->fecha_entrega)->format('d/m/Y')
                    . ' — Estado: ' . $estado
                );
            }
        } else {
            $section->addText('No se registraron actividades en esta reunión.', ['italics' => true]);
        }
        $section->addTextBreak(1);

        // — Compromisos —
        $section->addText('COMPROMISOS', [
            'bold' => true, 'size' => 12, 'color' => '2E75B6'
        ], ['spaceAfter' => 100]);

        if ($reunion->compromisos->count() > 0) {
            foreach ($reunion->compromisos as $compromiso) {
                $estado = match($compromiso->estado ?? 'pendiente') {
                    'cumplido' => '✓ Cumplido',
                    'vencido'  => '✗ Vencido',
                    default    => '○ Pendiente'
                };
                $section->addListItem(
                    $compromiso->descripcion
                    . ' — Responsable: ' . ($compromiso->responsable ?? 'Sin asignar')
                    . ' — Fecha: ' . \Carbon\Carbon::parse($compromiso->fecha)->format('d/m/Y')
                    . ' — Estado: ' . $estado
                );
            }
        } else {
            $section->addText('No se registraron compromisos en esta reunión.', ['italics' => true]);
        }
        $section->addTextBreak(1);

        // — Cierre —
        $section->addText('CIERRE', [
            'bold' => true, 'size' => 12, 'color' => '2E75B6'
        ], ['spaceAfter' => 100]);
        $section->addText(
            'Acta generada automáticamente por DocuMeet el '
            . now()->format('d/m/Y') . ' a las ' . now()->format('H:i') . '.',
            ['italics' => true, 'size' => 10, 'color' => '666666']
        );

        // Guardar DOCX
        if (!file_exists(storage_path('app/public/actas'))) {
            mkdir(storage_path('app/public/actas'), 0755, true);
        }

        $docxPath = storage_path('app/public/actas/acta-' . $acta->id . '.docx');
        $objWriter = IOFactory::createWriter($phpWord, 'Word2007');
        $objWriter->save($docxPath);

        $acta->archivo_docx = 'actas/acta-' . $acta->id . '.docx';
        $acta->save();

        AuditoriaHelper::registrar(
    'Finalizó el acta',
    'Acta',
    $acta->id,
    $acta->reunion_id,
    ['estado' => 'borrador'],
    ['estado' => 'final', 'aprobada_at' => now()->toDateTimeString()]
);

        // Notificar a invitados
        foreach ($reunion->invitados as $usuario) {
            $usuario->notify(new ActaGenerada($acta));
        }

        return redirect()
            ->route('actas.show', $acta)
            ->with('success', 'El acta fue finalizada y generada correctamente.');
    }

    // ══════════════════════════════════════════════════════════════
    // VER ACTA
    // ══════════════════════════════════════════════════════════════

    public function show(Acta $acta)
    {
        $reunion = $acta->reunion;
        $reunion->load('actividades', 'compromisos');
        return view('actas.show', compact('acta', 'reunion'));
    }

    // ══════════════════════════════════════════════════════════════
    // DESCARGAR PDF (dinámico, siempre actualizado)
    // ══════════════════════════════════════════════════════════════

    public function descargarPdf(Acta $acta)
    {
        $acta->load(['reunion.actividades', 'reunion.compromisos', 'reunion.user', 'reunion.invitados']);
        $html = view('actas.pdf', compact('acta'))->render();
        $pdf  = Pdf::loadHTML($html);

        return $pdf->stream('acta-' . $acta->id . '.pdf', ['Attachment' => false]);
    }

    // ══════════════════════════════════════════════════════════════
    // ACTIVIDADES DESDE EL ACTA
    // ══════════════════════════════════════════════════════════════

    public function storeActividad(Request $request, Acta $acta)
    {
        $data = $request->validate([
            'nombre'        => 'required|string',
            'responsable'   => 'required|string',
            'fecha_entrega' => 'required|date',
            'descripcion'   => 'nullable|string',
        ]);

        $actividad = $acta->reunion->actividades()->create($data);

        AuditoriaHelper::registrar(
            'Agregó actividad desde el acta: ' . $actividad->nombre,
            'Actividad',
            $actividad->id,
            $acta->reunion_id,
            null,
            ['nombre' => $actividad->nombre, 'responsable' => $actividad->responsable]
        );

        return response()->json([
            'ok'       => true,
            'actividad' => [
                'id'            => $actividad->id,
                'nombre'        => $actividad->nombre,
                'responsable'   => $actividad->responsable,
                'fecha_entrega' => \Carbon\Carbon::parse($actividad->fecha_entrega)->format('d/m/Y'),
                'descripcion'   => $actividad->descripcion,
                'estado'        => $actividad->estado ?? 'pendiente',
            ]
        ]);
    }

    public function destroyActividad($id)
    {
        $actividad = Actividad::find($id);

        if (!$actividad) {
            return response()->json(['ok' => false, 'message' => 'Actividad no encontrada'], 404);
        }

        AuditoriaHelper::registrar(
    'Eliminó actividad: ' . ($actividad->nombre ?? 'Sin nombre'),
    'Actividad',
    $actividad->id,
    $actividad->reunion_id,
    ['nombre' => $actividad->nombre, 'responsable' => $actividad->responsable],
    null
);

        $actividad->delete();
        return response()->json(['ok' => true]);
    }

    public function updateActividad(Request $request, $id)
    {
        $actividad = Actividad::findOrFail($id);

        $data = $request->validate([
            'nombre'        => 'required|string',
            'responsable'   => 'required|string',
            'fecha_entrega' => 'required|date',
            'descripcion'   => 'nullable|string',
            'estado'        => 'nullable|in:pendiente,en_curso,completada',
        ]);

        $actividad->update($data);

        AuditoriaHelper::registrar(
    'Editó actividad: ' . $actividad->nombre,
    'Actividad',
    $actividad->id,
    $actividad->reunion_id,
    null,
    $data
);

        return response()->json([
            'ok'       => true,
            'actividad' => [
                'id'            => $actividad->id,
                'nombre'        => $actividad->nombre,
                'responsable'   => $actividad->responsable,
                'fecha_entrega' => \Carbon\Carbon::parse($actividad->fecha_entrega)->format('d/m/Y'),
                'descripcion'   => $actividad->descripcion,
                'estado'        => $actividad->estado,
            ]
        ]);
    }

    // ── Actualizar solo el estado (para el checkbox) ──
    public function updateEstadoActividad(Request $request, $id)
    {
        $actividad = Actividad::findOrFail($id);

        $request->validate([
            'estado' => 'required|in:pendiente,en_curso,completada'
        ]);
        $estadoAnterior = $actividad->estado;
        $actividad->update(['estado' => $request->estado]);

        AuditoriaHelper::registrar(
    'Cambió estado de "' . $actividad->nombre . '" a: ' . $request->estado,
    'Actividad',
    $actividad->id,
    $actividad->reunion_id,
    ['estado' => $estadoAnterior],
    ['estado' => $request->estado]
);

        return response()->json(['ok' => true, 'estado' => $actividad->estado]);
    }

    // ══════════════════════════════════════════════════════════════
    // IA — EXTRAER COMPROMISOS
    // ══════════════════════════════════════════════════════════════

    public function extraerCompromisos(Request $request, Acta $acta)
    {
        try {
            $transcripcion = $acta->reunion->transcripciones()
                ->where('fuente', 'deepgram')
                ->orderBy('created_at', 'asc')
                ->pluck('contenido')
                ->implode("\n\n");

            if (empty($transcripcion)) {
                return response()->json(['ok' => false, 'error' => 'No hay transcripción disponible'], 400);
            }

            $claude               = new ClaudeService();
            $compromisosExtraidos = $claude->extraerCompromisos($transcripcion);

            $compromisosGuardados = [];
            foreach ($compromisosExtraidos as $comp) {
                $compromiso = $acta->reunion->compromisos()->create([
                    'descripcion' => $comp['descripcion'],
                    'responsable' => $comp['responsable'],
                    'fecha'       => $comp['fecha'],
                    'resultado'   => $comp['resultado'],
                    'estado'      => 'pendiente',
                ]);

                $compromisosGuardados[] = [
                    'id'          => $compromiso->id,
                    'descripcion' => $compromiso->descripcion,
                    'responsable' => $compromiso->responsable,
                    'fecha'       => \Carbon\Carbon::parse($compromiso->fecha)->format('d/m/Y'),
                    'resultado'   => $compromiso->resultado,
                    'estado'      => $compromiso->estado,
                ];
            }

            return response()->json([
                'ok'         => true,
                'compromisos' => $compromisosGuardados,
                'total'       => count($compromisosGuardados)
            ]);

        } catch (\Exception $e) {
            Log::error('❌ Error extrayendo compromisos: ' . $e->getMessage());
            return response()->json(['ok' => false, 'error' => 'Error al extraer compromisos: ' . $e->getMessage()], 500);
        }
    }

    // ══════════════════════════════════════════════════════════════
    // IA — GENERAR RESUMEN
    // ══════════════════════════════════════════════════════════════

    public function generarResumen(Request $request, Acta $acta)
    {
        try {
            $transcripcion = $acta->reunion->transcripciones()
                ->where('fuente', 'deepgram')
                ->orderBy('created_at', 'asc')
                ->pluck('contenido')
                ->implode("\n\n");

            if (empty($transcripcion)) {
                return response()->json(['ok' => false, 'error' => 'No hay transcripción disponible'], 400);
            }

            $claude  = new ClaudeService();
            $resumen = $claude->generarResumen($transcripcion);

            $acta->update(['resumen' => $resumen]);

            return response()->json(['ok' => true, 'resumen' => $resumen]);

        } catch (\Exception $e) {
            Log::error('❌ Error generando resumen: ' . $e->getMessage());
            return response()->json(['ok' => false, 'error' => 'Error al generar resumen'], 500);
        }
    }

    // ══════════════════════════════════════════════════════════════
    // DESCARGAR DOCX (dinámico)
    // ══════════════════════════════════════════════════════════════

public function descargarDocx(Acta $acta)
{
    try {
        $acta->load(['reunion.actividades', 'reunion.compromisos', 'reunion.user', 'reunion.invitados']);
        $reunion = $acta->reunion;

        $asistentes = $reunion->invitados
            ->pluck('name')
            ->prepend($reunion->user->name . ' (Moderador)')
            ->implode(', ');

        $html = '<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
body { font-family: Arial, sans-serif; font-size: 11pt; margin: 2cm; }
h1 { color: #1F3864; text-align: center; font-size: 16pt; }
h2 { color: #1F3864; text-align: center; font-size: 13pt; }
h3 { color: #2E75B6; font-size: 12pt; border-bottom: 1px solid #2E75B6; }
table { width: 100%; border-collapse: collapse; margin-bottom: 10pt; }
td, th { border: 1px solid #ccc; padding: 6pt; font-size: 10pt; }
th { background-color: #2E75B6; color: white; }
.footer { color: #666; font-style: italic; font-size: 9pt; text-align: center; margin-top: 20pt; }
</style>
</head>
<body>
<h1>ACTA DE REUNIÓN</h1>
<h2>' . htmlspecialchars(strtoupper($reunion->titulo)) . '</h2>

<h3>INFORMACIÓN DE LA REUNIÓN</h3>
<p><strong>Fecha:</strong> ' . \Carbon\Carbon::parse($reunion->fecha_hora)->format('d/m/Y H:i') . '</p>
<p><strong>Organizador:</strong> ' . htmlspecialchars($reunion->user->name ?? 'N/A') . '</p>
<p><strong>Descripción:</strong> ' . htmlspecialchars($reunion->descripcion ?? 'Sin descripción') . '</p>

<h3>ASISTENTES</h3>
<p>' . htmlspecialchars($asistentes) . '</p>';

        if (!empty($acta->resumen)) {
            $html .= '<h3>DESARROLLO DE LA REUNIÓN</h3>';
            foreach (explode("\n", $acta->resumen) as $linea) {
                $linea = trim($linea);
                if (!empty($linea)) {
                    $html .= '<p>' . htmlspecialchars($linea) . '</p>';
                }
            }
        }

        $html .= '<h3>ACTIVIDADES</h3>';
        if ($reunion->actividades->count() > 0) {
            $html .= '<table><tr><th>Actividad</th><th>Responsable</th><th>Fecha límite</th><th>Estado</th></tr>';
            foreach ($reunion->actividades as $act) {
                $html .= '<tr>
                    <td>' . htmlspecialchars($act->nombre) . '</td>
                    <td>' . htmlspecialchars($act->responsable ?? 'Sin asignar') . '</td>
                    <td>' . \Carbon\Carbon::parse($act->fecha_entrega)->format('d/m/Y') . '</td>
                    <td>' . ucfirst($act->estado ?? 'pendiente') . '</td>
                </tr>';
            }
            $html .= '</table>';
        } else {
            $html .= '<p>Sin actividades registradas.</p>';
        }

        $html .= '<h3>COMPROMISOS</h3>';
        if ($reunion->compromisos->count() > 0) {
            $html .= '<table><tr><th>Compromiso</th><th>Responsable</th><th>Fecha</th><th>Estado</th></tr>';
            foreach ($reunion->compromisos as $comp) {
                $html .= '<tr>
                    <td>' . htmlspecialchars($comp->descripcion) . '</td>
                    <td>' . htmlspecialchars($comp->responsable ?? 'Sin asignar') . '</td>
                    <td>' . \Carbon\Carbon::parse($comp->fecha)->format('d/m/Y') . '</td>
                    <td>' . ucfirst($comp->estado ?? 'pendiente') . '</td>
                </tr>';
            }
            $html .= '</table>';
        } else {
            $html .= '<p>Sin compromisos registrados.</p>';
        }

        $html .= '<p class="footer">Acta generada por DocuMeet el ' . now()->format('d/m/Y H:i') . '.</p>
</body>
</html>';

        $filename = 'acta-' . $acta->id . '.doc';

        return response($html, 200, [
            'Content-Type' => 'application/msword',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);

    } catch (\Exception $e) {
        Log::error('❌ Error descarga: ' . $e->getMessage());
        return response()->json(['error' => $e->getMessage()], 500);
    }
}
}