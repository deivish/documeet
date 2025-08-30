@extends('layouts.app')

@section('titulo')
    Tus Reuniones
@endsection

@section('content')
    <div class="max-w-4xl mx-auto py-6">
        <div class="mb-6 flex justify-between items-center">
        <h1 class="text-2xl font-bold text-gray-800">Reuniones organizadas por ti</h1>
        <a href="{{ route('post.index') }}" class="inline-flex items-center px-4 py-2 bg-indigo-500 text-white rounded-xl hover:bg-indigo-700 transition">
            ← Volver a Tucuenta
        </a>
    </div>
        @if($reunionesOrganizadas->isEmpty())
            <p class="text-gray-600 mb-6">No has creado ninguna reunión todavía.</p>
        @else
            <div class="space-y-4 mb-6">
                @foreach ($reunionesOrganizadas as $reunion)
    <div class="bg-white p-4 shadow rounded">
        <h3 class="font-semibold text-lg">{{ $reunion->titulo }}</h3>
        <p class="text-sm text-gray-600">Fecha: {{ $reunion->fecha_hora }}</p>
        <div class="flex space-x-4 mt-2">
            <a href="{{ route('reuniones.show', $reunion) }}" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 transition">Ver detalles</a>
            <a href="{{ route('reuniones.edit', $reunion) }}" class="bg-yellow-500 text-white px-4 py-2 rounded hover:bg-yellow-600 transition">Editar</a>
            <form action="{{ route('reuniones.destroy', $reunion) }}" method="POST" onsubmit="return confirm('¿Estás seguro de eliminar esta reunión?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600 transition">Eliminar</button>
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
                        <a href="{{ route('reuniones.show', $reunion) }}" class="inline-block mt-2 bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 transition">Ver detalles</a>
                    </div>
                @endforeach
            </div>
        @endif

    </div>
@endsection
