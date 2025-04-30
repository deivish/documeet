@extends('layouts.app')


@section('titulo')
<h1 class="text-2xl font-bold text-center text-yellow-600 mb-2">Inicia Sesión en DocuMeet</h1>
@endsection

@section('content')
<div class="max-w-md mx-auto bg-white p-8 rounded-xl shadow-md mt-10">
    <h2 class="text-2xl font-bold text-center text-indigo-600 mb-6">Iniciar sesión</h2>

    @if (session('status'))
        <div class="mb-4 text-sm text-green-600">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('login')}}" class="space-y-4">
        @csrf

        @if(session('mensaje'))            
            <p class="mt-1 text-sm text-red-600 italic">{{ session('mensaje') }}</p>
        @endif

        {{-- Correo electrónico --}}
        <div>
            <label for="email" class="block text-sm font-medium text-gray-700">Correo electrónico</label>
            <input type="email" name="email" id="email" required autofocus
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500" />
            @error('email')
            <p class="mt-1 text-sm text-red-600 italic">{{ $message }}</p>
            @enderror
        </div>

        {{-- Contraseña --}}
        <div>
            <label for="password" class="block text-sm font-medium text-gray-700">Contraseña</label>
            <input type="password" name="password" id="password" required
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500" />
            @error('password')
            <p class="mt-1 text-sm text-red-600 italic">{{ $message }}</p>
            @enderror
        </div>

        {{-- Recordarme --}}
        <div class="flex items-center justify-between">
            <label class="flex items-center">
                <input type="checkbox" name="remember" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                <span class="ml-2 text-sm text-gray-600">Recuérdame</span>
            </label>
            <a class="text-sm text-indigo-600 hover:underline">¿Olvidaste tu contraseña?</a>
        </div>

        {{-- Botón de login --}}
        <div>
            <button type="submit"
                    class="w-full bg-indigo-600 text-white py-2 px-4 rounded-md hover:bg-indigo-700 transition">
                Iniciar sesión
            </button>
        </div>
    </form>
</div>
@endsection