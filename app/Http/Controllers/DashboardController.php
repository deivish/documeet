<?php

namespace App\Http\Controllers;

use App\Models\Reunion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;

class DashboardController extends Controller
{
    public function reunion(Reunion $reunion)
    {
        // Verificar que el usuario pertenece a esta reunión
        $esParticipante = $reunion->invitados()
            ->where('user_id', Auth::id())
            ->exists();

        if (!$esParticipante && $reunion->user_id !== Auth::id()) {
            abort(403, 'No tienes acceso a esta reunión.');
        }

        $reunion->load([
            'actividades',
            'compromisos',
            'invitados',
            'user',
            'transcripciones',
            'acta',
            'reunionPadre.compromisos',
            'reunionesHijas',
        ]);

        // ══════════════════════════════════════
        // MÉTRICAS DE ACTIVIDADES
        // ══════════════════════════════════════
        $actividades       = $reunion->actividades;
        $totalActividades  = $actividades->count();
        $actCompletadas    = $actividades->where('estado', 'completada')->count();
        $actEnCurso        = $actividades->where('estado', 'en_curso')->count();
        $actPendientes     = $actividades->where('estado', 'pendiente')->count();
        $actVencidas       = $actividades->filter(function ($a) {
            return $a->estado !== 'completada'
                && \Carbon\Carbon::parse($a->fecha_entrega)->isPast();
        })->count();

        $pctActividades = $totalActividades > 0
            ? round(($actCompletadas / $totalActividades) * 100)
            : 0;

        // ══════════════════════════════════════
        // MÉTRICAS DE COMPROMISOS
        // ══════════════════════════════════════
        $compromisos      = $reunion->compromisos;
        $totalCompromisos = $compromisos->count();
        $compCumplidos    = $compromisos->where('estado', 'cumplido')->count();
        $compVencidos     = $compromisos->where('estado', 'vencido')->count();
        $compPendientes   = $compromisos->where('estado', 'pendiente')->count();

        $pctCompromisos = $totalCompromisos > 0
            ? round(($compCumplidos / $totalCompromisos) * 100)
            : 0;

        // Compromisos por responsable
        $compromisosPorResponsable = $compromisos
            ->groupBy('responsable')
            ->map(fn($g) => [
                'total'    => $g->count(),
                'cumplidos' => $g->where('estado', 'cumplido')->count(),
                'pendientes'=> $g->where('estado', 'pendiente')->count(),
                'vencidos'  => $g->where('estado', 'vencido')->count(),
            ]);

        // ══════════════════════════════════════
        // MÉTRICAS DE ASISTENCIA
        // ══════════════════════════════════════
        $totalInvitados = $reunion->invitados->count();
        $asistentes     = $reunion->invitados
            ->filter(fn($u) => $u->pivot->asistio ?? false)
            ->count();
        $pctAsistencia  = $totalInvitados > 0
            ? round(($asistentes / $totalInvitados) * 100)
            : 0;

        // ══════════════════════════════════════
        // MÉTRICAS DE TRANSCRIPCIÓN
        // ══════════════════════════════════════
        $transcripcionCompleta = $reunion->transcripciones
            ->where('fuente', 'deepgram')
            ->pluck('contenido')
            ->implode(' ');

        $palabrasTranscripcion = str_word_count($transcripcionCompleta);
        $fragmentos            = $reunion->transcripciones
            ->where('fuente', 'deepgram')
            ->count();

        // ══════════════════════════════════════
        // MÉTRICAS DE SEGUIMIENTO (reunión padre)
        // ══════════════════════════════════════
        $compromisosPadre         = collect();
        $compPadrePendientes      = 0;
        $compPadreCumplidos       = 0;

        if ($reunion->reunionPadre) {
            $compromisosPadre    = $reunion->reunionPadre->compromisos;
            $compPadrePendientes = $compromisosPadre->where('estado', 'pendiente')->count();
            $compPadreCumplidos  = $compromisosPadre->where('estado', 'cumplido')->count();
        }

        // ══════════════════════════════════════
        // SCORE GENERAL DE LA REUNIÓN (0-100)
        // ══════════════════════════════════════
        $score = 0;
        $factores = 0;
        if ($totalActividades > 0)  { $score += $pctActividades;  $factores++; }
        if ($totalCompromisos > 0)  { $score += $pctCompromisos;  $factores++; }
        if ($totalInvitados > 0)    { $score += $pctAsistencia;   $factores++; }
        $scoreGeneral = $factores > 0 ? round($score / $factores) : 0;

        // ══════════════════════════════════════
        // DATOS PARA GRÁFICAS
        // ══════════════════════════════════════

        // Actividades por estado (dona)
        $chartActividades = [
            'labels' => ['Completadas', 'En curso', 'Pendientes', 'Vencidas'],
            'data'   => [$actCompletadas, $actEnCurso, $actPendientes, $actVencidas],
            'colors' => ['#10b981', '#3b82f6', '#f59e0b', '#ef4444'],
        ];

        // Compromisos por estado (dona)
        $chartCompromisos = [
            'labels' => ['Cumplidos', 'Pendientes', 'Vencidos'],
            'data'   => [$compCumplidos, $compPendientes, $compVencidos],
            'colors' => ['#10b981', '#f59e0b', '#ef4444'],
        ];

        // Compromisos por responsable (barras)
        $chartResponsables = [
            'labels'    => $compromisosPorResponsable->keys()->toArray(),
            'cumplidos' => $compromisosPorResponsable->pluck('cumplidos')->toArray(),
            'pendientes'=> $compromisosPorResponsable->pluck('pendientes')->toArray(),
            'vencidos'  => $compromisosPorResponsable->pluck('vencidos')->toArray(),
        ];

        return view('reuniones.dashboard', compact(
            'reunion',
            // Actividades
            'totalActividades', 'actCompletadas', 'actEnCurso',
            'actPendientes', 'actVencidas', 'pctActividades',
            // Compromisos
            'totalCompromisos', 'compCumplidos', 'compVencidos',
            'compPendientes', 'pctCompromisos', 'compromisosPorResponsable',
            // Asistencia
            'totalInvitados', 'asistentes', 'pctAsistencia',
            // Transcripción
            'palabrasTranscripcion', 'fragmentos',
            // Seguimiento
            'compromisosPadre', 'compPadrePendientes', 'compPadreCumplidos',
            // Score
            'scoreGeneral',
            // Charts
            'chartActividades', 'chartCompromisos', 'chartResponsables'
        ));
    }

    public function auditoria(Reunion $reunion)
{
    // Solo el moderador puede ver la auditoría
    if ($reunion->user_id !== \Illuminate\Support\Facades\Auth::id()) {
        abort(403, 'Solo el moderador puede ver la auditoría.');
    }
 
    $registros = \App\Models\Auditoria::where('reunion_id', $reunion->id)
        ->with('usuario')
        ->orderBy('created_at', 'desc')
        ->paginate(25);
 
    return view('reuniones.auditoria', compact('reunion', 'registros'));
}

public function exportarAuditoria(Reunion $reunion)
{
    // Solo el moderador puede exportar
    if ($reunion->user_id !== \Illuminate\Support\Facades\Auth::id()) {
        abort(403, 'Solo el moderador puede exportar la auditoría.');
    }
 
    $registros = \App\Models\Auditoria::where('reunion_id', $reunion->id)
        ->with('usuario')
        ->orderBy('created_at', 'desc')
        ->get();
 
    $filename = 'auditoria-reunion-' . $reunion->id . '-' . now()->format('Ymd-His') . '.csv';
 
    $headers = [
        'Content-Type'        => 'text/csv; charset=UTF-8',
        'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        'Pragma'              => 'no-cache',
        'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
        'Expires'             => '0',
    ];
 
    $callback = function () use ($registros, $reunion) {
        $handle = fopen('php://output', 'w');
 
        // BOM para que Excel abra bien el UTF-8
        fputs($handle, "\xEF\xBB\xBF");
 
        // Encabezado del documento
        fputcsv($handle, ['AUDITORÍA DE REUNIÓN — DocuMeet'], ';');
        fputcsv($handle, ['Reunión:', $reunion->titulo], ';');
        fputcsv($handle, ['Exportado el:', now()->format('d/m/Y H:i')], ';');
        fputcsv($handle, [], ';');
 
        // Cabeceras de columnas
        fputcsv($handle, [
            'Fecha y hora',
            'Usuario',
            'Acción',
            'Modelo',
            'ID Registro',
            'Valores anteriores',
            'Valores nuevos',
        ], ';');
 
        foreach ($registros as $r) {
            $anteriores = '';
            $nuevos     = '';
 
            if ($r->valores_anteriores) {
                $anteriores = collect($r->valores_anteriores)
                    ->map(fn($v, $k) => "$k: $v")
                    ->implode(' | ');
            }
            if ($r->valores_nuevos) {
                $nuevos = collect($r->valores_nuevos)
                    ->map(fn($v, $k) => "$k: $v")
                    ->implode(' | ');
            }
 
            fputcsv($handle, [
                \Carbon\Carbon::parse($r->created_at)->format('d/m/Y H:i:s'),
                $r->usuario->name ?? 'N/A',
                $r->accion,
                $r->modelo,
                $r->modelo_id ?? '',
                $anteriores,
                $nuevos,
            ], ';');
        }
 
        fclose($handle);
    };
 
    return response()->stream($callback, 200, $headers);
}
}