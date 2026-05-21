<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Reunion;
use App\Models\Actividad;
use App\Models\Compromiso;
use Carbon\Carbon;

class PostController extends Controller
{
    public function index()
    {
        $user    = Auth::user();
        $userId  = $user->id;
        $anio    = now()->year;

        // ── KPIs globales ──
        $totalReunionesCreadas  = Reunion::where('user_id', $userId)->count();
        $totalReunionesInvitado = Reunion::whereHas('invitados', fn($q) => $q->where('user_id', $userId))
            ->where('user_id', '!=', $userId)->count();

        $reunionIds = Reunion::where('user_id', $userId)->pluck('id');

        $actividadesPendientes = Actividad::whereIn('reunion_id', $reunionIds)
            ->where('estado', 'pendiente')
            ->whereNull('deleted_at')
            ->count();

        $actividadesCompletadas = Actividad::whereIn('reunion_id', $reunionIds)
            ->where('estado', 'completada')
            ->whereNull('deleted_at')
            ->count();

        $compromisosPendientes = Compromiso::whereIn('reunion_id', $reunionIds)
            ->where('estado', 'pendiente')
            ->count();

        $compromisosVencidos = Compromiso::whereIn('reunion_id', $reunionIds)
            ->where('estado', 'vencido')
            ->count();

        $compromisosCumplidos = Compromiso::whereIn('reunion_id', $reunionIds)
            ->where('estado', 'cumplido')
            ->count();

        // ── Reuniones por mes (año actual) ──
        $reunionesPorMes = array_fill(0, 12, 0);
        Reunion::where('user_id', $userId)
            ->whereYear('fecha_hora', $anio)
            ->get()
            ->groupBy(fn($r) => Carbon::parse($r->fecha_hora)->month - 1)
            ->each(fn($grupo, $mes) => $reunionesPorMes[$mes] = $grupo->count());

        // ── Próximas reuniones (las 3 más cercanas) ──
        $proximasReuniones = Reunion::where('user_id', $userId)
            ->where('fecha_hora', '>=', now())
            ->orderBy('fecha_hora', 'asc')
            ->take(3)
            ->get();

        // ── Reuniones como invitado próximas ──
        $proximasComoInvitado = Reunion::whereHas('invitados', fn($q) => $q->where('user_id', $userId))
            ->where('user_id', '!=', $userId)
            ->where('fecha_hora', '>=', now())
            ->orderBy('fecha_hora', 'asc')
            ->take(3)
            ->get();

        // ── Reuniones recientes (últimas 5) ──
        $reunionesRecientes = Reunion::where('user_id', $userId)
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

        // ── Reuniones por día de la semana ──
        $diasSemana = ['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'];
        $reunionesPorDia = array_fill(0, 7, 0);
        Reunion::where('user_id', $userId)
            ->whereYear('fecha_hora', $anio)
            ->get()
            ->each(function ($r) use (&$reunionesPorDia) {
                $dia = Carbon::parse($r->fecha_hora)->dayOfWeek; // 0=Dom
                $idx = $dia === 0 ? 6 : $dia - 1; // convertir a Lun=0
                $reunionesPorDia[$idx]++;
            });

        return view('dashboard', compact(
            'totalReunionesCreadas',
            'totalReunionesInvitado',
            'actividadesPendientes',
            'actividadesCompletadas',
            'compromisosPendientes',
            'compromisosVencidos',
            'compromisosCumplidos',
            'reunionesPorMes',
            'proximasReuniones',
            'proximasComoInvitado',
            'reunionesRecientes',
            'compromisosVencidosDetalle',
            'reunionesPorDia',
            'diasSemana',
            'anio'
        ));
    }
}