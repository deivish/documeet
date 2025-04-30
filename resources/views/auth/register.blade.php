@extends('layouts.app')

@section('titulo')
<h2 class="text-2xl font-bold text-center text-yellow-600 mb-2">Registrate en DocuMeet</h2>
@endsection


@section('content')
<div class="max-w-2xl mx-auto bg-white p-8 rounded-xl shadow-md">
    <h2 class="text-2xl font-bold text-center text-indigo-600 mb-6">Registro de Usuario</h2>

    <form action="{{ route('register')}}" method="POST"  class="space-y-4">
        @csrf

        {{-- Tipo de documento --}}
        <div>
            <label for="tipo_documento" class="block text-sm font-medium text-gray-700">Tipo de documento</label>
            <select id="tipo_documento" name="tipo_documento" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                <option value="">Seleccione...</option>
                <option value="CC">Cédula de ciudadanía</option>
                <option value="CE">Cédula de extranjería</option>
                <option value="TI">Tarjeta de identidad</option>
                <option value="NIT">NIT</option>
            </select>
            @error('tipo_documento')
            <p class="mt-1 text-sm text-red-600 italic">{{ $message }}</p>
            @enderror
        </div>

        {{-- Número de documento --}}
        <div>
            <label for="numero_documento" class="block text-sm font-medium text-gray-700">Número de documento</label>
            <input type="text" id="numero_documento" name="numero_documento" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
            @error('numero_documento')
            <p class="mt-1 text-sm text-red-600 italic">{{ $message }}</p>
            @enderror
        </div>

        {{-- Cargo --}}
        <div>
            <label for="cargo" class="block text-sm font-medium text-gray-700">Cargo</label>
            <input type="text" id="cargo" name="cargo" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
            @error('cargo')
            <p class="mt-1 text-sm text-red-600 italic">{{ $message }}</p>
            @enderror
        </div>

        {{-- Organización --}}
        <div>
            <label for="organizacion" class="block text-sm font-medium text-gray-700">Organización</label>
            <input type="text" id="organizacion" name="organizacion" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
            @error('organizacion')
            <p class="mt-1 text-sm text-red-600 italic">{{ $message }}</p>
            @enderror
        </div>

        {{-- Nombre --}}
        <div>
            <label for="name" class="block text-sm font-medium text-gray-700">Nombre</label>
            <input type="text" id="name" name="name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
            @error('name')
                <p class="mt-1 text-sm text-red-600 italic">{{ $message }}</p>
            @enderror
        </div>

        {{-- Apellidos --}}
        <div>
            <label for="apellidos" class="block text-sm font-medium text-gray-700">Apellidos</label>
            <input type="text" id="apellidos" name="apellidos" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
            @error('apellidos')
                <p class="mt-1 text-sm text-red-600 italic">{{ $message }}</p>
            @enderror
        </div>

        {{-- Correo electrónico --}}
        <div>
            <label for="email" class="block text-sm font-medium text-gray-700">Correo electrónico</label>
            <input type="email" id="email" name="email" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
            @error('email')
            <p class="mt-1 text-sm text-red-600 italic">{{ $message }}</p>
            @enderror
        </div>

        {{-- Número de celular --}}
        <div>
            <label for="celular" class="block text-sm font-medium text-gray-700">Número de celular</label>
            <input type="text" id="celular" name="celular" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
            @error('celular')
            <p class="mt-1 text-sm text-red-600 italic">{{ $message }}</p>
            @enderror
        </div>

        {{-- Contraseña --}}
        <div>
            <label for="password" class="block text-sm font-medium text-gray-700">Contraseña</label>
            <input type="password" id="password" name="password" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
            @error('password')
            <p class="mt-1 text-sm text-red-600 italic">{{ $message }}</p>
            @enderror
        </div>

        {{-- Confirmar contraseña --}}
        <div>
            <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirmar contraseña</label>
            <input type="password" id="password_confirmation" name="password_confirmation" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
        </div>

        {{-- Botón de registro --}}
        <div>
            <button type="submit" class="w-full bg-indigo-600 text-white py-2 px-4 rounded-md hover:bg-indigo-700 transition">
                Registrarse
            </button>
        </div>
    </form>
</div>

@endsection