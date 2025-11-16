@extends('layouts.app')

@section('titulo')
    Historial - Detalles de Reunión
@endsection

@section('content')
<div class="max-w-6xl mx-auto px-4 py-8">
    
    {{-- Header con Breadcrumb --}}
    <div class="mb-8">
        <a href="{{ route('reuniones.historial') }}" 
           class="inline-flex items-center text-gray-600 hover:text-indigo-600 font-medium transition-colors group mb-6">
            <svg class="w-5 h-5 mr-2 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            Volver al Historial
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        {{-- Columna Principal: Información de la Reunión --}}
        <div class="lg:col-span-2 space-y-6">
            
            {{-- Card Principal de la Reunión (Historial) --}}
            <div class="bg-white rounded-2xl shadow-xl border border-gray-200 overflow-hidden">
                {{-- Header con estilo de historial (gris) --}}
                <div class="bg-gradient-to-r from-gray-500 to-gray-600 p-8 text-white relative overflow-hidden">
                    {{-- Patrón de fondo --}}
                    <div class="absolute inset-0 opacity-10">
                        <svg class="w-full h-full" xmlns="http://www.w3.org/2000/svg">
                            <pattern id="pattern" x="0" y="0" width="40" height="40" patternUnits="userSpaceOnUse">
                                <circle cx="20" cy="20" r="2" fill="white"/>
                            </pattern>
                            <rect width="100%" height="100%" fill="url(#pattern)"/>
                        </svg>
                    </div>

                    <div class="relative z-10">
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="bg-white/20 backdrop-blur text-white text-xs font-semibold px-3 py-1 rounded-full flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z">
                                        </path>
                                    </svg>
                                    Historial
                                </span>
                                @if($reunion->deleted_at)
                                <span class="bg-red-500/30 backdrop-blur text-white text-xs font-semibold px-3 py-1 rounded-full flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                        </path>
                                    </svg>
                                    Eliminada
                                </span>
                                @else
                                <span class="bg-blue-500/30 backdrop-blur text-white text-xs font-semibold px-3 py-1 rounded-full flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    Finalizada
                                </span>
                                @endif
                            </div>
                        </div>
                        
                        <h1 class="text-3xl md:text-4xl font-bold mb-3">{{ $reunion->titulo }}</h1>
                        <p class="text-gray-200 text-lg leading-relaxed">{{ $reunion->descripcion }}</p>
                    </div>
                </div>

                {{-- Información de la Reunión --}}
                <div class="p-8">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Fecha y Hora --}}
                        <div class="flex items-start gap-4">
                            <div class="w-12 h-12 bg-gray-100 rounded-xl flex items-center justify-center flex-shrink-0">
                                <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                    </path>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-gray-500 uppercase tracking-wide">Fecha y Hora</p>
                                <p class="text-lg font-bold text-gray-800 mt-1">
                                    {{ \Carbon\Carbon::parse($reunion->fecha_hora)->format('d/m/Y') }}
                                </p>
                                <p class="text-md text-gray-600">
                                    {{ \Carbon\Carbon::parse($reunion->fecha_hora)->format('H:i') }}
                                </p>
                                <p class="text-xs text-gray-400 mt-1">
                                    Hace {{ \Carbon\Carbon::parse($reunion->fecha_hora)->diffForHumans() }}
                                </p>
                            </div>
                        </div>

                        {{-- Organizador --}}
                        <div class="flex items-start gap-4">
                            <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center flex-shrink-0">
                                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z">
                                    </path>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-gray-500 uppercase tracking-wide">Organizador</p>
                                <p class="text-lg font-bold text-gray-800 mt-1">{{ $reunion->organizador->name ?? 'Desconocido' }}</p>
                                @if($reunion->organizador)
                                <p class="text-sm text-gray-600">{{ $reunion->organizador->email }}</p>
                                @endif
                            </div>
                        </div>

                        {{-- Actividades --}}
                        <div class="flex items-start gap-4">
                            <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center flex-shrink-0">
                                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                                    </path>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-gray-500 uppercase tracking-wide">Actividades</p>
                                <p class="text-lg font-bold text-gray-800 mt-1">
                                    {{ $reunion->actividades->whereNull('deleted_at')->count() }} tareas
                                </p>
                                <p class="text-sm text-gray-600">Fueron asignadas</p>
                            </div>
                        </div>

                        {{-- Participantes --}}
                        <div class="flex items-start gap-4">
                            <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center flex-shrink-0">
                                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                        d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z">
                                    </path>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-gray-500 uppercase tracking-wide">Participantes</p>
                                <p class="text-lg font-bold text-gray-800 mt-1">
                                    {{ $invitados->count() }} personas
                                </p>
                                <p class="text-sm text-gray-600">Participaron</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Sección de Participantes --}}
            <div class="bg-white rounded-2xl shadow-xl border border-gray-200 overflow-hidden">
                <div class="bg-gradient-to-r from-blue-50 to-indigo-50 px-8 py-6 border-b border-gray-200">
                    <h2 class="text-2xl font-bold text-gray-800 flex items-center gap-3">
                        <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                                </path>
                            </svg>
                        </div>
                        Participantes de la Reunión
                    </h2>
                    <p class="text-gray-600 mt-2">Personas que asistieron a esta reunión</p>
                </div>

                <div class="p-8">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @forelse ($invitados as $invitado)
                            <div class="flex items-center gap-4 p-4 bg-gray-50 rounded-xl hover:bg-gray-100 transition-colors">
                                <div class="w-12 h-12 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-full flex items-center justify-center flex-shrink-0">
                                    <span class="text-white text-lg font-bold">
                                        {{ strtoupper(substr($invitado->name, 0, 2)) }}
                                    </span>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="font-semibold text-gray-800 truncate">{{ $invitado->name }}</p>
                                    <p class="text-sm text-gray-500 truncate">{{ $invitado->email }}</p>
                                </div>
                                <div class="flex-shrink-0">
                                    <span class="w-2 h-2 bg-green-500 rounded-full block"></span>
                                </div>
                            </div>
                        @empty
                            <div class="col-span-2 text-center py-8">
                                <svg class="w-16 h-16 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                                    </path>
                                </svg>
                                <p class="text-gray-500 font-medium">No hubo participantes</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- Sección de Actividades --}}
            <div class="bg-white rounded-2xl shadow-xl border border-gray-200 overflow-hidden">
                <div class="bg-gradient-to-r from-green-50 to-emerald-50 px-8 py-6 border-b border-gray-200">
                    <h2 class="text-2xl font-bold text-gray-800 flex items-center gap-3">
                        <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4">
                                </path>
                            </svg>
                        </div>
                        Actividades de la Reunión
                    </h2>
                    <p class="text-gray-600 mt-2">Tareas que fueron asignadas durante la reunión</p>
                </div>

                <div class="p-8">
                    @forelse ($reunion->actividades->whereNull('deleted_at') as $actividad)
                        <div class="mb-4 last:mb-0 bg-gradient-to-r from-gray-50 to-white border-l-4 border-gray-400 rounded-xl p-6 hover:shadow-lg transition-all">
                            <div class="flex items-start justify-between gap-4">
                                <div class="flex-1">
                                    <h3 class="text-xl font-bold text-gray-800 mb-2">{{ $actividad->nombre }}</h3>
                                    <p class="text-gray-600 mb-4">{{ $actividad->descripcion }}</p>
                                    
                                    <div class="flex flex-wrap gap-4">
                                        {{-- Responsable --}}
                                        <div class="flex items-center gap-2">
                                            <div class="w-8 h-8 bg-indigo-100 rounded-full flex items-center justify-center">
                                                <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z">
                                                    </path>
                                                </svg>
                                            </div>
                                            <div>
                                                <p class="text-xs text-gray-500 font-medium">Responsable</p>
                                                <p class="text-sm font-semibold text-gray-800">{{ $actividad->responsable ?? 'Sin asignar' }}</p>
                                            </div>
                                        </div>

                                        {{-- Fecha Límite --}}
                                        <div class="flex items-center gap-2">
                                            <div class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center">
                                                <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z">
                                                    </path>
                                                </svg>
                                            </div>
                                            <div>
                                                <p class="text-xs text-gray-500 font-medium">Fecha Límite</p>
                                                <p class="text-sm font-semibold text-gray-800">
                                                    {{ \Carbon\Carbon::parse($actividad->fecha_entrega)->format('d/m/Y') }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Estado completada --}}
                                <div class="flex-shrink-0">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-600">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        Finalizada
                                    </span>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-12">
                            <svg class="w-24 h-24 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                                </path>
                            </svg>
                            <p class="text-gray-500 text-lg font-medium">No hubo actividades</p>
                            <p class="text-gray-400 text-sm mt-1">Esta reunión no tuvo tareas asignadas</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Sidebar: Información de Historial --}}
        <div class="lg:col-span-1">
            <div class="sticky top-24 space-y-6">
                
                {{-- Card de Estado --}}
                <div class="bg-gradient-to-br from-gray-50 to-gray-100 rounded-2xl border-2 border-gray-300 p-6">
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 bg-gray-600 rounded-xl flex items-center justify-center flex-shrink-0">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z">
                                </path>
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-bold text-gray-800 mb-2">Estado de la Reunión</h4>
                            @if($reunion->deleted_at)
                                <p class="text-sm text-gray-700 mb-2">
                                    Esta reunión fue <span class="font-bold text-red-600">eliminada</span> el {{ \Carbon\Carbon::parse($reunion->deleted_at)->format('d/m/Y') }}
                                </p>
                            @else
                                <p class="text-sm text-gray-700 mb-2">
                                    Esta reunión ya <span class="font-bold text-blue-600">finalizó</span>.
                                </p>
                            @endif
                            <p class="text-xs text-gray-500">Los datos se mantienen para consulta histórica</p>
                        </div>
                    </div>
                </div>

                {{-- Card de Información --}}
                <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-2xl border-2 border-blue-200 p-6">
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 bg-blue-500 rounded-xl flex items-center justify-center flex-shrink-0">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                                </path>
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-bold text-gray-800 mb-2">Información</h4>
                            <ul class="text-sm text-gray-700 space-y-2">
                                <li class="flex items-start gap-2">
                                    <svg class="w-4 h-4 text-blue-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    Datos de solo lectura
                                </li>
                                <li class="flex items-start gap-2">
                                    <svg class="w-4 h-4 text-blue-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    No se puede modificar
                                </li>
                                <li class="flex items-start gap-2">
                                    <svg class="w-4 h-4 text-blue-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    Disponible para consulta
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                {{-- Card de Estadísticas --}}
                <div class="bg-white rounded-2xl shadow-xl border border-gray-200 p-6">
                    <h4 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                            </path>
                        </svg>
                        Resumen
                    </h4>
                    
                    <div class="space-y-4">
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <span class="text-sm text-gray-600">Participantes</span>
                            <span class="text-lg font-bold text-gray-800">{{ $invitados->count() }}</span>
                        </div>
                        
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <span class="text-sm text-gray-600">Actividades</span>
                            <span class="text-lg font-bold text-gray-800">{{ $reunion->actividades->whereNull('deleted_at')->count() }}</span>
                        </div>
                        
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <span class="text-sm text-gray-600">Duración estimada</span>
                            <span class="text-lg font-bold text-gray-800">1h</span>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>
</div>
@endsection