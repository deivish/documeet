@extends('layouts.app')

@section('titulo')
    Invitados a la Reunión
@endsection

@section('content')
<!-- Botón de regreso -->
    <div class="flex justify-end">
        <a href="{{ route('reuniones.invitaciones') }}" class="inline-flex items-center text-indigo-600 hover:text-indigo-800 font-medium transition">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            Volver a Tus Reuniones
        </a>
    </div>
<div class="max-w-2xl mx-auto bg-white p-6 rounded shadow-md">
    <h2 class="text-xl font-semibold text-indigo-700 mb-4">Agregar Invitado</h2>

        {{-- mensaje si de error si no existe el usuario --}}
    @if ($errors->any())
        <div class="mb-4 p-3 bg-red-100 text-red-700 rounded">
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    {{-- mensaje de exito si existe el usuario --}}
    @if (session('success'))
        <div class="mb-4 p-3 bg-green-100 text-green-700 rounded">
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('reuniones.agregarInvitado', $reunion) }}" method="POST" class="mb-6">
        @csrf
        <div class="flex gap-4">
            <input type="email" name="email" placeholder="Correo del invitado" class="w-full border border-gray-300 p-2 rounded" required>
            <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
                Agregar
            </button>
        </div>
    </form>

    <h3 class="text-lg font-bold text-gray-700 mb-2">Lista de Invitados</h3>
    <ul class="list-disc list-inside">
        @forelse ($reunion->invitados as $invitado)
            <li class="text-gray-600">{{ $invitado->email }}</li>
        @empty
            <li class="text-gray-500">Aún no hay invitados.</li>
        @endforelse
    </ul>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const emailInput = document.querySelector('input[name="email"]');
            const errorBox = document.querySelector('.bg-red-100');
    
            if (emailInput && errorBox) {
                emailInput.addEventListener('input', () => {
                    errorBox.remove(); // Elimina el mensaje de error cuando el usuario empieza a escribir
                });
            }
        });
    </script>
</div>
@endsection
