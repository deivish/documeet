@extends('layouts.app')

@section('titulo')
    Detalles de la Reunión (Historial)
@endsection

@section('content')
<div class="max-w-4xl mx-auto mt-8 px-4">
    <!-- Botón de regreso -->
    <div class="mb-6">
        <a href="{{ route('reuniones.historial') }}" class="inline-flex items-center text-indigo-600 hover:text-indigo-800 font-medium transition">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            Volver al Historial
        </a>
    </div>

    <!-- Detalles de la reunión -->
    <div class="bg-white shadow-md rounded-lg p-6 space-y-4 mb-6">
        <h2 class="text-3xl font-bold text-indigo-700">{{ $reunion->titulo }}</h2>
        <p class="text-gray-600">{{ $reunion->descripcion }}</p>
        <div class="text-sm text-gray-500">
            <span class="font-semibold">📅 Fecha:</span> {{ \Carbon\Carbon::parse($reunion->fecha_hora)->format('d/m/Y H:i') }}
        </div>
        <div class="text-sm text-gray-500">
            <span class="font-semibold">👤 Organizador:</span> {{ $reunion->organizador->name ?? 'Desconocido' }}
        </div>
    </div>

    <!-- Participantes -->
    <div class="bg-white shadow rounded-lg p-6 mb-6">
        <h3 class="text-xl font-semibold text-gray-800 mb-4">Participantes</h3>
        <ul class="list-disc list-inside text-gray-700">
            @foreach ($invitados as $invitado)
                <li>{{ $invitado->name }} ({{ $invitado->email }})</li>
            @endforeach
        </ul>
    </div>

    <!-- Actividades -->
    <div class="bg-gray-50 p-6 rounded-xl shadow space-y-4">
        <h3 class="text-2xl font-semibold text-gray-800 mb-4 flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-indigo-600 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m2 0a2 2 0 100-4H7a2 2 0 100 4h10zm2 4a2 2 0 100-4H7a2 2 0 100 4h10z" />
            </svg>
            Actividades de la Reunión
        </h3>

        @forelse ($reunion->actividades->whereNull('deleted_at') as $actividad)
            <div class="bg-white p-4 rounded-lg shadow-md border-l-4 border-indigo-500">
                <h4 class="text-lg font-bold text-indigo-700">{{ $actividad->nombre }}</h4>
                <p class="text-sm text-gray-500 mt-2">
                    <span class="font-medium">Responsable:</span> {{ $actividad->responsable ?? 'Sin asignar' }}
                    <span class="ml-4 font-medium">Fecha límite:</span> {{ \Carbon\Carbon::parse($actividad->fecha_entrega)->format('d/m/Y') }}
                </p>
                <p class="text-gray-600 mt-1">{{ $actividad->descripcion }}</p>
            </div>
        @empty
            <p class="text-gray-500 italic">No hay actividades asociadas a esta reunión.</p>
        @endforelse
    </div>
</div>
@endsection
