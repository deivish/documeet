@extends('layouts.app')

@section('titulo')
    Tucuenta
@endsection

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 justify-center">
            <!-- Tarjeta: Reuniones creadas -->
            <div class="bg-white shadow-md rounded-lg p-4">
                <h2 class="text-lg font-semibold">Tus reuniones</h2>
                <p class="text-sm text-gray-600 mt-2">Puedes ver o gestionar tus reuniones programadas.</p>
                <a href="{{ route('reuniones.index') }}" class="mt-4 inline-block bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Ver reuniones</a>
            </div>

            <!-- Tarjeta: Crear nueva reunión -->
            <div class="bg-white shadow-md rounded-lg p-4">
                <h2 class="text-lg font-semibold">Agendar nueva reunión</h2>
                <p class="text-sm text-gray-600 mt-2">Puedes programar una nueva reunión con tu equipo.</p>
                <a href="{{ route('reuniones.create') }}" class="mt-4 inline-block bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">Agendar</a>
            </div>

            <!-- Tarjeta: Historial de reuniones -->
            <div class="bg-white shadow-md rounded-lg p-4">
                <h2 class="text-lg font-semibold">Historial de reuniones</h2>
                <p class="text-sm text-gray-600 mt-2">Consulta tus reuniones eliminadas o pasadas.</p>
                <a href="{{ route('reuniones.historial') }}" class="mt-4 inline-block bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700">Ver historial</a>
            </div>

        </div>

        
    </div>
@endsection