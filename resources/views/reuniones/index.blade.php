@extends('layouts.app')

@section('titulo')
    Tus Reuniones
@endsection

@section('content')
    <div class="max-w-4xl mx-auto py-6">

        <h2 class="text-2xl font-bold mb-4">Reuniones organizadas por ti</h2>
        @if($reunionesOrganizadas->isEmpty())
            <p class="text-gray-600 mb-6">No has creado ninguna reunión todavía.</p>
        @else
            <div class="space-y-4 mb-6">
                @foreach ($reunionesOrganizadas as $reunion)
    <div class="bg-white p-4 shadow rounded">
        <h3 class="font-semibold text-lg">{{ $reunion->titulo }}</h3>
        <p class="text-sm text-gray-600">Fecha: {{ $reunion->fecha_hora }}</p>
        <div class="flex space-x-4 mt-2">
            <a href="{{ route('reuniones.show', $reunion) }}" class="text-blue-500 hover:underline">Ver detalles</a>
            <a href="{{ route('reuniones.edit', $reunion) }}" class="text-yellow-500 hover:underline">Editar</a>
            <form action="{{ route('reuniones.destroy', $reunion) }}" method="POST" onsubmit="return confirm('¿Estás seguro de eliminar esta reunión?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="text-red-500 hover:underline">Eliminar</button>
            </form>
        </div>
    </div>
@endforeach

            </div>
        @endif

        <h2 class="text-2xl font-bold mb-4 mt-10">Reuniones donde eres invitado</h2>
        @if($reunionesInvitado->isEmpty())
            <p class="text-gray-600">No has sido invitado a ninguna reunión.</p>
        @else
            <div class="space-y-4">
                @foreach ($reunionesInvitado as $reunion)
                    <div class="bg-white p-4 shadow rounded">
                        <h3 class="font-semibold text-lg">{{ $reunion->titulo }}</h3>
                        <p class="text-sm text-gray-600">Fecha: {{ $reunion->fecha_hora }}</p>
                        <a href="{{ route('reuniones.show', $reunion) }}" class="text-blue-500 hover:underline">Ver detalles</a>
                    </div>
                @endforeach
            </div>
        @endif

    </div>
@endsection
