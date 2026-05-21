<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Reunion;
use App\Models\Actividad;
use App\Models\Compromiso;
use Carbon\Carbon;

class AnalyticsController extends Controller
{
    public function index()
    {
        $user   = Auth::user();
        $userId = $user->id;
        $anio   = now()->year;

        // Incluye reuniones eliminadas para tener datos completos
        $reunionIds = Reunion::withTrashed()
            ->where('user_id', $userId)
            ->pluck('id');

        // ── KPIs ──
        $totalReunionesCreadas  = Reunion::withTrashed()->where('user_id', $userId)->count();
        $totalReunionesInvitado = Reunion::withTrashed()
            ->whereHas('invitados', fn($q) => $q->where('user_id', $userId))
            ->where('user_id', '!=', $userId)
            ->count();

        $actividadesPendientes  = Actividad::whereIn('reunion_id', $reunionIds)->where('estado', 'pendiente')->whereNull('deleted_at')->count();
        $actividadesCompletadas = Actividad::whereIn('reunion_id', $reunionIds)->where('estado', 'completada')->whereNull('deleted_at')->count();
        $actividadesEnCurso     = Actividad::whereIn('reunion_id', $reunionIds)->where('estado', 'en_curso')->whereNull('deleted_at')->count();

        $compromisosPendientes = Compromiso::whereIn('reunion_id', $reunionIds)->where('estado', 'pendiente')->count();
        $compromisosVencidos   = Compromiso::whereIn('reunion_id', $reunionIds)->where('estado', 'vencido')->count();
        $compromisosCumplidos  = Compromiso::whereIn('reunion_id', $reunionIds)->where('estado', 'cumplido')->count();

        // ── Reuniones por mes — últimos 12 meses móviles ──
        $inicio12 = now()->subMonths(11)->startOfMonth();

        $mesesLabels    = [];
        $reunionesPorMes = array_fill(0, 12, 0);

        for ($i = 0; $i < 12; $i++) {
            $mes = $inicio12->copy()->addMonths($i);
            $mesesLabels[] = $mes->locale('es')->isoFormat('MMM YY');
        }

        Reunion::withTrashed()
    ->where('user_id', $userId)
    ->where('fecha_hora', '>=', $inicio12)
    ->get()
    ->each(function ($r) use ($inicio12, &$reunionesPorMes) {
        $diff = (int) $inicio12->copy()
            ->diffInMonths(Carbon::parse($r->fecha_hora)->startOfMonth());
        if ($diff >= 0 && $diff < 12) {
            $reunionesPorMes[$diff]++;
        }
    });

        // ── Reuniones por día de semana — últimos 12 meses ──
        $diasSemana      = ['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'];
        $reunionesPorDia = array_fill(0, 7, 0);

        Reunion::withTrashed()
            ->where('user_id', $userId)
            ->where('fecha_hora', '>=', $inicio12)
            ->get()
            ->each(function ($r) use (&$reunionesPorDia) {
                $dia = Carbon::parse($r->fecha_hora)->dayOfWeek;
                $idx = $dia === 0 ? 6 : $dia - 1;
                $reunionesPorDia[$idx]++;
            });

        // ── Próximas reuniones ──
        $proximasReuniones = Reunion::where('user_id', $userId)
            ->where('fecha_hora', '>=', now())
            ->orderBy('fecha_hora', 'asc')
            ->take(5)
            ->get();

        // ── Reuniones recientes ──
        $reunionesRecientes = Reunion::withTrashed()
            ->where('user_id', $userId)
            ->orderBy('fecha_hora', 'desc')
            ->take(5)
            ->get();

        // ── Compromisos vencidos con detalle ──
        $compromisosVencidosDetalle = Compromiso::whereIn('reunion_id', $reunionIds)
            ->where('estado', 'vencido')
            ->with('reunion')
            ->orderBy('fecha', 'asc')
            ->take(5)
            ->get();

        // ── Próximas como invitado ──
        $proximasComoInvitado = Reunion::whereHas('invitados', fn($q) => $q->where('user_id', $userId))
            ->where('user_id', '!=', $userId)
            ->where('fecha_hora', '>=', now())
            ->orderBy('fecha_hora', 'asc')
            ->take(3)
            ->get();

        return view('analytics', compact(
            'totalReunionesCreadas',
            'totalReunionesInvitado',
            'actividadesPendientes',
            'actividadesCompletadas',
            'actividadesEnCurso',
            'compromisosPendientes',
            'compromisosVencidos',
            'compromisosCumplidos',
            'reunionesPorMes',
            'mesesLabels',
            'reunionesPorDia',
            'diasSemana',
            'proximasReuniones',
            'proximasComoInvitado',
            'reunionesRecientes',
            'compromisosVencidosDetalle',
            'anio'
        ));
    }
}