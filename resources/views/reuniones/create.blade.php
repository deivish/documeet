@extends('layouts.app')

@section('titulo')
    Crear Reunión
@endsection

@section('content')
    <div class="max-w-3xl mx-auto bg-white p-6 rounded shadow-md">
        <form action="{{ route('reuniones.store') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label for="titulo" class="block text-gray-700 font-bold mb-2">Título</label>
                <input type="text" name="titulo" id="titulo" class="w-full border border-gray-300 p-2 rounded" required>
            </div>

            <div class="mb-4">
                <label for="descripcion" class="block text-gray-700 font-bold mb-2">Descripción</label>
                <textarea name="descripcion" id="descripcion" rows="4" class="w-full border border-gray-300 p-2 rounded"></textarea>
            </div>

            <div class="mb-4">
                <label for="fecha" class="block text-gray-700 font-bold mb-2">Fecha</label>
                <input type="datetime-local" name="fecha_hora" id="fecha_hora" class="w-full border border-gray-300 p-2 rounded" required>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                    Crear Reunión
                </button>
            </div>
        </form>
    </div>
@endsection
