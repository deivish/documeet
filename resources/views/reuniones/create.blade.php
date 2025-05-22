@extends('layouts.app')

@section('titulo')
    {{ isset($reunion) ? 'Editar Reunión' : 'Crear Reunión' }}
@endsection

@section('content')
    <div class="max-w-3xl mx-auto bg-white p-6 rounded shadow-md">
        <form action="{{ isset($reunion) ? route('reuniones.update', $reunion->id) : route('reuniones.store') }}" method="POST">
            @csrf
            @if(isset($reunion))
                @method('PUT')
            @endif

            <div class="mb-4">
                <label for="titulo" class="block text-gray-700 font-bold mb-2">Título</label>
                <input type="text" name="titulo" id="titulo"
                       value="{{ old('titulo', $reunion->titulo ?? '') }}"
                       class="w-full border border-gray-300 p-2 rounded" required>
            </div>

            <div class="mb-4">
                <label for="descripcion" class="block text-gray-700 font-bold mb-2">Descripción</label>
                <textarea name="descripcion" id="descripcion" rows="4"
                          class="w-full border border-gray-300 p-2 rounded"
                          required>{{ old('descripcion', $reunion->descripcion ?? '') }}</textarea>
            </div>

            <div class="mb-4">
                <label for="fecha_hora" class="block text-gray-700 font-bold mb-2">Fecha</label>
                <input type="datetime-local" name="fecha_hora" id="fecha_hora"
                       value="{{ old('fecha_hora', isset($reunion) ? \Carbon\Carbon::parse($reunion->fecha_hora)->format('Y-m-d\TH:i') : '') }}"
                       class="w-full border border-gray-300 p-2 rounded" required>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                    {{ isset($reunion) ? 'Actualizar' : 'Crear' }} Reunión
                </button>
            </div>
        </form>
    </div>
@endsection
