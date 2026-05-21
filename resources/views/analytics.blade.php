@extends('layouts.app')

@section('titulo')
    Dashboard — DocuMeet
@endsection

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    {{-- ══════════ HEADER ══════════ --}}
    <div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-800 flex items-center gap-3">
                <div class="w-12 h-12 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl flex items-center justify-center shadow-lg">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
                ¡Hola, {{ auth()->user()->name }}!
            </h1>
            <p class="text-gray-500 mt-1 text-sm">Resumen general de tu actividad en DocuMeet · {{ now()->format('d/m/Y') }}</p>
        </div>
        <div class="flex gap-3">
            {{-- Botón volver --}}
<a href="{{ route('post.index') }}"
   class="inline-flex items-center gap-2 px-5 py-2.5 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 rounded-xl font-medium transition-all text-sm">
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
    </svg>
    Volver
</a>
            <a href="{{ route('reuniones.create') }}"
               class="inline-flex items-center gap-2 px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-medium transition-all shadow-md text-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Nueva Reunión
            </a>
            <a href="{{ route('reuniones.index') }}"
               class="inline-flex items-center gap-2 px-5 py-2.5 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 rounded-xl font-medium transition-all text-sm">
                Ver Reuniones
            </a>
        </div>
    </div>

    {{-- ══════════ KPIs FILA 1 ══════════ --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">

        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-5">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 bg-indigo-100 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <span class="text-xs text-indigo-600 font-semibold bg-indigo-50 px-2 py-1 rounded-full">Organizador</span>
            </div>
            <p class="text-3xl font-bold text-gray-800">{{ $totalReunionesCreadas }}</p>
            <p class="text-sm text-gray-500 mt-1">Reuniones creadas</p>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-5">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 bg-teal-100 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <span class="text-xs text-teal-600 font-semibold bg-teal-50 px-2 py-1 rounded-full">Invitado</span>
            </div>
            <p class="text-3xl font-bold text-gray-800">{{ $totalReunionesInvitado }}</p>
            <p class="text-sm text-gray-500 mt-1">Como participante</p>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-5">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 bg-amber-100 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
                <span class="text-xs text-amber-600 font-semibold bg-amber-50 px-2 py-1 rounded-full">Pendientes</span>
            </div>
            <p class="text-3xl font-bold text-gray-800">{{ $actividadesPendientes }}</p>
            <p class="text-sm text-gray-500 mt-1">Actividades por completar</p>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-5">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 {{ $compromisosVencidos > 0 ? 'bg-red-100' : 'bg-green-100' }} rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 {{ $compromisosVencidos > 0 ? 'text-red-600' : 'text-green-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <span class="text-xs {{ $compromisosVencidos > 0 ? 'text-red-600 bg-red-50' : 'text-green-600 bg-green-50' }} font-semibold px-2 py-1 rounded-full">
                    {{ $compromisosVencidos > 0 ? 'Atención' : 'Al día' }}
                </span>
            </div>
            <p class="text-3xl font-bold {{ $compromisosVencidos > 0 ? 'text-red-600' : 'text-gray-800' }}">{{ $compromisosVencidos }}</p>
            <p class="text-sm text-gray-500 mt-1">Compromisos vencidos</p>
        </div>

    </div>

    {{-- ══════════ FILA 2: GRÁFICAS ══════════ --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">

        {{-- Barras: reuniones por mes --}}
        <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-base font-bold text-gray-800">Reuniones por mes</h3>
                    <p class="text-xs text-gray-500 mt-0.5">Últimos 12 meses</p>
                </div>
                <span class="text-xs text-indigo-600 bg-indigo-50 px-3 py-1 rounded-full font-medium">
                    {{ array_sum($reunionesPorMes) }} total
                </span>
            </div>
            <div class="relative h-48">
                <canvas id="chartMeses"></canvas>
            </div>
        </div>

        {{-- Dona: compromisos --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-base font-bold text-gray-800 mb-1">Estado de compromisos</h3>
            <p class="text-xs text-gray-500 mb-4">Todas tus reuniones</p>
            @php $totalComp = $compromisosPendientes + $compromisosVencidos + $compromisosCumplidos; @endphp
            @if($totalComp > 0)
                <div class="relative h-44">
                    <canvas id="chartCompromisos"></canvas>
                </div>
                <div class="mt-4 space-y-1.5 text-xs">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span> Cumplidos</div>
                        <span class="font-semibold text-gray-700">{{ $compromisosCumplidos }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-amber-400"></span> Pendientes</div>
                        <span class="font-semibold text-gray-700">{{ $compromisosPendientes }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-red-500"></span> Vencidos</div>
                        <span class="font-semibold text-gray-700">{{ $compromisosVencidos }}</span>
                    </div>
                </div>
            @else
                <div class="h-44 flex items-center justify-center text-gray-400 text-sm">Sin compromisos aún</div>
            @endif
        </div>

    </div>

    {{-- ══════════ FILA 3: REUNIONES POR DÍA + ACTIVIDADES ══════════ --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">

        {{-- Barras: reuniones por día de semana --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-base font-bold text-gray-800 mb-1">Reuniones por día de la semana</h3>
            <p class="text-xs text-gray-500 mb-4">Últimos 12 meses — qué días sueles reunirte más</p>
            <div class="relative h-44">
                <canvas id="chartDias"></canvas>
            </div>
        </div>

        {{-- Actividades: resumen --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-base font-bold text-gray-800 mb-4">Estado de actividades</h3>
            @php $totalAct = $actividadesPendientes + $actividadesCompletadas; @endphp
            @if($totalAct > 0)
                @php $pct = $totalAct > 0 ? round(($actividadesCompletadas / $totalAct) * 100) : 0; @endphp
                <div class="mb-6">
                    <div class="flex justify-between text-sm mb-2">
                        <span class="text-gray-600 font-medium">Progreso general</span>
                        <span class="font-bold {{ $pct >= 70 ? 'text-green-600' : ($pct >= 40 ? 'text-yellow-600' : 'text-red-600') }}">{{ $pct }}%</span>
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-3">
                        <div class="h-3 rounded-full transition-all {{ $pct >= 70 ? 'bg-green-500' : ($pct >= 40 ? 'bg-yellow-500' : 'bg-red-500') }}"
                             style="width: {{ $pct }}%"></div>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div class="bg-green-50 rounded-xl p-4 border border-green-200 text-center">
                        <p class="text-2xl font-bold text-green-600">{{ $actividadesCompletadas }}</p>
                        <p class="text-xs text-green-700 mt-1">Completadas</p>
                    </div>
                    <div class="bg-amber-50 rounded-xl p-4 border border-amber-200 text-center">
                        <p class="text-2xl font-bold text-amber-600">{{ $actividadesPendientes }}</p>
                        <p class="text-xs text-amber-700 mt-1">Pendientes</p>
                    </div>
                </div>
            @else
                <div class="h-44 flex items-center justify-center text-gray-400 text-sm">Sin actividades registradas</div>
            @endif
        </div>

    </div>

    {{-- ══════════ FILA 4: PRÓXIMAS + VENCIDOS ══════════ --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">

        {{-- Próximas reuniones --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="bg-gradient-to-r from-indigo-50 to-purple-50 px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                <h3 class="text-base font-bold text-gray-800">Próximas reuniones</h3>
                <a href="{{ route('reuniones.index') }}" class="text-xs text-indigo-600 hover:underline">Ver todas →</a>
            </div>
            <div class="divide-y divide-gray-100">
                @forelse($proximasReuniones as $r)
                    <a href="{{ route('reuniones.show', $r->id) }}"
                       class="flex items-center gap-4 px-6 py-4 hover:bg-gray-50 transition-all">
                        <div class="w-12 h-12 bg-indigo-100 rounded-xl flex flex-col items-center justify-center flex-shrink-0">
                            <span class="text-xs font-bold text-indigo-600 leading-none">{{ \Carbon\Carbon::parse($r->fecha_hora)->format('d') }}</span>
                            <span class="text-xs text-indigo-500">{{ \Carbon\Carbon::parse($r->fecha_hora)->format('M') }}</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-gray-800 truncate">{{ $r->titulo }}</p>
                            <p class="text-xs text-gray-500 mt-0.5">{{ \Carbon\Carbon::parse($r->fecha_hora)->format('H:i') }} · {{ \Carbon\Carbon::parse($r->fecha_hora)->diffForHumans() }}</p>
                        </div>
                        <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                @empty
                    <div class="px-6 py-8 text-center text-gray-400 text-sm">No tienes reuniones próximas</div>
                @endforelse
                @forelse($proximasComoInvitado as $r)
                    <a href="{{ route('reuniones.show', $r->id) }}"
                       class="flex items-center gap-4 px-6 py-4 hover:bg-gray-50 transition-all">
                        <div class="w-12 h-12 bg-teal-100 rounded-xl flex flex-col items-center justify-center flex-shrink-0">
                            <span class="text-xs font-bold text-teal-600 leading-none">{{ \Carbon\Carbon::parse($r->fecha_hora)->format('d') }}</span>
                            <span class="text-xs text-teal-500">{{ \Carbon\Carbon::parse($r->fecha_hora)->format('M') }}</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-gray-800 truncate">{{ $r->titulo }}</p>
                            <p class="text-xs text-gray-500 mt-0.5">{{ \Carbon\Carbon::parse($r->fecha_hora)->format('H:i') }} · Invitado</p>
                        </div>
                        <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                @empty
                @endforelse
            </div>
        </div>

        {{-- Compromisos vencidos --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="bg-gradient-to-r from-red-50 to-orange-50 px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                <h3 class="text-base font-bold text-gray-800 flex items-center gap-2">
                    <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Compromisos vencidos
                </h3>
                @if($compromisosVencidos > 0)
                    <span class="text-xs bg-red-100 text-red-700 px-2 py-1 rounded-full font-semibold">{{ $compromisosVencidos }} total</span>
                @endif
            </div>
            <div class="divide-y divide-gray-100">
                @forelse($compromisosVencidosDetalle as $comp)
                    <div class="flex items-start gap-3 px-6 py-4">
                        <span class="text-lg flex-shrink-0 mt-0.5">❌</span>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-800 truncate">{{ $comp->descripcion }}</p>
                            <p class="text-xs text-gray-500 mt-0.5">
                                {{ $comp->responsable ?? 'Sin asignar' }} ·
                                Venció el {{ \Carbon\Carbon::parse($comp->fecha)->format('d/m/Y') }}
                            </p>
                            <p class="text-xs text-indigo-500 mt-0.5">{{ $comp->reunion->titulo ?? '' }}</p>
                        </div>
                    </div>
                @empty
                    <div class="px-6 py-8 text-center text-gray-400 text-sm">
                        <span class="text-2xl block mb-2">✅</span>
                        No tienes compromisos vencidos
                    </div>
                @endforelse
            </div>
        </div>

    </div>

    {{-- ══════════ FILA 5: ACCESOS RÁPIDOS ══════════ --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">

        <a href="{{ route('reuniones.index') }}"
           class="group bg-white rounded-2xl shadow-sm border border-gray-200 hover:shadow-lg hover:border-indigo-300 transition-all p-6 flex items-center gap-4">
            <div class="w-12 h-12 bg-indigo-100 group-hover:bg-indigo-600 rounded-xl flex items-center justify-center transition-all">
                <svg class="w-6 h-6 text-indigo-600 group-hover:text-white transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
            <div>
                <p class="font-bold text-gray-800">Mis Reuniones</p>
                <p class="text-xs text-gray-500">{{ $totalReunionesCreadas }} creadas</p>
            </div>
        </a>

        <a href="{{ route('reuniones.create') }}"
           class="group bg-white rounded-2xl shadow-sm border border-gray-200 hover:shadow-lg hover:border-green-300 transition-all p-6 flex items-center gap-4">
            <div class="w-12 h-12 bg-green-100 group-hover:bg-green-600 rounded-xl flex items-center justify-center transition-all">
                <svg class="w-6 h-6 text-green-600 group-hover:text-white transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
            </div>
            <div>
                <p class="font-bold text-gray-800">Nueva Reunión</p>
                <p class="text-xs text-gray-500">Agendar ahora</p>
            </div>
        </a>

        <a href="{{ route('reuniones.historial') }}"
           class="group bg-white rounded-2xl shadow-sm border border-gray-200 hover:shadow-lg hover:border-gray-400 transition-all p-6 flex items-center gap-4">
            <div class="w-12 h-12 bg-gray-100 group-hover:bg-gray-600 rounded-xl flex items-center justify-center transition-all">
                <svg class="w-6 h-6 text-gray-600 group-hover:text-white transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <p class="font-bold text-gray-800">Historial</p>
                <p class="text-xs text-gray-500">Reuniones pasadas</p>
            </div>
        </a>

    </div>

    {{-- ══════════ REUNIONES RECIENTES ══════════ --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
            <h3 class="text-base font-bold text-gray-800">Reuniones recientes</h3>
            <a href="{{ route('reuniones.index') }}" class="text-xs text-indigo-600 hover:underline">Ver todas →</a>
        </div>
        <div class="divide-y divide-gray-100">
            @forelse($reunionesRecientes as $r)
                <div class="flex items-center gap-4 px-6 py-4 hover:bg-gray-50 transition-all">
                    <div class="w-10 h-10 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-lg flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-gray-800 truncate">{{ $r->titulo }}</p>
                        <p class="text-xs text-gray-500 mt-0.5">{{ \Carbon\Carbon::parse($r->fecha_hora)->format('d/m/Y H:i') }}</p>
                    </div>
                    <div class="flex items-center gap-2">
                        @if($r->acta)
                            <a href="{{ route('actas.show', $r->acta) }}"
                               class="text-xs px-3 py-1.5 {{ $r->acta->estado === 'final' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }} rounded-lg font-medium">
                                {{ $r->acta->estado === 'final' ? '✓ Acta final' : '⏳ Borrador' }}
                            </a>
                        @endif
                        <a href="{{ route('reuniones.show', $r->id) }}"
                           class="text-xs px-3 py-1.5 bg-indigo-50 text-indigo-600 hover:bg-indigo-100 rounded-lg font-medium transition-all">
                            Ver →
                        </a>
                    </div>
                </div>
            @empty
                <div class="px-6 py-8 text-center text-gray-400 text-sm">No hay reuniones recientes</div>
            @endforelse
        </div>
    </div>

</div>

{{-- Chart.js --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
// ── Barras: reuniones por mes ──
new Chart(document.getElementById('chartMeses'), {
    type: 'bar',
    data: {
        labels: {!! json_encode($mesesLabels) !!},
        datasets: [{
            label: 'Reuniones',
            data: {!! json_encode(array_values($reunionesPorMes)) !!},
            backgroundColor: 'rgba(99, 102, 241, 0.7)',
            borderColor: 'rgba(99, 102, 241, 1)',
            borderWidth: 1,
            borderRadius: 6,
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
            y: { beginAtZero: true, ticks: { stepSize: 1, font: { size: 11 } }, grid: { color: '#f3f4f6' } },
            x: { ticks: { font: { size: 11 } }, grid: { display: false } }
        }
    }
});

// ── Dona: compromisos ──
@if(($compromisosPendientes + $compromisosVencidos + $compromisosCumplidos) > 0)
new Chart(document.getElementById('chartCompromisos'), {
    type: 'doughnut',
    data: {
        labels: ['Cumplidos', 'Pendientes', 'Vencidos'],
        datasets: [{
            data: [{{ $compromisosCumplidos }}, {{ $compromisosPendientes }}, {{ $compromisosVencidos }}],
            backgroundColor: ['#10b981', '#f59e0b', '#ef4444'],
            borderWidth: 2,
            borderColor: '#fff',
            hoverOffset: 4
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        cutout: '65%'
    }
});
@endif

// ── Barras: reuniones por día de semana ──
new Chart(document.getElementById('chartDias'), {
    type: 'bar',
    data: {
        labels: {!! json_encode($diasSemana) !!},
        datasets: [{
            label: 'Reuniones',
            data: {!! json_encode(array_values($reunionesPorDia)) !!},
            backgroundColor: 'rgba(20, 184, 166, 0.7)',
            borderColor: 'rgba(20, 184, 166, 1)',
            borderWidth: 1,
            borderRadius: 6,
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
            y: { beginAtZero: true, ticks: { stepSize: 1, font: { size: 11 } }, grid: { color: '#f3f4f6' } },
            x: { ticks: { font: { size: 11 } }, grid: { display: false } }
        }
    }
});
</script>
@endsection