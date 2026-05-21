@extends('layouts.app')
@section('titulo') Nueva contraseña @endsection

@section('content')
<div class="min-h-[calc(100vh-16rem)] flex items-center justify-center px-4 py-12">
    <div class="max-w-md w-full">
        <div class="bg-white rounded-2xl shadow-xl border border-gray-200 overflow-hidden">

            <div class="bg-gradient-to-r from-indigo-500 to-purple-600 p-8 text-white text-center">
                <div class="w-16 h-16 bg-white/20 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                </div>
                <h1 class="text-2xl font-bold">Crear nueva contraseña</h1>
                <p class="text-indigo-100 mt-2 text-sm">Ingresa el código recibido y tu nueva contraseña</p>
            </div>

            <div class="p-8">

                @if ($errors->any())
                    <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl text-red-700 text-sm">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form method="POST" action="{{ route('password.update') }}" class="space-y-5">
                    @csrf

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Correo electrónico</label>
                        <input type="email" name="email" value="{{ $email ?? old('email') }}" required
                               placeholder="tucorreo@ejemplo.com"
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Código de verificación</label>
                        <input type="text" name="code" maxlength="6" required autofocus
                               placeholder="000000"
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all text-center text-3xl font-bold tracking-widest @error('code') border-red-400 @enderror">
                        @error('code')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Nueva contraseña</label>
                        <input type="password" name="password" required
                               placeholder="Mínimo 8 caracteres"
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Confirmar contraseña</label>
                        <input type="password" name="password_confirmation" required
                               placeholder="Repite tu contraseña"
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all">
                    </div>

                    <button type="submit"
                            class="w-full py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-bold rounded-xl hover:from-indigo-700 hover:to-purple-700 transition-all shadow-lg">
                        Guardar nueva contraseña
                    </button>
                </form>

                <div class="mt-6 text-center">
                    <a href="{{ route('password.request') }}" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">
                        ← Solicitar nuevo código
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection