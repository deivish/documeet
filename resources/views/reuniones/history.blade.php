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

    {{-- Búsqueda y Filtros --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 mb-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            {{-- Búsqueda --}}
            <div class="md:col-span-2">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Buscar reunión</label>
                <div class="relative">
                    <input type="text" 
                           id="searchInput"
                           placeholder="Buscar por título o descripción..."
                           class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition">
                    <svg class="w-5 h-5 text-gray-400 absolute left-3 top-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z">
                        </path>
                    </svg>
                </div>
            </div>

            {{-- Filtro por tipo --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Filtrar por tipo</label>
                <select id="filterType" 
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition">
                    <option value="all">Todas las reuniones</option>
                    <option value="organized">Organizadas por mí</option>
                    <option value="invited">Donde fui invitado</option>
                </select>
            </div>
        </div>
    </div>

    @if($reunionesOrganizadas->isEmpty() && $reunionesInvitado->isEmpty())
        {{-- Estado Vacío --}}
        <div class="bg-white rounded-2xl shadow-xl border border-gray-200 p-12 text-center">
            <div class="max-w-md mx-auto">
                <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z">
                        </path>
                    </svg>
                </div>
                <h3 class="text-2xl font-bold text-gray-800 mb-2">Sin historial de reuniones</h3>
                <p class="text-gray-600 mb-6">
                    Aún no tienes reuniones pasadas o eliminadas en tu historial
                </p>
                <a href="{{ route('reuniones.index') }}" 
                   class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-xl hover:from-indigo-700 hover:to-purple-700 transition-all shadow-lg hover:shadow-xl font-medium">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                        </path>
                    </svg>
                    Ver Reuniones Activas
                </a>
            </div>
        </div>
    @else
        {{-- Sección: Reuniones organizadas por mí --}}
        @if(!$reunionesOrganizadas->isEmpty())
            <div class="mb-12 section-organized" data-type="organized">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z">
                            </path>
                        </svg>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-800">Reuniones Organizadas por Ti</h2>
                    <span class="bg-indigo-100 text-indigo-700 text-sm font-semibold px-3 py-1 rounded-full">
                        {{ $reunionesOrganizadas->count() }}
                    </span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($reunionesOrganizadas as $reunion)
                        <div class="reunion-card bg-white rounded-2xl shadow-md border border-gray-200 hover:shadow-xl transition-all overflow-hidden group"
                             data-title="{{ strtolower($reunion->titulo) }}"
                             data-description="{{ strtolower($reunion->descripcion) }}"
                             data-type="organized">
                            
                            {{-- Header de la card --}}
                            <div class="bg-gradient-to-r from-indigo-500 to-purple-600 p-4 text-white">
                                <div class="flex items-start justify-between mb-2">
                                    <span class="bg-white/20 backdrop-blur text-white text-xs font-semibold px-2 py-1 rounded-full">
                                        Organizador
                                    </span>
                                    @if($reunion->deleted_at)
                                    <span class="bg-red-500/30 backdrop-blur text-white text-xs font-semibold px-2 py-1 rounded-full">
                                        Eliminada
                                    </span>
                                    @else
                                    <span class="bg-blue-500/30 backdrop-blur text-white text-xs font-semibold px-2 py-1 rounded-full">
                                        Finalizada
                                    </span>
                                    @endif
                                </div>
                                <h3 class="text-xl font-bold line-clamp-2 group-hover:text-indigo-100 transition">
                                    {{ $reunion->titulo }}
                                </h3>
                            </div>

                            {{-- Contenido de la card --}}
                            <div class="p-6">
                                <div class="flex items-center gap-2 text-sm text-gray-500 mb-3">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                        </path>
                                    </svg>
                                    {{ \Carbon\Carbon::parse($reunion->fecha_hora)->format('d/m/Y H:i') }}
                                </div>

                                <p class="text-gray-600 text-sm line-clamp-3 mb-4">
                                    {{ $reunion->descripcion }}
                                </p>

                                {{-- Estadísticas --}}
                                <div class="flex items-center gap-4 mb-4 pb-4 border-b border-gray-200">
                                    <div class="flex items-center gap-1 text-xs text-gray-500">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z">
                                            </path>
                                        </svg>
                                        {{ $reunion->invitados->count() }} invitados
                                    </div>
                                    <div class="flex items-center gap-1 text-xs text-gray-500">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                                            </path>
                                        </svg>
                                        {{ $reunion->actividades->whereNull('deleted_at')->count() }} tareas
                                    </div>
                                </div>

                                <a href="{{ route('reuniones.detalle_historial', $reunion->id) }}" 
                                   class="inline-flex items-center text-indigo-600 hover:text-indigo-800 font-semibold text-sm group">
                                    Ver detalles completos
                                    <svg class="w-4 h-4 ml-1 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Sección: Reuniones donde fui invitado --}}
        @if(!$reunionesInvitado->isEmpty())
            <div class="section-invited" data-type="invited">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z">
                            </path>
                        </svg>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-800">Reuniones Donde Fuiste Invitado</h2>
                    <span class="bg-green-100 text-green-700 text-sm font-semibold px-3 py-1 rounded-full">
                        {{ $reunionesInvitado->count() }}
                    </span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($reunionesInvitado as $reunion)
                        <div class="reunion-card bg-white rounded-2xl shadow-md border border-gray-200 hover:shadow-xl transition-all overflow-hidden group"
                             data-title="{{ strtolower($reunion->titulo) }}"
                             data-description="{{ strtolower($reunion->descripcion) }}"
                             data-type="invited">
                            
                            {{-- Header de la card --}}
                            <div class="bg-gradient-to-r from-green-500 to-emerald-600 p-4 text-white">
                                <div class="flex items-start justify-between mb-2">
                                    <span class="bg-white/20 backdrop-blur text-white text-xs font-semibold px-2 py-1 rounded-full">
                                        Invitado
                                    </span>
                                    @if($reunion->deleted_at)
                                    <span class="bg-red-500/30 backdrop-blur text-white text-xs font-semibold px-2 py-1 rounded-full">
                                        Eliminada
                                    </span>
                                    @else
                                    <span class="bg-blue-500/30 backdrop-blur text-white text-xs font-semibold px-2 py-1 rounded-full">
                                        Finalizada
                                    </span>
                                    @endif
                                </div>
                                <h3 class="text-xl font-bold line-clamp-2 group-hover:text-green-100 transition">
                                    {{ $reunion->titulo }}
                                </h3>
                            </div>

                            {{-- Contenido de la card --}}
                            <div class="p-6">
                                <div class="flex items-center gap-2 text-sm text-gray-500 mb-3">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                        </path>
                                    </svg>
                                    {{ \Carbon\Carbon::parse($reunion->fecha_hora)->format('d/m/Y H:i') }}
                                </div>

                                <p class="text-gray-600 text-sm line-clamp-3 mb-4">
                                    {{ $reunion->descripcion }}
                                </p>

                                {{-- Organizador --}}
                                <div class="flex items-center gap-2 mb-4 pb-4 border-b border-gray-200">
                                    <div class="w-8 h-8 bg-gradient-to-br from-green-500 to-emerald-600 rounded-full flex items-center justify-center flex-shrink-0">
                                        <span class="text-white text-xs font-bold">
                                            {{ strtoupper(substr($reunion->organizador->name, 0, 2)) }}
                                        </span>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-xs text-gray-500">Organizada por</p>
                                        <p class="text-sm font-semibold text-gray-800 truncate">{{ $reunion->organizador->name }}</p>
                                    </div>
                                </div>

                                <a href="{{ route('reuniones.detalle_historial', $reunion->id) }}" 
                                   class="inline-flex items-center text-green-600 hover:text-green-800 font-semibold text-sm group">
                                    Ver detalles completos
                                    <svg class="w-4 h-4 ml-1 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    @endif
</div>

{{-- JavaScript para Búsqueda y Filtros --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const filterType = document.getElementById('filterType');
    const cards = document.querySelectorAll('.reunion-card');
    const sectionOrganized = document.querySelector('.section-organized');
    const sectionInvited = document.querySelector('.section-invited');

    function filterCards() {
        const searchTerm = searchInput?.value.toLowerCase() || '';
        const typeFilter = filterType?.value || 'all';
        
        let organizedVisible = 0;
        let invitedVisible = 0;

        cards.forEach(card => {
            const title = card.dataset.title;
            const description = card.dataset.description;
            const type = card.dataset.type;
            
            const matchesSearch = title.includes(searchTerm) || description.includes(searchTerm);
            const matchesType = typeFilter === 'all' || type === typeFilter;
            
            if (matchesSearch && matchesType) {
                card.style.display = 'block';
                if (type === 'organized') organizedVisible++;
                if (type === 'invited') invitedVisible++;
            } else {
                card.style.display = 'none';
            }
        });

        // Mostrar/ocultar secciones
        if (sectionOrganized) {
            sectionOrganized.style.display = organizedVisible > 0 ? 'block' : 'none';
        }
        if (sectionInvited) {
            sectionInvited.style.display = invitedVisible > 0 ? 'block' : 'none';
        }
    }

    searchInput?.addEventListener('input', filterCards);
    filterType?.addEventListener('change', filterCards);
});
</script>
@endsection