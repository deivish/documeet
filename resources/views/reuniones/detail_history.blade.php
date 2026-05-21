@extends('layouts.app')

@section('titulo')
    Historial — {{ $reunion->titulo }}
@endsection

@section('content')
<div class="max-w-6xl mx-auto px-4 py-8">

    {{-- Breadcrumb --}}
    <div class="mb-8">
        <a href="{{ route('reuniones.historial') }}"
           class="inline-flex items-center text-gray-600 hover:text-indigo-600 font-medium transition-colors group mb-6">
            <svg class="w-5 h-5 mr-2 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Volver al Historial
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        {{-- ══════ COLUMNA PRINCIPAL ══════ --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Card principal --}}
            <div class="bg-white rounded-2xl shadow-xl border border-gray-200 overflow-hidden">
                <div class="bg-gradient-to-r from-gray-500 to-gray-600 p-8 text-white relative overflow-hidden">
                    <div class="absolute inset-0 opacity-10">
                        <svg class="w-full h-full" xmlns="http://www.w3.org/2000/svg">
                            <pattern id="pattern" x="0" y="0" width="40" height="40" patternUnits="userSpaceOnUse">
                                <circle cx="20" cy="20" r="2" fill="white"/>
                            </pattern>
                            <rect width="100%" height="100%" fill="url(#pattern)"/>
                        </svg>
                    </div>
                    <div class="relative z-10">
                        <div class="flex items-start justify-between mb-4 flex-wrap gap-2">
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="bg-white/20 backdrop-blur text-white text-xs font-semibold px-3 py-1 rounded-full">
                                    Historial
                                </span>
                                @if($reunion->deleted_at)
                                    <span class="bg-red-500/30 backdrop-blur text-white text-xs font-semibold px-3 py-1 rounded-full">Eliminada</span>
                                @else
                                    <span class="bg-blue-500/30 backdrop-blur text-white text-xs font-semibold px-3 py-1 rounded-full">Finalizada</span>
                                @endif
                            </div>
                        </div>
                        <h1 class="text-3xl md:text-4xl font-bold mb-3">{{ $reunion->titulo }}</h1>
                        <p class="text-gray-200 text-lg leading-relaxed">{{ $reunion->descripcion }}</p>
                    </div>
                </div>

                <div class="p-8">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="flex items-start gap-4">
                            <div class="w-12 h-12 bg-gray-100 rounded-xl flex items-center justify-center flex-shrink-0">
                                <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-gray-500 uppercase tracking-wide">Fecha y Hora</p>
                                <p class="text-lg font-bold text-gray-800 mt-1">{{ \Carbon\Carbon::parse($reunion->fecha_hora)->format('d/m/Y') }}</p>
                                <p class="text-md text-gray-600">{{ \Carbon\Carbon::parse($reunion->fecha_hora)->format('H:i') }}</p>
                                <p class="text-xs text-gray-400 mt-1">{{ \Carbon\Carbon::parse($reunion->fecha_hora)->diffForHumans() }}</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-4">
                            <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center flex-shrink-0">
                                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-gray-500 uppercase tracking-wide">Organizador</p>
                                <p class="text-lg font-bold text-gray-800 mt-1">{{ $reunion->user->name ?? 'Desconocido' }}</p>
                                <p class="text-sm text-gray-600">{{ $reunion->user->email ?? '' }}</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-4">
                            <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center flex-shrink-0">
                                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-gray-500 uppercase tracking-wide">Participantes</p>
                                <p class="text-lg font-bold text-gray-800 mt-1">{{ $invitados->count() }} personas</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-4">
                            <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center flex-shrink-0">
                                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-gray-500 uppercase tracking-wide">Actividades</p>
                                <p class="text-lg font-bold text-gray-800 mt-1">{{ $reunion->actividades->count() }} tareas</p>
                                <p class="text-sm text-gray-600">{{ $reunion->compromisos->count() }} compromisos</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Participantes --}}
            <div class="bg-white rounded-2xl shadow-xl border border-gray-200 overflow-hidden">
                <div class="bg-gradient-to-r from-blue-50 to-indigo-50 px-8 py-5 border-b border-gray-200">
                    <h2 class="text-xl font-bold text-gray-800 flex items-center gap-3">
                        <div class="w-9 h-9 bg-blue-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </div>
                        Participantes
                    </h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        @forelse($invitados as $invitado)
                            <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl">
                                <div class="w-10 h-10 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-full flex items-center justify-center flex-shrink-0">
                                    <span class="text-white text-sm font-bold">{{ strtoupper(substr($invitado->name, 0, 2)) }}</span>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="font-semibold text-gray-800 text-sm truncate">{{ $invitado->name }}</p>
                                    <p class="text-xs text-gray-500 truncate">{{ $invitado->email }}</p>
                                </div>
                            </div>
                        @empty
                            <div class="col-span-2 text-center py-6 text-gray-400 text-sm">No hubo participantes registrados</div>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- ══════ ACTIVIDADES ══════ --}}
            <div class="bg-white rounded-2xl shadow-xl border border-gray-200 overflow-hidden">
                <div class="bg-gradient-to-r from-green-50 to-emerald-50 px-8 py-5 border-b border-gray-200 flex items-center justify-between">
                    <h2 class="text-xl font-bold text-gray-800 flex items-center gap-3">
                        <div class="w-9 h-9 bg-green-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                        </div>
                        Actividades
                    </h2>
                    @if($reunion->actividades->count() > 0)
                    <div class="flex gap-2 text-xs">
                        @php
                            $actComp = $reunion->actividades->where('estado','completada')->count();
                            $actPend = $reunion->actividades->where('estado','pendiente')->count();
                            $actCurs = $reunion->actividades->where('estado','en_curso')->count();
                        @endphp
                        @if($actComp > 0)<span class="px-2 py-1 bg-green-100 text-green-700 rounded-full font-medium">✓ {{ $actComp }} completadas</span>@endif
                        @if($actCurs > 0)<span class="px-2 py-1 bg-blue-100 text-blue-700 rounded-full font-medium">⟳ {{ $actCurs }} en curso</span>@endif
                        @if($actPend > 0)<span class="px-2 py-1 bg-yellow-100 text-yellow-700 rounded-full font-medium">○ {{ $actPend }} pendientes</span>@endif
                    </div>
                    @endif
                </div>
                <div class="p-6 space-y-3">
                    @forelse($reunion->actividades as $actividad)
                        @php
                            $est = $actividad->estado ?? 'pendiente';
                            $colorBorde = match($est) {
                                'completada' => 'border-green-400',
                                'en_curso'   => 'border-blue-400',
                                default      => 'border-yellow-400'
                            };
                        @endphp
                        <div class="bg-gray-50 border-l-4 {{ $colorBorde }} rounded-xl p-5">
                            <div class="flex items-start justify-between gap-3">
                                <div class="flex-1">
                                    <h3 class="font-bold text-gray-800 mb-1">{{ $actividad->nombre }}</h3>
                                    @if($actividad->descripcion)
                                        <p class="text-gray-600 text-sm mb-3">{{ $actividad->descripcion }}</p>
                                    @endif
                                    <div class="flex flex-wrap gap-4 text-xs text-gray-500">
                                        <span class="flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                            </svg>
                                            {{ $actividad->responsable ?? 'Sin asignar' }}
                                        </span>
                                        <span class="flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                            Límite: {{ \Carbon\Carbon::parse($actividad->fecha_entrega)->format('d/m/Y') }}
                                        </span>
                                    </div>
                                </div>
                                <span class="flex-shrink-0 px-3 py-1 rounded-full text-xs font-semibold
                                    {{ $est === 'completada' ? 'bg-green-100 text-green-700' : ($est === 'en_curso' ? 'bg-blue-100 text-blue-700' : 'bg-yellow-100 text-yellow-700') }}">
                                    {{ $est === 'completada' ? '✓ Completada' : ($est === 'en_curso' ? '⟳ En curso' : '○ Pendiente') }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8 text-gray-400 text-sm">Esta reunión no tuvo actividades asignadas</div>
                    @endforelse
                </div>
            </div>

            {{-- ══════ COMPROMISOS ══════ --}}
            <div class="bg-white rounded-2xl shadow-xl border border-gray-200 overflow-hidden">
                <div class="bg-gradient-to-r from-purple-50 to-indigo-50 px-8 py-5 border-b border-gray-200 flex items-center justify-between">
                    <h2 class="text-xl font-bold text-gray-800 flex items-center gap-3">
                        <div class="w-9 h-9 bg-purple-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                            </svg>
                        </div>
                        Compromisos
                    </h2>
                    @if($reunion->compromisos->count() > 0)
                    <div class="flex gap-2 text-xs">
                        @php
                            $compCump = $reunion->compromisos->where('estado','cumplido')->count();
                            $compVenc = $reunion->compromisos->where('estado','vencido')->count();
                            $compPend = $reunion->compromisos->where('estado','pendiente')->count();
                        @endphp
                        @if($compCump > 0)<span class="px-2 py-1 bg-green-100 text-green-700 rounded-full font-medium">✓ {{ $compCump }} cumplidos</span>@endif
                        @if($compVenc > 0)<span class="px-2 py-1 bg-red-100 text-red-700 rounded-full font-medium">✗ {{ $compVenc }} vencidos</span>@endif
                        @if($compPend > 0)<span class="px-2 py-1 bg-yellow-100 text-yellow-700 rounded-full font-medium">○ {{ $compPend }} pendientes</span>@endif
                    </div>
                    @endif
                </div>
                <div class="p-6 space-y-3">
                    @forelse($reunion->compromisos as $compromiso)
                        @php
                            $est = $compromiso->estado ?? 'pendiente';
                            $colorBorde = match($est) {
                                'cumplido' => 'border-green-400',
                                'vencido'  => 'border-red-400',
                                default    => 'border-yellow-400'
                            };
                        @endphp
                        <div class="bg-gray-50 border-l-4 {{ $colorBorde }} rounded-xl p-5">
                            <div class="flex items-start justify-between gap-3">
                                <div class="flex-1">
                                    <h3 class="font-bold text-gray-800 mb-1">{{ $compromiso->descripcion }}</h3>
                                    @if($compromiso->resultado)
                                        <p class="text-gray-500 text-sm mb-3">Resultado esperado: {{ $compromiso->resultado }}</p>
                                    @endif
                                    <div class="flex flex-wrap gap-4 text-xs text-gray-500">
                                        <span class="flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                            </svg>
                                            {{ $compromiso->responsable ?? 'Sin asignar' }}
                                        </span>
                                        <span class="flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                            Fecha: {{ \Carbon\Carbon::parse($compromiso->fecha)->format('d/m/Y') }}
                                        </span>
                                    </div>
                                </div>
                                <span class="flex-shrink-0 px-3 py-1 rounded-full text-xs font-semibold
                                    {{ $est === 'cumplido' ? 'bg-green-100 text-green-700' : ($est === 'vencido' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700') }}">
                                    {{ $est === 'cumplido' ? '✓ Cumplido' : ($est === 'vencido' ? '✗ Vencido' : '○ Pendiente') }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8 text-gray-400 text-sm">Esta reunión no tuvo compromisos registrados</div>
                    @endforelse
                </div>
            </div>

        </div>

        {{-- ══════ SIDEBAR ══════ --}}
        <div class="lg:col-span-1">
            <div class="sticky top-24 space-y-6">

                {{-- Acta --}}
                @if($reunion->acta)
                <div class="bg-white rounded-2xl shadow-xl border border-gray-200 overflow-hidden">
                    <div class="bg-gradient-to-r from-indigo-50 to-purple-50 px-6 py-4 border-b border-gray-200">
                        <h4 class="font-bold text-gray-800 flex items-center gap-2">
                            <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            Acta de la reunión
                        </h4>
                    </div>
                    <div class="p-5 space-y-3">
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-500">Estado</span>
                            <span class="px-2 py-1 rounded-full text-xs font-semibold
                                {{ $reunion->acta->estado === 'final' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                                {{ $reunion->acta->estado === 'final' ? '✓ Final' : '⏳ Borrador' }}
                            </span>
                        </div>
                        @if($reunion->acta->aprobada_at)
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-500">Aprobada</span>
                            <span class="text-gray-700 font-medium">{{ \Carbon\Carbon::parse($reunion->acta->aprobada_at)->format('d/m/Y') }}</span>
                        </div>
                        @endif
                        <div class="pt-2 space-y-2">
                            <a href="{{ route('actas.show', $reunion->acta) }}"
                               class="w-full flex items-center justify-center gap-2 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition-all">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                Ver Acta
                            </a>
                            <div class="flex gap-2">
                                <a href="{{ route('actas.pdf', $reunion->acta) }}" target="_blank"
                                   class="flex-1 flex items-center justify-center gap-1 px-3 py-2 bg-red-50 hover:bg-red-100 text-red-700 text-xs font-medium rounded-lg transition-all">
                                    PDF
                                </a>
                                <a href="{{ route('actas.docx', $reunion->acta) }}"
                                   class="flex-1 flex items-center justify-center gap-1 px-3 py-2 bg-blue-50 hover:bg-blue-100 text-blue-700 text-xs font-medium rounded-lg transition-all">
                                    Word
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                {{-- Estado de la reunión --}}
                <div class="bg-gradient-to-br from-gray-50 to-gray-100 rounded-2xl border-2 border-gray-300 p-6">
                    <div class="flex items-start gap-4">
                        <div class="w-11 h-11 bg-gray-600 rounded-xl flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-bold text-gray-800 mb-2">Estado</h4>
                            @if($reunion->deleted_at)
                                <p class="text-sm text-gray-700">Eliminada el <span class="font-bold text-red-600">{{ \Carbon\Carbon::parse($reunion->deleted_at)->format('d/m/Y') }}</span></p>
                            @else
                                <p class="text-sm text-gray-700">Reunión <span class="font-bold text-blue-600">finalizada</span></p>
                            @endif
                            <p class="text-xs text-gray-400 mt-1">Solo lectura — datos históricos</p>
                        </div>
                    </div>
                </div>

                {{-- Resumen estadísticas --}}
                <div class="bg-white rounded-2xl shadow-xl border border-gray-200 p-6">
                    <h4 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                        Resumen
                    </h4>
                    <div class="space-y-3">
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <span class="text-sm text-gray-600">Participantes</span>
                            <span class="text-lg font-bold text-gray-800">{{ $invitados->count() }}</span>
                        </div>
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <span class="text-sm text-gray-600">Actividades</span>
                            <span class="text-lg font-bold text-gray-800">{{ $reunion->actividades->count() }}</span>
                        </div>
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <span class="text-sm text-gray-600">Compromisos</span>
                            <span class="text-lg font-bold text-gray-800">{{ $reunion->compromisos->count() }}</span>
                        </div>
                        @if($reunion->acta)
                        <div class="flex items-center justify-between p-3 bg-indigo-50 rounded-lg">
                            <span class="text-sm text-indigo-700">Acta</span>
                            <span class="text-xs font-bold {{ $reunion->acta->estado === 'final' ? 'text-green-600' : 'text-yellow-600' }}">
                                {{ $reunion->acta->estado === 'final' ? '✓ Final' : '⏳ Borrador' }}
                            </span>
                        </div>
                        @endif
                    </div>
                </div>

            </div>
        </div>

    </div>
</div>
@endsection