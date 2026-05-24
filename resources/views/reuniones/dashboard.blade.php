@extends('layouts.app')

@section('titulo')
    Dashboard — {{ $reunion->titulo }}
@endsection

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 py-8">
<div class="max-w-7xl mx-auto px-4">

    {{-- HEADER --}}
    <div class="mb-8 flex flex-wrap items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <a href="{{ route('reuniones.show', $reunion->id) }}"
               class="inline-flex items-center gap-2 px-4 py-2 bg-white text-gray-700 hover:text-indigo-600 font-medium rounded-lg shadow-sm hover:shadow-md transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Volver a la reunión
            </a>
            @if($reunion->user_id === Auth::id())
            <a href="{{ route('reuniones.auditoria', $reunion->id) }}"
            class="inline-flex items-center gap-2 px-4 py-2 bg-white text-gray-700 hover:text-purple-600 font-medium rounded-lg shadow-sm hover:shadow-md transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Auditoría
            </a>
            @endif
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Dashboard</h1>
                <p class="text-gray-500 text-sm">{{ $reunion->titulo }} · {{ \Carbon\Carbon::parse($reunion->fecha_hora)->format('d/m/Y H:i') }}</p>
            </div>
        </div>
        {{-- Score general --}}
        <div class="flex items-center gap-3 bg-white px-6 py-3 rounded-2xl shadow-sm border border-gray-200">
            <div class="text-center">
                <p class="text-xs text-gray-500 font-medium uppercase">Score general</p>
                <p class="text-3xl font-bold
                    {{ $scoreGeneral >= 70 ? 'text-green-600' : ($scoreGeneral >= 40 ? 'text-yellow-600' : 'text-red-600') }}">
                    {{ $scoreGeneral }}%
                </p>
            </div>
            <div class="w-12 h-12 rounded-full flex items-center justify-center
                {{ $scoreGeneral >= 70 ? 'bg-green-100' : ($scoreGeneral >= 40 ? 'bg-yellow-100' : 'bg-red-100') }}">
                @if($scoreGeneral >= 70)
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                @elseif($scoreGeneral >= 40)
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                @else
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                @endif
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════
         FILA 1: TARJETAS KPI
    ══════════════════════════════════ --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">

        {{-- Actividades --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
                <span class="text-xs font-semibold px-2 py-1 rounded-full
                    {{ $pctActividades >= 70 ? 'bg-green-100 text-green-700' : ($pctActividades >= 40 ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700') }}">
                    {{ $pctActividades }}%
                </span>
            </div>
            <p class="text-2xl font-bold text-gray-800">{{ $actCompletadas }}/{{ $totalActividades }}</p>
            <p class="text-sm text-gray-500 mt-1">Actividades completadas</p>
            <div class="mt-3 w-full bg-gray-100 rounded-full h-1.5">
                <div class="h-1.5 rounded-full bg-blue-500 transition-all" style="width: {{ $pctActividades }}%"></div>
            </div>
        </div>

        {{-- Compromisos --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 bg-purple-100 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                    </svg>
                </div>
                <span class="text-xs font-semibold px-2 py-1 rounded-full
                    {{ $pctCompromisos >= 70 ? 'bg-green-100 text-green-700' : ($pctCompromisos >= 40 ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700') }}">
                    {{ $pctCompromisos }}%
                </span>
            </div>
            <p class="text-2xl font-bold text-gray-800">{{ $compCumplidos }}/{{ $totalCompromisos }}</p>
            <p class="text-sm text-gray-500 mt-1">Compromisos cumplidos</p>
            <div class="mt-3 w-full bg-gray-100 rounded-full h-1.5">
                <div class="h-1.5 rounded-full bg-purple-500 transition-all" style="width: {{ $pctCompromisos }}%"></div>
            </div>
        </div>

        {{-- Asistencia --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 bg-green-100 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                </div>
                <span class="text-xs font-semibold px-2 py-1 rounded-full bg-green-100 text-green-700">
                    {{ $totalInvitados }} participantes
                </span>
            </div>
            <p class="text-2xl font-bold text-gray-800">{{ $palabrasTranscripcion }}</p>
            <p class="text-sm text-gray-500 mt-1">Palabras transcritas</p>
            <p class="text-xs text-gray-400 mt-1">{{ $fragmentos }} fragmentos capturados</p>
        </div>

        {{-- Compromisos vencidos --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 {{ $compVencidos > 0 ? 'bg-red-100' : 'bg-gray-100' }} rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 {{ $compVencidos > 0 ? 'text-red-600' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                @if($compVencidos > 0)
                    <span class="text-xs font-semibold px-2 py-1 rounded-full bg-red-100 text-red-700">Atención</span>
                @else
                    <span class="text-xs font-semibold px-2 py-1 rounded-full bg-green-100 text-green-700">Al día</span>
                @endif
            </div>
            <p class="text-2xl font-bold {{ $compVencidos > 0 ? 'text-red-600' : 'text-gray-800' }}">{{ $compVencidos }}</p>
            <p class="text-sm text-gray-500 mt-1">Compromisos vencidos</p>
            <p class="text-xs text-gray-400 mt-1">{{ $actVencidas }} actividades vencidas</p>
        </div>

    </div>

    {{-- ══════════════════════════════════
         FILA 2: GRÁFICAS
    ══════════════════════════════════ --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">

        {{-- Dona actividades --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-base font-bold text-gray-800 mb-4 flex items-center gap-2">
                <span class="w-3 h-3 bg-blue-500 rounded-full"></span>
                Estado de Actividades
            </h3>
            @if($totalActividades > 0)
                <div class="relative h-48">
                    <canvas id="chartActividades"></canvas>
                </div>
                <div class="mt-4 grid grid-cols-2 gap-2 text-xs">
                    <div class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-emerald-500"></span> Completadas: {{ $actCompletadas }}</div>
                    <div class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-blue-500"></span> En curso: {{ $actEnCurso }}</div>
                    <div class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-amber-500"></span> Pendientes: {{ $actPendientes }}</div>
                    <div class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-red-500"></span> Vencidas: {{ $actVencidas }}</div>
                </div>
            @else
                <div class="h-48 flex items-center justify-center text-gray-400 text-sm">Sin actividades registradas</div>
            @endif
        </div>

        {{-- Dona compromisos --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-base font-bold text-gray-800 mb-4 flex items-center gap-2">
                <span class="w-3 h-3 bg-purple-500 rounded-full"></span>
                Estado de Compromisos
            </h3>
            @if($totalCompromisos > 0)
                <div class="relative h-48">
                    <canvas id="chartCompromisos"></canvas>
                </div>
                <div class="mt-4 grid grid-cols-3 gap-2 text-xs">
                    <div class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-emerald-500"></span> Cumplidos: {{ $compCumplidos }}</div>
                    <div class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-amber-500"></span> Pendientes: {{ $compPendientes }}</div>
                    <div class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-red-500"></span> Vencidos: {{ $compVencidos }}</div>
                </div>
            @else
                <div class="h-48 flex items-center justify-center text-gray-400 text-sm">Sin compromisos registrados</div>
            @endif
        </div>

        {{-- Barras por vencimiento --}}
<div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
    <h3 class="text-base font-bold text-gray-800 mb-4 flex items-center gap-2">
        <span class="w-3 h-3 bg-orange-500 rounded-full"></span>
        Compromisos por Vencimiento
    </h3>
    @if($totalCompromisos > 0)
        <div class="relative h-48">
            <canvas id="chartVencimientos"></canvas>
        </div>
        <div class="mt-4 grid grid-cols-2 gap-1 text-xs">
            <div class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-red-500"></span> Vencidos</div>
            <div class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-orange-500"></span> Esta semana</div>
            <div class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-yellow-500"></span> Próx. semana</div>
            <div class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-blue-500"></span> Este mes</div>
            <div class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-green-500"></span> Más adelante</div>
        </div>
    @else
        <div class="h-48 flex items-center justify-center text-gray-400 text-sm">Sin compromisos registrados</div>
    @endif
</div>

    </div>

    {{-- ══════════════════════════════════
         FILA 3: SEGUIMIENTO + DETALLE
    ══════════════════════════════════ --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">

        {{-- Seguimiento reunión anterior --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="bg-gradient-to-r from-amber-50 to-orange-50 px-6 py-4 border-b border-gray-200">
                <h3 class="text-base font-bold text-gray-800 flex items-center gap-2">
                    <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                    </svg>
                    Seguimiento de reunión anterior
                </h3>
            </div>
            <div class="p-6">
                @if($reunion->reunionPadre)
                    <div class="mb-4 p-3 bg-amber-50 rounded-lg border border-amber-200">
                        <p class="text-xs text-amber-700 font-medium">Reunión vinculada</p>
                        <p class="text-sm font-semibold text-gray-800 mt-1">{{ $reunion->reunionPadre->titulo }}</p>
                        <p class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($reunion->reunionPadre->fecha_hora)->format('d/m/Y') }}</p>
                    </div>

                    @if($compromisosPadre->count() > 0)
                        <div class="space-y-2">
                            @foreach($compromisosPadre->take(5) as $comp)
                            <div class="flex items-center justify-between p-3 rounded-lg border
                                {{ $comp->estado === 'cumplido' ? 'bg-green-50 border-green-200' : ($comp->estado === 'vencido' ? 'bg-red-50 border-red-200' : 'bg-gray-50 border-gray-200') }}">
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm text-gray-800 truncate">{{ $comp->descripcion }}</p>
                                    <p class="text-xs text-gray-500">{{ $comp->responsable ?? 'Sin asignar' }}</p>
                                </div>
                                <span class="ml-2 text-xs font-semibold px-2 py-1 rounded-full flex-shrink-0
                                    {{ $comp->estado === 'cumplido' ? 'bg-green-100 text-green-700' : ($comp->estado === 'vencido' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700') }}">
                                    {{ $comp->estado === 'cumplido' ? '✓' : ($comp->estado === 'vencido' ? '✗' : '○') }}
                                    {{ ucfirst($comp->estado ?? 'pendiente') }}
                                </span>
                            </div>
                            @endforeach
                            @if($compromisosPadre->count() > 5)
                                <p class="text-xs text-gray-400 text-center pt-1">+{{ $compromisosPadre->count() - 5 }} compromisos más</p>
                            @endif
                        </div>
                        <div class="mt-4 grid grid-cols-2 gap-3 text-center">
                            <div class="bg-green-50 rounded-lg p-3 border border-green-200">
                                <p class="text-xl font-bold text-green-600">{{ $compPadreCumplidos }}</p>
                                <p class="text-xs text-green-700">Cumplidos</p>
                            </div>
                            <div class="bg-yellow-50 rounded-lg p-3 border border-yellow-200">
                                <p class="text-xl font-bold text-yellow-600">{{ $compPadrePendientes }}</p>
                                <p class="text-xs text-yellow-700">Pendientes</p>
                            </div>
                        </div>
                    @else
                        <p class="text-sm text-gray-400 text-center py-4">La reunión anterior no tiene compromisos registrados.</p>
                    @endif
                @else
                    <div class="text-center py-8">
                        <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                            </svg>
                        </div>
                        <p class="text-sm text-gray-500">Esta reunión no está vinculada a una anterior.</p>
                        <a href="{{ route('reuniones.edit', $reunion) }}"
                           class="mt-3 inline-block text-xs text-indigo-600 hover:underline">
                            Vincular con una reunión anterior →
                        </a>
                    </div>
                @endif
            </div>
        </div>

        {{-- Tabla detalle de compromisos --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="bg-gradient-to-r from-indigo-50 to-purple-50 px-6 py-4 border-b border-gray-200">
                <h3 class="text-base font-bold text-gray-800 flex items-center gap-2">
                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                    </svg>
                    Compromisos de esta reunión
                </h3>
            </div>
            <div class="p-4 overflow-y-auto" style="max-height: 380px;">
                @forelse($reunion->compromisos as $comp)
                    <div class="flex items-start gap-3 p-3 rounded-lg mb-2
                        {{ $comp->estado === 'cumplido' ? 'bg-green-50' : ($comp->estado === 'vencido' ? 'bg-red-50' : 'bg-gray-50') }}">
                        <span class="text-lg flex-shrink-0 mt-0.5">
                            {{ $comp->estado === 'cumplido' ? '✅' : ($comp->estado === 'vencido' ? '❌' : '⏳') }}
                        </span>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-800">{{ $comp->descripcion }}</p>
                            <p class="text-xs text-gray-500 mt-0.5">
                                {{ $comp->responsable ?? 'Sin asignar' }} ·
                                {{ \Carbon\Carbon::parse($comp->fecha)->format('d/m/Y') }}
                            </p>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8 text-gray-400 text-sm">No hay compromisos registrados</div>
                @endforelse
            </div>
        </div>

    </div>

    {{-- ══════════════════════════════════
         FILA 4: REUNIONES RELACIONADAS
    ══════════════════════════════════ --}}
    @if($reunion->reunionesHijas->count() > 0)
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden mb-8">
        <div class="bg-gradient-to-r from-teal-50 to-emerald-50 px-6 py-4 border-b border-gray-200">
            <h3 class="text-base font-bold text-gray-800 flex items-center gap-2">
                <svg class="w-5 h-5 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                </svg>
                Reuniones de seguimiento vinculadas ({{ $reunion->reunionesHijas->count() }})
            </h3>
        </div>
        <div class="p-4 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
            @foreach($reunion->reunionesHijas as $hija)
                <a href="{{ route('reuniones.dashboard', $hija->id) }}"
                   class="block p-4 rounded-xl border border-gray-200 hover:border-indigo-300 hover:shadow-md transition-all">
                    <p class="text-sm font-semibold text-gray-800 truncate">{{ $hija->titulo }}</p>
                    <p class="text-xs text-gray-500 mt-1">{{ \Carbon\Carbon::parse($hija->fecha_hora)->format('d/m/Y') }}</p>
                    <p class="text-xs text-indigo-600 mt-2 font-medium">Ver dashboard →</p>
                </a>
            @endforeach
        </div>
    </div>
    @endif

</div>
</div>

{{-- Chart.js --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
const chartDefaults = {
    plugins: { legend: { display: false } },
    responsive: true,
    maintainAspectRatio: false,
};

// ── Dona Actividades ──
@if($totalActividades > 0)
new Chart(document.getElementById('chartActividades'), {
    type: 'doughnut',
    data: {
        labels: {!! json_encode($chartActividades['labels']) !!},
        datasets: [{
            data: {!! json_encode($chartActividades['data']) !!},
            backgroundColor: {!! json_encode($chartActividades['colors']) !!},
            borderWidth: 2,
            borderColor: '#fff',
            hoverOffset: 4
        }]
    },
    options: {
        ...chartDefaults,
        plugins: {
            legend: { display: true, position: 'bottom', labels: { font: { size: 11 }, padding: 10 } }
        },
        cutout: '65%',
    }
});
@endif

// ── Dona Compromisos ──
@if($totalCompromisos > 0)
new Chart(document.getElementById('chartCompromisos'), {
    type: 'doughnut',
    data: {
        labels: {!! json_encode($chartCompromisos['labels']) !!},
        datasets: [{
            data: {!! json_encode($chartCompromisos['data']) !!},
            backgroundColor: {!! json_encode($chartCompromisos['colors']) !!},
            borderWidth: 2,
            borderColor: '#fff',
            hoverOffset: 4
        }]
    },
    options: {
        ...chartDefaults,
        plugins: {
            legend: { display: true, position: 'bottom', labels: { font: { size: 11 }, padding: 10 } }
        },
        cutout: '65%',
    }
});
@endif

// ── Barras por Vencimiento ──
@if($totalCompromisos > 0)
new Chart(document.getElementById('chartVencimientos'), {
    type: 'bar',
    data: {
        labels: {!! json_encode($chartVencimientos['labels']) !!},
        datasets: [{
            data: {!! json_encode($chartVencimientos['data']) !!},
            backgroundColor: {!! json_encode($chartVencimientos['colors']) !!},
            borderRadius: 6,
            borderWidth: 0,
        }]
    },
    options: {
        ...chartDefaults,
        plugins: {
            legend: { display: false }
        },
        scales: {
            x: { grid: { display: false }, ticks: { font: { size: 10 } } },
            y: { beginAtZero: true, ticks: { stepSize: 1, font: { size: 10 } } }
        }
    }
});
@endif
</script>
@endsection