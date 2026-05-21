@extends('layouts.app')

@section('titulo')
    Auditoría — {{ $reunion->titulo }}
@endsection

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 py-8">
<div class="max-w-5xl mx-auto px-4">

    {{-- HEADER --}}
    <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('reuniones.dashboard', $reunion->id) }}"
               class="inline-flex items-center gap-2 px-4 py-2 bg-white text-gray-700 hover:text-indigo-600 font-medium rounded-lg shadow-sm hover:shadow-md transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Dashboard
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
                    <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Auditoría
                </h1>
                <p class="text-gray-500 text-sm">{{ $reunion->titulo }} · Solo visible para el moderador</p>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <span class="px-3 py-1.5 bg-indigo-100 text-indigo-700 text-xs font-semibold rounded-full">
                {{ $registros->total() }} eventos
            </span>
            {{-- Botón exportar CSV --}}
            <a href="{{ route('reuniones.auditoria.exportar', $reunion->id) }}"
               class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-lg transition-all shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Exportar CSV
            </a>
        </div>
    </div>

    {{-- FILTROS --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-4 mb-6">
        <div class="flex flex-wrap gap-3 items-center">
            <span class="text-sm font-medium text-gray-600">Filtrar por:</span>
            <button onclick="filtrar('todos')" id="btn-todos"
                class="filtro-btn px-3 py-1.5 text-xs font-medium rounded-lg bg-indigo-600 text-white transition-all">
                Todos
            </button>
            <button onclick="filtrar('Reunion')" id="btn-Reunion"
                class="filtro-btn px-3 py-1.5 text-xs font-medium rounded-lg bg-gray-100 text-gray-600 hover:bg-gray-200 transition-all">
                Reunión
            </button>
            <button onclick="filtrar('Actividad')" id="btn-Actividad"
                class="filtro-btn px-3 py-1.5 text-xs font-medium rounded-lg bg-gray-100 text-gray-600 hover:bg-gray-200 transition-all">
                Actividades
            </button>
            <button onclick="filtrar('Compromiso')" id="btn-Compromiso"
                class="filtro-btn px-3 py-1.5 text-xs font-medium rounded-lg bg-gray-100 text-gray-600 hover:bg-gray-200 transition-all">
                Compromisos
            </button>
            <button onclick="filtrar('Acta')" id="btn-Acta"
                class="filtro-btn px-3 py-1.5 text-xs font-medium rounded-lg bg-gray-100 text-gray-600 hover:bg-gray-200 transition-all">
                Acta
            </button>
        </div>
    </div>

    {{-- TIMELINE --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">

        @if($registros->count() > 0)
        <div class="divide-y divide-gray-100" id="auditoria-list">
            @foreach($registros as $registro)
            <div class="auditoria-item p-5 hover:bg-gray-50 transition-all"
                 data-modelo="{{ $registro->modelo }}">

                <div class="flex items-start gap-4">

                    {{-- Ícono por modelo --}}
                    <div class="flex-shrink-0 w-10 h-10 rounded-xl flex items-center justify-center
                        @if($registro->modelo === 'Reunion') bg-indigo-100
                        @elseif($registro->modelo === 'Actividad') bg-green-100
                        @elseif($registro->modelo === 'Compromiso') bg-purple-100
                        @elseif($registro->modelo === 'Acta') bg-amber-100
                        @else bg-gray-100 @endif">
                        @if($registro->modelo === 'Reunion')
                            <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        @elseif($registro->modelo === 'Actividad')
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                        @elseif($registro->modelo === 'Compromiso')
                            <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                            </svg>
                        @elseif($registro->modelo === 'Acta')
                            <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        @endif
                    </div>

                    {{-- Contenido --}}
                    <div class="flex-1 min-w-0">
                        <div class="flex items-start justify-between gap-2 flex-wrap">
                            <div>
                                <p class="text-sm font-semibold text-gray-800">{{ $registro->accion }}</p>
                                <div class="flex items-center gap-2 mt-0.5 flex-wrap">
                                    <span class="text-xs text-gray-500 font-medium">{{ $registro->usuario->name ?? 'Usuario' }}</span>
                                    <span class="text-gray-300">·</span>
                                    <span class="px-1.5 py-0.5 rounded text-xs font-medium
                                        @if($registro->modelo === 'Reunion') bg-indigo-50 text-indigo-600
                                        @elseif($registro->modelo === 'Actividad') bg-green-50 text-green-600
                                        @elseif($registro->modelo === 'Compromiso') bg-purple-50 text-purple-600
                                        @elseif($registro->modelo === 'Acta') bg-amber-50 text-amber-600
                                        @else bg-gray-100 text-gray-600 @endif">
                                        {{ $registro->modelo }}
                                        @if($registro->modelo_id) #{{ $registro->modelo_id }} @endif
                                    </span>
                                </div>
                            </div>
                            <span class="text-xs text-gray-400 flex-shrink-0 whitespace-nowrap">
                                {{ \Carbon\Carbon::parse($registro->created_at)->format('d/m/Y H:i:s') }}
                            </span>
                        </div>

                        {{-- Cambios detallados --}}
                        @if($registro->valores_anteriores && $registro->valores_nuevos)
                        <div class="mt-3 grid grid-cols-1 sm:grid-cols-2 gap-2">
                            <div class="bg-red-50 rounded-lg p-3 border border-red-100">
                                <p class="text-xs font-semibold text-red-600 mb-1.5 flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                                    </svg>
                                    Antes
                                </p>
                                @foreach($registro->valores_anteriores as $campo => $valor)
                                    <div class="flex gap-1 text-xs">
                                        <span class="font-medium text-red-700 flex-shrink-0">{{ $campo }}:</span>
                                        <span class="text-red-600 break-all">
                                            @if(\Carbon\Carbon::hasFormat($valor, 'Y-m-d H:i:s') || \Carbon\Carbon::hasFormat($valor, 'Y-m-d\TH:i:s.uZ'))
                                                {{ \Carbon\Carbon::parse($valor)->format('d/m/Y H:i') }}
                                            @else
                                                {{ \Str::limit((string)$valor, 80) }}
                                            @endif
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                            <div class="bg-green-50 rounded-lg p-3 border border-green-100">
                                <p class="text-xs font-semibold text-green-600 mb-1.5 flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                    </svg>
                                    Después
                                </p>
                                @foreach($registro->valores_nuevos as $campo => $valor)
                                    <div class="flex gap-1 text-xs">
                                        <span class="font-medium text-green-700 flex-shrink-0">{{ $campo }}:</span>
                                        <span class="text-green-600 break-all">
                                            @if(\Carbon\Carbon::hasFormat($valor, 'Y-m-d H:i:s') || \Carbon\Carbon::hasFormat($valor, 'Y-m-d\TH:i:s.uZ'))
                                                {{ \Carbon\Carbon::parse($valor)->format('d/m/Y H:i') }}
                                            @else
                                                {{ \Str::limit((string)$valor, 80) }}
                                            @endif
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        @elseif($registro->valores_nuevos)
                        <div class="mt-2 bg-blue-50 rounded-lg p-2.5 border border-blue-100">
                            @foreach($registro->valores_nuevos as $campo => $valor)
                                <div class="flex gap-1 text-xs">
                                    <span class="font-medium text-blue-700 flex-shrink-0">{{ $campo }}:</span>
                                    <span class="text-blue-600 break-all">{{ \Str::limit((string)$valor, 80) }}</span>
                                </div>
                            @endforeach
                        </div>
                        @elseif($registro->valores_anteriores)
                        <div class="mt-2 bg-red-50 rounded-lg p-2.5 border border-red-100">
                            @foreach($registro->valores_anteriores as $campo => $valor)
                                <div class="flex gap-1 text-xs">
                                    <span class="font-medium text-red-700 flex-shrink-0">{{ $campo }}:</span>
                                    <span class="text-red-600 break-all">{{ \Str::limit((string)$valor, 80) }}</span>
                                </div>
                            @endforeach
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        {{-- Paginación --}}
        @if($registros->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $registros->links() }}
        </div>
        @endif

        @else
        <div class="text-center py-16">
            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <p class="text-gray-500 font-medium">No hay eventos de auditoría registrados</p>
            <p class="text-gray-400 text-sm mt-1">Los cambios realizados en esta reunión aparecerán aquí</p>
        </div>
        @endif
    </div>

</div>
</div>

<script>
function filtrar(modelo) {
    const items   = document.querySelectorAll('.auditoria-item');
    const botones = document.querySelectorAll('.filtro-btn');

    botones.forEach(b => {
        b.classList.remove('bg-indigo-600', 'text-white');
        b.classList.add('bg-gray-100', 'text-gray-600');
    });

    const btnActivo = document.getElementById('btn-' + modelo);
    if (btnActivo) {
        btnActivo.classList.remove('bg-gray-100', 'text-gray-600');
        btnActivo.classList.add('bg-indigo-600', 'text-white');
    }

    items.forEach(item => {
        item.style.display = (modelo === 'todos' || item.dataset.modelo === modelo) ? '' : 'none';
    });
}
</script>
@endsection