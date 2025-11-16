@extends('layouts.app')

@section('titulo')
    Bienvenido
@endsection

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    
    {{-- Hero Section --}}
    <div class="bg-gradient-to-br from-indigo-500 via-purple-500 to-pink-500 rounded-3xl shadow-2xl overflow-hidden mb-12">
        <div class="px-8 py-16 sm:px-16 sm:py-24">
            <div class="max-w-3xl">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-16 h-16 bg-white/20 backdrop-blur rounded-2xl flex items-center justify-center">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z">
                            </path>
                        </svg>
                    </div>
                    <span class="bg-white/20 backdrop-blur text-white text-sm font-semibold px-4 py-2 rounded-full">
                        Versión Beta
                    </span>
                </div>
                
                <h1 class="text-5xl font-bold text-white mb-4">
                    ¡Bienvenido a DocuMeet!
                </h1>
                
                <p class="text-xl text-white/90 mb-8 leading-relaxed">
                    La plataforma integral para gestionar tus reuniones virtuales con transcripción automática 
                    y generación de actas impulsadas por Inteligencia Artificial.
                </p>
                
                @auth
                <a href="{{ route('post.index') }}" 
                   class="inline-flex items-center px-8 py-4 bg-white text-indigo-600 rounded-xl hover:bg-gray-50 transition-all shadow-xl hover:shadow-2xl font-bold text-lg">
                    Ir a Mi Cuenta
                    <svg class="w-6 h-6 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                    </svg>
                </a>
                @else
                <div class="flex flex-wrap gap-4">
                    <a href="{{ route('login') }}" 
                       class="inline-flex items-center px-8 py-4 bg-white text-indigo-600 rounded-xl hover:bg-gray-50 transition-all shadow-xl hover:shadow-2xl font-bold text-lg">
                        Iniciar Sesión
                        <svg class="w-6 h-6 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                        </svg>
                    </a>
                    <a href="{{ route('register') }}" 
                       class="inline-flex items-center px-8 py-4 bg-white/10 backdrop-blur border-2 border-white text-white rounded-xl hover:bg-white/20 transition-all font-bold text-lg">
                        Registrarse
                    </a>
                </div>
                @endauth
            </div>
        </div>
    </div>

    {{-- Características --}}
    <div class="mb-12">
        <h2 class="text-3xl font-bold text-gray-800 text-center mb-12">
            Características Principales
        </h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            {{-- Característica 1 --}}
            <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-200 hover:shadow-xl transition-all">
                <div class="w-14 h-14 bg-blue-100 rounded-xl flex items-center justify-center mb-4">
                    <svg class="w-7 h-7 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                            d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z">
                        </path>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-800 mb-2">Videollamadas HD</h3>
                <p class="text-gray-600">
                    Realiza reuniones virtuales con video y audio de alta calidad usando Daily.co
                </p>
            </div>

            {{-- Característica 2 --}}
            <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-200 hover:shadow-xl transition-all">
                <div class="w-14 h-14 bg-purple-100 rounded-xl flex items-center justify-center mb-4">
                    <svg class="w-7 h-7 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                            d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z">
                        </path>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-800 mb-2">Transcripción Automática</h3>
                <p class="text-gray-600">
                    Convierte automáticamente el audio en texto con Deepgram AI
                </p>
            </div>

            {{-- Característica 3 --}}
            <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-200 hover:shadow-xl transition-all">
                <div class="w-14 h-14 bg-green-100 rounded-xl flex items-center justify-center mb-4">
                    <svg class="w-7 h-7 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                        </path>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-800 mb-2">Actas con IA</h3>
                <p class="text-gray-600">
                    Genera actas automáticamente con resúmenes y compromisos usando Claude AI
                </p>
            </div>

            {{-- Característica 4 --}}
            <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-200 hover:shadow-xl transition-all">
                <div class="w-14 h-14 bg-yellow-100 rounded-xl flex items-center justify-center mb-4">
                    <svg class="w-7 h-7 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z">
                        </path>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-800 mb-2">Gestión de Equipos</h3>
                <p class="text-gray-600">
                    Invita participantes y asigna roles de moderador e invitado
                </p>
            </div>

            {{-- Característica 5 --}}
            <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-200 hover:shadow-xl transition-all">
                <div class="w-14 h-14 bg-indigo-100 rounded-xl flex items-center justify-center mb-4">
                    <svg class="w-7 h-7 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4">
                        </path>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-800 mb-2">Seguimiento de Tareas</h3>
                <p class="text-gray-600">
                    Crea y asigna actividades con responsables y fechas límite
                </p>
            </div>

            {{-- Característica 6 --}}
            <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-200 hover:shadow-xl transition-all">
                <div class="w-14 h-14 bg-red-100 rounded-xl flex items-center justify-center mb-4">
                    <svg class="w-7 h-7 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                            d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z">
                        </path>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-800 mb-2">Exportación PDF/DOCX</h3>
                <p class="text-gray-600">
                    Descarga las actas en formato PDF o Word para compartir
                </p>
            </div>
        </div>
    </div>

    {{-- CTA Final --}}
    <div class="bg-white rounded-2xl shadow-xl border border-gray-200 p-12 text-center">
        <h2 class="text-3xl font-bold text-gray-800 mb-4">
            ¿Listo para empezar?
        </h2>
        <p class="text-gray-600 mb-8 max-w-2xl mx-auto">
            Únete a DocuMeet y transforma la forma en que gestionas tus reuniones virtuales
        </p>
        @guest
        <a href="{{ route('register') }}" 
           class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-xl hover:from-indigo-700 hover:to-purple-700 transition-all shadow-lg hover:shadow-xl font-bold text-lg">
            Crear Cuenta Gratis
            <svg class="w-6 h-6 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
            </svg>
        </a>
        @else
        <a href="{{ route('reuniones.create') }}" 
           class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-xl hover:from-indigo-700 hover:to-purple-700 transition-all shadow-lg hover:shadow-xl font-bold text-lg">
            Crear Primera Reunión
            <svg class="w-6 h-6 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
            </svg>
        </a>
        @endguest
    </div>

</div>
@endsection