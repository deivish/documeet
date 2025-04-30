@extends('layouts.app')

@section('titulo')
    Tucuenta
@endsection

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Tarjeta: Reuniones creadas -->
            <div class="bg-white shadow-md rounded-lg p-4">
                <h2 class="text-lg font-semibold">Tus reuniones</h2>
                <p class="text-sm text-gray-600 mt-2">Puedes ver o gestionar tus reuniones programadas.</p>
                <a  class="mt-4 inline-block bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Ver reuniones</a>
            </div>

            <!-- Tarjeta: Reuniones donde eres invitado -->
            <div class="bg-white shadow-md rounded-lg p-4">
                <h2 class="text-lg font-semibold">Invitaciones</h2>
                <p class="text-sm text-gray-600 mt-2">Reuniones donde has sido invitado como participante.</p>
                <a  class="mt-4 inline-block bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">Ver invitaciones</a>
            </div>

            <!-- Tarjeta: Crear nueva reunión -->
            <div class="bg-white shadow-md rounded-lg p-4">
                <h2 class="text-lg font-semibold">Agendar nueva reunión</h2>
                <p class="text-sm text-gray-600 mt-2">Puedes programar una nueva reunión con tu equipo.</p>
                <a  class="mt-4 inline-block bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">Agendar</a>
            </div>
        </div>

        {{-- <div class="mt-10">
            <h2 class="text-xl font-bold mb-4">Próximas reuniones</h2>
            <!-- Aquí puedes listar reuniones próximas -->
            @foreach ($proximasReuniones as $reunion)
                <div class="bg-gray-100 rounded-md p-4 mb-2">
                    <h3 class="text-md font-semibold">{{ $reunion->titulo }}</h3>
                    <p class="text-sm text-gray-700">Fecha: {{ $reunion->fecha }}</p>
                    <a  class="text-blue-500 hover:underline text-sm">Ver detalles</a>
                </div>
            @endforeach
        </div> --}}
    </div>
@endsection