@extends('layouts.app')
@section('titulo') Recuperar contraseña @endsection

@section('content')
<div class="min-h-[calc(100vh-16rem)] flex items-center justify-center px-4 py-12">
    <div class="max-w-md w-full">
        <div class="bg-white rounded-2xl shadow-xl border border-gray-200 overflow-hidden">

            <div class="bg-gradient-to-r from-indigo-500 to-purple-600 p-8 text-white text-center">
                <div class="w-16 h-16 bg-white/20 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                    </svg>
                </div>
                <h1 class="text-2xl font-bold">¿Olvidaste tu contraseña?</h1>
                <p class="text-indigo-100 mt-2 text-sm">Ingresa tu correo y te daremos un código de verificación</p>
            </div>

            <div class="p-8">

                {{-- Código generado (solo desarrollo) --}}
                @if (session('code'))
                    <div class="mb-6 p-5 bg-indigo-50 border-2 border-indigo-300 rounded-xl text-center">
                        <p class="text-sm text-indigo-600 font-medium mb-2">Tu código de verificación es:</p>
                        <p class="text-5xl font-bold tracking-widest text-indigo-700 mb-3">{{ session('code') }}</p>
                        <p class="text-xs text-gray-500 mb-4">Expira en 15 minutos</p>
                        <a href="{{ route('password.reset') }}?email={{ urlencode(session('email')) }}"
                           class="inline-flex items-center gap-2 px-6 py-3 bg-indigo-600 text-white font-bold rounded-xl hover:bg-indigo-700 transition-all">
                            Continuar → Cambiar contraseña
                        </a>
                    </div>
                @endif

                @if (session('success') && !session('code'))
                    <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-xl text-green-700 text-sm font-medium">
                        ✅ {{ session('success') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl text-red-700 text-sm">
                        {{ $errors->first() }}
                    </div>
                @endif

                @if (!session('code'))
                <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
                    @csrf
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Correo electrónico</label>
                        <input type="email" name="email" value="{{ old('email') }}" required autofocus
                               placeholder="tucorreo@ejemplo.com"
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all">
                    </div>
                    <button type="submit"
                            class="w-full py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-bold rounded-xl hover:from-indigo-700 hover:to-purple-700 transition-all shadow-lg">
                        Generar código de verificación
                    </button>
                </form>
                @endif

                <div class="mt-6 text-center">
                    <a href="{{ route('login') }}" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">
                        ← Volver al inicio de sesión
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection