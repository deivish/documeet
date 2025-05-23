@extends('layouts.app')

@section('titulo')
    Detalles de la Reunión
@endsection

@section('content')
<div class="max-w-3xl mx-auto bg-white p-6 rounded shadow-md">
    <h2 class="text-2xl font-bold text-indigo-700 mb-2">{{ $reunion->titulo }}</h2>
    <p class="text-gray-700 mb-4">{{ $reunion->descripcion }}</p>
    <p class="text-gray-500 text-sm mb-2">Fecha: {{ $reunion->fecha_hora  }}</p>

    @if ($reunion->user_id === Auth::id())
    <div class="mt-4">
        <a href="{{ route('reuniones.invitados', $reunion) }}" class="inline-block bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
            Ver/Agregar Invitados
        </a>
    </div>
@endif
    
</div>
@endsection
