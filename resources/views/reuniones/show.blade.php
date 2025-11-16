@extends('layouts.app')

@section('titulo')
    Detalles de la Reunión
@endsection

@section('content')
<div class="max-w-6xl mx-auto px-4 py-8">
    
    {{-- Header con Breadcrumb --}}
    <div class="mb-8">
        <a href="{{ route('reuniones.index') }}" 
           class="inline-flex items-center text-gray-600 hover:text-indigo-600 font-medium transition-colors group mb-6">
            <svg class="w-5 h-5 mr-2 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            Volver a Mis Reuniones
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        {{-- Columna Principal: Información de la Reunión --}}
        <div class="lg:col-span-2 space-y-6">
            
            {{-- Card Principal de la Reunión --}}
            <div class="bg-white rounded-2xl shadow-xl border border-gray-200 overflow-hidden">
                {{-- Header con gradiente --}}
                <div class="bg-gradient-to-r from-indigo-500 to-purple-600 p-8 text-white">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <div class="flex items-center gap-2 mb-3">
                                <span class="bg-white/20 backdrop-blur text-white text-xs font-semibold px-3 py-1 rounded-full">
                                    Reunión Activa
                                </span>
                                @if ($reunion->user_id === Auth::id())
                                <span class="bg-yellow-400/30 backdrop-blur text-white text-xs font-semibold px-3 py-1 rounded-full">
                                    Organizador
                                </span>
                                @else
                                <span class="bg-green-400/30 backdrop-blur text-white text-xs font-semibold px-3 py-1 rounded-full">
                                    Invitado
                                </span>
                                @endif
                            </div>
                            <h1 class="text-3xl md:text-4xl font-bold mb-3">{{ $reunion->titulo }}</h1>
                            <p class="text-indigo-100 text-lg leading-relaxed">{{ $reunion->descripcion }}</p>
                        </div>
                    </div>
                </div>

                {{-- Información de la Reunión --}}
                <div class="p-8">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Fecha y Hora --}}
                        <div class="flex items-start gap-4">
                            <div class="w-12 h-12 bg-indigo-100 rounded-xl flex items-center justify-center flex-shrink-0">
                                <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                                <p class="text-lg font-bold text-gray-800 mt-1">{{ $reunion->user->name }}</p>
                                <p class="text-sm text-gray-600">{{ $reunion->user->email }}</p>
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
                                <p class="text-sm text-gray-600">Asignadas al equipo</p>
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
                                    {{ $reunion->invitados->count() + 1 }} personas
                                </p>
                                <p class="text-sm text-gray-600">En esta reunión</p>
                            </div>
                        </div>
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
                    <p class="text-gray-600 mt-2">Tareas asignadas y compromisos del equipo</p>
                </div>

                <div class="p-8">
                    @forelse ($reunion->actividades->whereNull('deleted_at') as $actividad)
                        <div class="mb-4 last:mb-0 bg-gradient-to-r from-gray-50 to-white border-l-4 border-indigo-500 rounded-xl p-6 hover:shadow-lg transition-all">
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
                                            <div class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center">
                                                <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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

                                {{-- Estado badge --}}
                                <div class="flex-shrink-0">
                                    @php
                                        $fechaLimite = \Carbon\Carbon::parse($actividad->fecha_entrega);
                                        $hoy = \Carbon\Carbon::now();
                                        $diasRestantes = $hoy->diffInDays($fechaLimite, false);
                                    @endphp
                                    
                                    @if($diasRestantes < 0)
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800">
                                            Vencida
                                        </span>
                                    @elseif($diasRestantes == 0)
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-orange-100 text-orange-800">
                                            Hoy
                                        </span>
                                    @elseif($diasRestantes <= 3)
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800">
                                            {{ $diasRestantes }} días
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                            {{ $diasRestantes }} días
                                        </span>
                                    @endif
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
                            <p class="text-gray-500 text-lg font-medium">No hay actividades asociadas</p>
                            <p class="text-gray-400 text-sm mt-1">Las actividades aparecerán aquí una vez sean asignadas</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Sidebar: Acciones Rápidas --}}
        <div class="lg:col-span-1">
            <div class="sticky top-24 space-y-6">
                
                {{-- Card de Acciones --}}
                <div class="bg-white rounded-2xl shadow-xl border border-gray-200 overflow-hidden">
                    <div class="bg-gradient-to-r from-indigo-50 to-purple-50 px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                            <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                    d="M13 10V3L4 14h7v7l9-11h-7z">
                                </path>
                            </svg>
                            Acciones Rápidas
                        </h3>
                    </div>

                    <div class="p-6 space-y-3">
                        {{-- Unirse a Videollamada --}}
                        <a href="{{ route('reuniones.videollamada', $reunion) }}" 
                           class="w-full inline-flex items-center justify-center gap-3 px-6 py-4 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-xl hover:from-indigo-700 hover:to-purple-700 transition-all shadow-lg hover:shadow-xl font-bold group">
                            <svg class="w-6 h-6 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                    d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z">
                                </path>
                            </svg>
                            Unirme Ahora
                        </a>

                        {{-- Ver/Agregar Invitados (Solo Organizador) --}}
                        @if ($reunion->user_id === Auth::id())
                        <a href="{{ route('reuniones.invitados', $reunion) }}" 
                           class="w-full inline-flex items-center justify-center gap-3 px-6 py-4 bg-green-600 text-white rounded-xl hover:bg-green-700 transition-all shadow-md hover:shadow-lg font-medium">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z">
                                </path>
                            </svg>
                            Gestionar Invitados
                        </a>

                        {{-- Editar Reunión (Solo Organizador) --}}
                        <a href="{{ route('reuniones.edit', $reunion) }}" 
                           class="w-full inline-flex items-center justify-center gap-3 px-6 py-4 bg-gray-600 text-white rounded-xl hover:bg-gray-700 transition-all shadow-md hover:shadow-lg font-medium">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                </path>
                            </svg>
                            Editar Reunión
                        </a>
                        @endif
                    </div>
                </div>

                {{-- Card de Información Adicional --}}
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
                            <h4 class="font-bold text-gray-800 mb-2">Información Importante</h4>
                            <ul class="text-sm text-gray-700 space-y-2">
                                <li class="flex items-start gap-2">
                                    <svg class="w-4 h-4 text-blue-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    Puedes unirte 15 min antes
                                </li>
                                <li class="flex items-start gap-2">
                                    <svg class="w-4 h-4 text-blue-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    La grabación se activa automáticamente
                                </li>
                                <li class="flex items-start gap-2">
                                    <svg class="w-4 h-4 text-blue-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    Se generarán actas automáticamente
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>
</div>
@endsection