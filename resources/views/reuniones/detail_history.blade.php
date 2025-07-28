@extends('layouts.app')

@section('titulo')
    Detalles de la Reunión (Historial)
@endsection

@section('content')
<div class="max-w-3xl mx-auto bg-white p-6 rounded shadow-md">
    <h2 class="text-2xl font-bold text-indigo-700 mb-2">{{ $reunion->titulo }}</h2>
    <p class="text-gray-700 mb-4">{{ $reunion->descripcion }}</p>
    <p class="text-gray-500 text-sm mb-2">Fecha: {{ $reunion->fecha_hora }}</p>
    <p class="text-gray-500 text-sm mb-2">Organizador: {{ $reunion->organizador->name ?? 'Desconocido' }}</p>

    <h3 class="text-lg font-semibold mt-4 mb-2">Participantes:</h3>
    <ul class="list-disc list-inside text-gray-700">
        @foreach ($invitados as $invitado)
            <li>{{ $invitado->name }} ({{ $invitado->email }})</li>
        @endforeach
    </ul>
</div>
@endsection
