@extends('layouts.app')

@section('titulo')
    Historial de Reuniones
@endsection

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    
    {{-- Header con Breadcrumb y Acciones --}}
    <div class="mb-8">
        <a href="{{ route('post.index') }}" 
           class="inline-flex items-center text-gray-600 hover:text-indigo-600 font-medium transition-colors group mb-6">
            <svg class="w-5 h-5 mr-2 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            Volver a Mi Cuenta
        </a>

        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-3xl md:text-4xl font-bold text-gray-800 flex items-center gap-3">
                    <div class="w-12 h-12 bg-gradient-to-br from-gray-500 to-gray-600 rounded-xl flex items-center justify-center shadow-lg">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z">
                            </path>
                        </svg>
                    </div>
                    Historial de Reuniones
                </h1>
                <p class="text-gray-600 mt-2">Consulta reuniones pasadas y eliminadas</p>
            </div>

            {{-- Estadísticas Rápidas --}}
            <div class="flex gap-4">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 px-4 py-3">
                    <div class="flex items-center gap-2">
                        <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z">
                                </path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-gray-800">{{ $reunionesOrganizadas->count() }}</p>
                            <p class="text-xs text-gray-500">Organizadas</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-200 px-4 py-3">
                    <div class="flex items-center gap-2">
                        <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z">
                                </path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-gray-800">{{ $reunionesInvitado->count() }}</p>
                            <p class="text-xs text-gray-500">Invitado</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Búsqueda y Filtros mejorados --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
            {{-- Búsqueda principal --}}
            <div class="md:col-span-2">
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Buscar reunión, actividad o compromiso
                </label>
                <div class="relative">
                    <input type="text"
                           id="searchInput"
                           placeholder="Ej: revisión de presupuesto, entregar informe..."
                           class="w-full pl-10 pr-10 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition text-sm">
                    <svg class="w-5 h-5 text-gray-400 absolute left-3 top-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    {{-- Botón limpiar --}}
                    <button id="clearSearch" class="hidden absolute right-3 top-3.5 text-gray-400 hover:text-gray-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>

            {{-- Filtro por tipo --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Filtrar por tipo</label>
                <select id="filterType"
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition text-sm">
                    <option value="all">Todas las reuniones</option>
                    <option value="organized">Organizadas por mí</option>
                    <option value="invited">Donde fui invitado</option>
                </select>
            </div>
        </div>

        {{-- Filtros rápidos por campo --}}
        <div class="flex flex-wrap items-center gap-2">
            <span class="text-xs text-gray-500 font-medium">Buscar en:</span>
            <button data-campo="all" onclick="setCampo(this)"
                class="campo-btn px-3 py-1.5 text-xs font-medium rounded-lg bg-indigo-600 text-white transition-all">
                Todo
            </button>
            <button data-campo="titulo" onclick="setCampo(this)"
                class="campo-btn px-3 py-1.5 text-xs font-medium rounded-lg bg-gray-100 text-gray-600 hover:bg-gray-200 transition-all">
                Título reunión
            </button>
            <button data-campo="descripcion" onclick="setCampo(this)"
                class="campo-btn px-3 py-1.5 text-xs font-medium rounded-lg bg-gray-100 text-gray-600 hover:bg-gray-200 transition-all">
                Descripción
            </button>
            <button data-campo="actividades" onclick="setCampo(this)"
                class="campo-btn px-3 py-1.5 text-xs font-medium rounded-lg bg-gray-100 text-gray-600 hover:bg-gray-200 transition-all">
                Actividades
            </button>
            <button data-campo="compromisos" onclick="setCampo(this)"
                class="campo-btn px-3 py-1.5 text-xs font-medium rounded-lg bg-gray-100 text-gray-600 hover:bg-gray-200 transition-all">
                Compromisos
            </button>
        </div>

        {{-- Resultados info --}}
        <div id="search-info" class="hidden mt-3 text-sm text-gray-500">
            Mostrando <span id="result-count" class="font-semibold text-indigo-600">0</span> resultado(s) para
            "<span id="search-term" class="font-semibold text-gray-700"></span>"
        </div>
    </div>

    @if($reunionesOrganizadas->isEmpty() && $reunionesInvitado->isEmpty())
        <div class="bg-white rounded-2xl shadow-xl border border-gray-200 p-12 text-center">
            <div class="max-w-md mx-auto">
                <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h3 class="text-2xl font-bold text-gray-800 mb-2">Sin historial de reuniones</h3>
                <p class="text-gray-600 mb-6">Aún no tienes reuniones pasadas o eliminadas en tu historial</p>
                <a href="{{ route('reuniones.index') }}"
                   class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-xl hover:from-indigo-700 hover:to-purple-700 transition-all shadow-lg font-medium">
                    Ver Reuniones Activas
                </a>
            </div>
        </div>
    @else

        {{-- Sin resultados (se muestra/oculta con JS) --}}
        <div id="no-results" class="hidden bg-white rounded-2xl shadow-sm border border-gray-200 p-12 text-center mb-8">
            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>
            <p class="text-gray-600 font-medium">No se encontraron reuniones</p>
            <p class="text-gray-400 text-sm mt-1">Intenta con otro término de búsqueda</p>
            <button onclick="limpiarBusqueda()" class="mt-4 text-indigo-600 text-sm hover:underline">Limpiar búsqueda</button>
        </div>

        {{-- ══════ Reuniones organizadas ══════ --}}
        @if(!$reunionesOrganizadas->isEmpty())
        <div class="mb-12 section-organized" data-type="organized">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
                <h2 class="text-2xl font-bold text-gray-800">Reuniones Organizadas por Ti</h2>
                <span class="section-count bg-indigo-100 text-indigo-700 text-sm font-semibold px-3 py-1 rounded-full">
                    {{ $reunionesOrganizadas->count() }}
                </span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($reunionesOrganizadas as $reunion)
                {{-- Preparar datos de búsqueda --}}
                @php
                    $actividadesTexto = $reunion->actividades->pluck('nombre')->implode(' | ');
                    $actividadesDescTexto = $reunion->actividades->pluck('descripcion')->implode(' | ');
                    $compromisosTexto = $reunion->compromisos->pluck('descripcion')->implode(' | ');
                    $responsablesTexto = $reunion->actividades->pluck('responsable')->merge($reunion->compromisos->pluck('responsable'))->implode(' | ');
                @endphp
                <div class="reunion-card bg-white rounded-2xl shadow-md border border-gray-200 hover:shadow-xl transition-all overflow-hidden group"
                     data-titulo="{{ strtolower($reunion->titulo) }}"
                     data-descripcion="{{ strtolower($reunion->descripcion ?? '') }}"
                     data-actividades="{{ strtolower($actividadesTexto . ' ' . $actividadesDescTexto) }}"
                     data-compromisos="{{ strtolower($compromisosTexto) }}"
                     data-responsables="{{ strtolower($responsablesTexto) }}"
                     data-type="organized">

                    {{-- Header de la card --}}
                    <div class="bg-gradient-to-r from-indigo-500 to-purple-600 p-4 text-white">
                        <div class="flex items-start justify-between mb-2">
                            <span class="bg-white/20 backdrop-blur text-white text-xs font-semibold px-2 py-1 rounded-full">
                                Organizador
                            </span>
                            @if($reunion->deleted_at)
                            <span class="bg-red-500/30 backdrop-blur text-white text-xs font-semibold px-2 py-1 rounded-full">Eliminada</span>
                            @else
                            <span class="bg-blue-500/30 backdrop-blur text-white text-xs font-semibold px-2 py-1 rounded-full">Finalizada</span>
                            @endif
                        </div>
                        <h3 class="text-xl font-bold line-clamp-2 group-hover:text-indigo-100 transition">
                            {{ $reunion->titulo }}
                        </h3>
                    </div>

                    {{-- Contenido --}}
                    <div class="p-6">
                        <div class="flex items-center gap-2 text-sm text-gray-500 mb-3">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            {{ \Carbon\Carbon::parse($reunion->fecha_hora)->format('d/m/Y H:i') }}
                        </div>

                        <p class="text-gray-600 text-sm line-clamp-2 mb-4">{{ $reunion->descripcion }}</p>

                        {{-- Badges de actividades y compromisos --}}
                        <div class="flex flex-wrap gap-2 mb-4">
                            @if($reunion->actividades->count() > 0)
                            <span class="inline-flex items-center gap-1 px-2 py-1 bg-blue-50 text-blue-700 text-xs font-medium rounded-full">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                                {{ $reunion->actividades->count() }} actividades
                            </span>
                            @endif
                            @if($reunion->compromisos->count() > 0)
                            <span class="inline-flex items-center gap-1 px-2 py-1 bg-purple-50 text-purple-700 text-xs font-medium rounded-full">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                                </svg>
                                {{ $reunion->compromisos->count() }} compromisos
                            </span>
                            @endif
                            <span class="inline-flex items-center gap-1 px-2 py-1 bg-gray-50 text-gray-600 text-xs font-medium rounded-full">
                                {{ $reunion->invitados->count() }} participantes
                            </span>
                        </div>

                        {{-- Coincidencia de búsqueda (se muestra dinámicamente) --}}
                        <div class="match-info hidden mb-3 p-2 bg-yellow-50 border border-yellow-200 rounded-lg text-xs text-yellow-800">
                        </div>

                        <div class="border-t border-gray-100 pt-4">
                            <a href="{{ route('reuniones.detalle_historial', $reunion->id) }}"
                               class="inline-flex items-center text-indigo-600 hover:text-indigo-800 font-semibold text-sm group">
                                Ver detalles completos
                                <svg class="w-4 h-4 ml-1 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- ══════ Reuniones como invitado ══════ --}}
        @if(!$reunionesInvitado->isEmpty())
        <div class="section-invited" data-type="invited">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                </div>
                <h2 class="text-2xl font-bold text-gray-800">Reuniones Donde Fuiste Invitado</h2>
                <span class="section-count bg-green-100 text-green-700 text-sm font-semibold px-3 py-1 rounded-full">
                    {{ $reunionesInvitado->count() }}
                </span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($reunionesInvitado as $reunion)
                @php
                    $actividadesTexto = $reunion->actividades->pluck('nombre')->implode(' | ');
                    $actividadesDescTexto = $reunion->actividades->pluck('descripcion')->implode(' | ');
                    $compromisosTexto = $reunion->compromisos->pluck('descripcion')->implode(' | ');
                    $responsablesTexto = $reunion->actividades->pluck('responsable')->merge($reunion->compromisos->pluck('responsable'))->implode(' | ');
                @endphp
                <div class="reunion-card bg-white rounded-2xl shadow-md border border-gray-200 hover:shadow-xl transition-all overflow-hidden group"
                     data-titulo="{{ strtolower($reunion->titulo) }}"
                     data-descripcion="{{ strtolower($reunion->descripcion ?? '') }}"
                     data-actividades="{{ strtolower($actividadesTexto . ' ' . $actividadesDescTexto) }}"
                     data-compromisos="{{ strtolower($compromisosTexto) }}"
                     data-responsables="{{ strtolower($responsablesTexto) }}"
                     data-type="invited">

                    <div class="bg-gradient-to-r from-green-500 to-emerald-600 p-4 text-white">
                        <div class="flex items-start justify-between mb-2">
                            <span class="bg-white/20 backdrop-blur text-white text-xs font-semibold px-2 py-1 rounded-full">Invitado</span>
                            @if($reunion->deleted_at)
                            <span class="bg-red-500/30 backdrop-blur text-white text-xs font-semibold px-2 py-1 rounded-full">Eliminada</span>
                            @else
                            <span class="bg-blue-500/30 backdrop-blur text-white text-xs font-semibold px-2 py-1 rounded-full">Finalizada</span>
                            @endif
                        </div>
                        <h3 class="text-xl font-bold line-clamp-2 group-hover:text-green-100 transition">{{ $reunion->titulo }}</h3>
                    </div>

                    <div class="p-6">
                        <div class="flex items-center gap-2 text-sm text-gray-500 mb-3">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            {{ \Carbon\Carbon::parse($reunion->fecha_hora)->format('d/m/Y H:i') }}
                        </div>

                        <p class="text-gray-600 text-sm line-clamp-2 mb-4">{{ $reunion->descripcion }}</p>

                        {{-- Organizador --}}
                        <div class="flex items-center gap-2 mb-4">
                            <div class="w-8 h-8 bg-gradient-to-br from-green-500 to-emerald-600 rounded-full flex items-center justify-center flex-shrink-0">
                                <span class="text-white text-xs font-bold">
                                    {{ strtoupper(substr($reunion->organizador->name ?? 'U', 0, 2)) }}
                                </span>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">Organizada por</p>
                                <p class="text-sm font-semibold text-gray-800">{{ $reunion->organizador->name ?? 'N/A' }}</p>
                            </div>
                        </div>

                        {{-- Badges --}}
                        <div class="flex flex-wrap gap-2 mb-4">
                            @if($reunion->actividades->count() > 0)
                            <span class="inline-flex items-center gap-1 px-2 py-1 bg-blue-50 text-blue-700 text-xs font-medium rounded-full">
                                {{ $reunion->actividades->count() }} actividades
                            </span>
                            @endif
                            @if($reunion->compromisos->count() > 0)
                            <span class="inline-flex items-center gap-1 px-2 py-1 bg-purple-50 text-purple-700 text-xs font-medium rounded-full">
                                {{ $reunion->compromisos->count() }} compromisos
                            </span>
                            @endif
                        </div>

                        {{-- Coincidencia de búsqueda --}}
                        <div class="match-info hidden mb-3 p-2 bg-yellow-50 border border-yellow-200 rounded-lg text-xs text-yellow-800">
                        </div>

                        <div class="border-t border-gray-100 pt-4">
                            <a href="{{ route('reuniones.detalle_historial', $reunion->id) }}"
                               class="inline-flex items-center text-green-600 hover:text-green-800 font-semibold text-sm group">
                                Ver detalles completos
                                <svg class="w-4 h-4 ml-1 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

    @endif
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const searchInput    = document.getElementById('searchInput');
    const clearBtn       = document.getElementById('clearSearch');
    const filterType     = document.getElementById('filterType');
    const cards          = document.querySelectorAll('.reunion-card');
    const noResults      = document.getElementById('no-results');
    const searchInfo     = document.getElementById('search-info');
    const resultCount    = document.getElementById('result-count');
    const searchTermEl   = document.getElementById('search-term');
    const sectionOrg     = document.querySelector('.section-organized');
    const sectionInv     = document.querySelector('.section-invited');

    let campoBusqueda = 'all'; // campo activo

    // ── Cambiar campo de búsqueda ──
    window.setCampo = function(btn) {
        document.querySelectorAll('.campo-btn').forEach(b => {
            b.classList.remove('bg-indigo-600', 'text-white');
            b.classList.add('bg-gray-100', 'text-gray-600');
        });
        btn.classList.remove('bg-gray-100', 'text-gray-600');
        btn.classList.add('bg-indigo-600', 'text-white');
        campoBusqueda = btn.dataset.campo;
        filtrar();
    };

    // ── Limpiar búsqueda ──
    window.limpiarBusqueda = function() {
        searchInput.value = '';
        clearBtn.classList.add('hidden');
        searchInfo.classList.add('hidden');
        filtrar();
    };

    clearBtn?.addEventListener('click', limpiarBusqueda);

    // ── Función principal de filtrado ──
    function filtrar() {
        const term       = searchInput.value.toLowerCase().trim();
        const typeFilter = filterType.value;

        // Mostrar/ocultar botón limpiar
        if (term.length > 0) {
            clearBtn.classList.remove('hidden');
        } else {
            clearBtn.classList.add('hidden');
            searchInfo.classList.add('hidden');
        }

        let totalVisible    = 0;
        let orgVisible      = 0;
        let invVisible      = 0;

        cards.forEach(card => {
            const type         = card.dataset.type;
            const matchesType  = typeFilter === 'all' || type === typeFilter;
            const matchInfo    = card.querySelector('.match-info');

            // Limpiar match anterior
            if (matchInfo) {
                matchInfo.classList.add('hidden');
                matchInfo.textContent = '';
            }

            let matchesTerm = true;
            let matchLabel  = '';

            if (term.length > 0) {
                const titulo      = card.dataset.titulo      || '';
                const descripcion = card.dataset.descripcion || '';
                const actividades = card.dataset.actividades || '';
                const compromisos = card.dataset.compromisos || '';
                const responsables= card.dataset.responsables|| '';

                if (campoBusqueda === 'all') {
                    matchesTerm = titulo.includes(term) ||
                                  descripcion.includes(term) ||
                                  actividades.includes(term) ||
                                  compromisos.includes(term) ||
                                  responsables.includes(term);

                    // Indicar dónde coincidió
                    if (actividades.includes(term))  matchLabel = '📋 Coincide en actividades';
                    if (compromisos.includes(term))  matchLabel = '✅ Coincide en compromisos';
                    if (responsables.includes(term)) matchLabel = '👤 Coincide en responsables';
                    if (descripcion.includes(term))  matchLabel = '📝 Coincide en descripción';
                    if (titulo.includes(term))       matchLabel = '🔖 Coincide en título';

                } else if (campoBusqueda === 'titulo') {
                    matchesTerm = titulo.includes(term);
                    if (matchesTerm) matchLabel = '🔖 Coincide en título';

                } else if (campoBusqueda === 'descripcion') {
                    matchesTerm = descripcion.includes(term);
                    if (matchesTerm) matchLabel = '📝 Coincide en descripción';

                } else if (campoBusqueda === 'actividades') {
                    matchesTerm = actividades.includes(term);
                    if (matchesTerm) matchLabel = '📋 Coincide en actividades';

                } else if (campoBusqueda === 'compromisos') {
                    matchesTerm = compromisos.includes(term);
                    if (matchesTerm) matchLabel = '✅ Coincide en compromisos';
                }

                // Mostrar etiqueta de coincidencia
                if (matchesTerm && matchLabel && matchInfo) {
                    matchInfo.textContent = matchLabel;
                    matchInfo.classList.remove('hidden');
                }
            }

            const visible = matchesTerm && matchesType;
            card.style.display = visible ? '' : 'none';

            if (visible) {
                totalVisible++;
                if (type === 'organized') orgVisible++;
                if (type === 'invited')   invVisible++;
            }
        });

        // Mostrar/ocultar secciones
        if (sectionOrg) sectionOrg.style.display = orgVisible > 0 ? '' : 'none';
        if (sectionInv) sectionInv.style.display = invVisible > 0 ? '' : 'none';

        // Mensaje sin resultados
        if (noResults) noResults.classList.toggle('hidden', totalVisible > 0 || term.length === 0);

        // Info de búsqueda
        if (term.length > 0) {
            searchInfo.classList.remove('hidden');
            resultCount.textContent = totalVisible;
            searchTermEl.textContent = term;
        }
    }

    searchInput?.addEventListener('input', filtrar);
    filterType?.addEventListener('change', filtrar);
});
</script>
@endsection