<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @auth
        <meta name="user-id" content="{{ auth()->id() }}">
    @endauth

    <title>@yield('titulo', 'DocuMeet')</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gradient-to-br from-gray-50 to-gray-100 text-gray-800 min-h-screen flex flex-col">

    {{-- Header Mejorado --}}
    <header class="bg-white shadow-lg sticky top-0 z-50 border-b border-gray-200">
        <div class="container mx-auto px-4 lg:px-8">
            <div class="flex items-center justify-between h-16">

                {{-- Logo y Marca --}}
                <div class="flex items-center gap-3">
                    <a href="/" class="flex items-center gap-3 group">
                        <div
                            class="w-10 h-10 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl flex items-center justify-center shadow-lg group-hover:shadow-xl transition-all">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z">
                                </path>
                            </svg>
                        </div>
                        <span
                            class="text-2xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent hidden sm:block">
                            DocuMeet
                        </span>
                    </a>
                </div>

                @auth
                    {{-- Navegación Desktop --}}
                    <nav class="hidden md:flex items-center space-x-1">
                        {{-- Botón Reuniones --}}
                        <a href="{{ route('reuniones.index') }}"
                            class="px-4 py-2 text-gray-700 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-all font-medium flex items-center gap-2 {{ request()->routeIs('reuniones.index') ? 'bg-indigo-50 text-indigo-600' : '' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                </path>
                            </svg>
                            <span class="hidden lg:block">Reuniones</span>
                        </a>

                        {{-- Botón Historial --}}
                        <a href="{{ route('reuniones.historial') }}"
                            class="px-4 py-2 text-gray-700 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-all font-medium flex items-center gap-2 {{ request()->routeIs('reuniones.historial') ? 'bg-indigo-50 text-indigo-600' : '' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z">
                                </path>
                            </svg>
                            <span class="hidden lg:block">Historial</span>
                        </a>

                        {{-- Notificaciones --}}
                        <div class="relative">
                            <button id="notification-bell" type="button"
                                class="relative text-gray-600 hover:text-indigo-600 focus:outline-none cursor-pointer transition-colors">
                                {{-- Ícono de campana SVG --}}
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9">
                                    </path>
                                </svg>

                                @php
                                    $unreadCount = auth()->user()->unreadNotifications->count();
                                @endphp

                                {{-- Badge de contador --}}
                                <span id="notification-count"
                                    class="absolute -top-2 -right-2 bg-red-600 text-white text-xs font-bold rounded-full h-5 w-5 flex items-center justify-center {{ $unreadCount == 0 ? 'hidden' : '' }}">
                                    {{ $unreadCount }}
                                </span>
                            </button>

                            {{-- Dropdown de notificaciones --}}
                            <div id="notification-dropdown"
                                class="hidden absolute right-0 mt-2 w-80 bg-white border border-gray-200 rounded-lg shadow-xl z-50">

                                {{-- Header del dropdown --}}
                                <div
                                    class="px-4 py-3 border-b border-gray-200 flex items-center justify-between bg-gradient-to-r from-indigo-50 to-purple-50">
                                    <h3 class="text-sm font-semibold text-gray-800 flex items-center gap-2">
                                        <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9">
                                            </path>
                                        </svg>
                                        Notificaciones
                                    </h3>
                                    @if ($unreadCount > 0)
                                        <span class="text-xs bg-indigo-600 text-white px-2 py-1 rounded-full font-medium">
                                            {{ $unreadCount }}
                                        </span>
                                    @endif
                                </div>

                                {{-- Lista de notificaciones --}}
                                <ul id="notification-list" class="max-h-80 overflow-y-auto divide-y divide-gray-100">
                                    @forelse (auth()->user()->unreadNotifications as $notification)
                                        <li class="hover:bg-gray-50 transition-colors">
                                            <a href="{{ route('reuniones.show', $notification->data['reunion_id']) }}"
                                                class="block p-4">
                                                <div class="flex items-start gap-3">
                                                    {{-- Ícono de reunión --}}
                                                    <div class="flex-shrink-0 mt-1">
                                                        <div
                                                            class="w-10 h-10 bg-indigo-100 rounded-full flex items-center justify-center">
                                                            <svg class="w-5 h-5 text-indigo-600" fill="none"
                                                                stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                                                </path>
                                                            </svg>
                                                        </div>
                                                    </div>

                                                    {{-- Contenido de la notificación --}}
                                                    <div class="flex-1 min-w-0">
                                                        <p class="text-sm font-semibold text-gray-800 mb-1">
                                                            {{ $notification->data['titulo'] }}
                                                        </p>
                                                        <p class="text-xs text-gray-500 flex items-center gap-1">
    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
        </path>
    </svg>
    @php
        try {
            // Intenta parsear la fecha en formato ISO
            $fechaHora = \Carbon\Carbon::parse($notification->data['fecha_hora']);
            echo $fechaHora->format('d/m/Y H:i');
        } catch (\Exception $e) {
            // Si falla, muestra la fecha tal cual está guardada
            echo $notification->data['fecha_hora'] ?? 'Fecha no disponible';
        }
    @endphp
</p>
                                                        @if (isset($notification->data['organizador']))
                                                            <p class="text-xs text-gray-400 mt-1">
                                                                Por: {{ $notification->data['organizador'] }}
                                                            </p>
                                                        @endif
                                                    </div>

                                                    {{-- Indicador de no leída --}}
                                                    <div class="flex-shrink-0">
                                                        <span class="w-2 h-2 bg-indigo-600 rounded-full block"></span>
                                                    </div>
                                                </div>
                                            </a>
                                        </li>
                                    @empty
                                        <li class="p-8 text-center">
                                            <svg class="w-16 h-16 mx-auto text-gray-300 mb-3" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9">
                                                </path>
                                            </svg>
                                            <p class="text-gray-500 text-sm font-medium">Sin notificaciones nuevas</p>
                                            <p class="text-gray-400 text-xs mt-1">Te notificaremos cuando te inviten a una
                                                reunión</p>
                                        </li>
                                    @endforelse
                                </ul>

                                {{-- Footer del dropdown --}}
                                @if ($unreadCount > 0)
                                    <div class="px-4 py-3 border-t border-gray-200 bg-gray-50">
                                        <a href="{{ route('reuniones.index') }}"
                                            class="text-xs text-indigo-600 hover:text-indigo-800 font-medium flex items-center justify-center gap-1 transition-colors">
                                            Ver todas las reuniones
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 5l7 7-7 7"></path>
                                            </svg>
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>

                        {{-- Dropdown de Usuario --}}
                        <div class="relative ml-2" x-data="{ open: false }">
                            <button @click="open = !open" type="button"
                                class="flex items-center gap-2 px-3 py-2 text-gray-700 hover:bg-gray-100 rounded-lg transition-all">
                                <div
                                    class="w-8 h-8 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-full flex items-center justify-center">
                                    <span class="text-white text-sm font-bold">
                                        {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                                    </span>
                                </div>
                                <span class="hidden lg:block font-medium">{{ auth()->user()->name }}</span>
                                <svg class="w-4 h-4 transition-transform" :class="{ 'rotate-180': open }" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>

                            <div x-show="open" @click.away="open = false" x-transition
                                class="absolute right-0 mt-2 w-56 bg-white border border-gray-200 rounded-xl shadow-xl z-50">
                                <div class="px-4 py-3 border-b border-gray-200">
                                    <p class="text-sm font-semibold text-gray-800">{{ auth()->user()->name }}</p>
                                    <p class="text-xs text-gray-500">{{ auth()->user()->email }}</p>
                                </div>

                                <div class="py-2">
                                    <a href="{{ route('post.index') }}"
                                        class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 hover:bg-indigo-50 hover:text-indigo-600 transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z">
                                            </path>
                                        </svg>
                                        Mi Cuenta
                                    </a>

                                    <a href="{{ route('reuniones.create') }}"
                                        class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 hover:bg-indigo-50 hover:text-indigo-600 transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 4v16m8-8H4"></path>
                                        </svg>
                                        Nueva Reunión
                                    </a>
                                </div>

                                <div class="border-t border-gray-200 py-2">
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit"
                                            class="flex items-center gap-3 px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors w-full text-left">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1">
                                                </path>
                                            </svg>
                                            Cerrar Sesión
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </nav>

                    {{-- Menú Móvil --}}
                    <div class="md:hidden">
                        <button id="mobile-menu-button" type="button"
                            class="p-2 text-gray-600 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-all">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 6h16M4 12h16M4 18h16"></path>
                            </svg>
                        </button>
                    </div>
                @endauth

                @guest
                    <nav class="flex items-center gap-3">
                        <a href="/login"
                            class="px-4 py-2 text-gray-700 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-all font-medium">
                            Iniciar Sesión
                        </a>
                        <a href="{{ route('register') }}"
                            class="px-4 py-2 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-lg hover:from-indigo-700 hover:to-purple-700 transition-all font-medium shadow-md hover:shadow-lg">
                            Registrarse
                        </a>
                    </nav>
                @endguest
            </div>

            {{-- Menú Móvil Desplegable --}}
            @auth
                <div id="mobile-menu" class="hidden md:hidden border-t border-gray-200 py-4">
                    <div class="space-y-2">
                        <a href="{{ route('post.index') }}"
                            class="flex items-center gap-3 px-4 py-2 text-gray-700 hover:bg-indigo-50 hover:text-indigo-600 rounded-lg transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z">
                                </path>
                            </svg>
                            Mi Cuenta
                        </a>

                        <a href="{{ route('reuniones.index') }}"
                            class="flex items-center gap-3 px-4 py-2 text-gray-700 hover:bg-indigo-50 hover:text-indigo-600 rounded-lg transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                </path>
                            </svg>
                            Mis Reuniones
                        </a>

                        <a href="{{ route('reuniones.create') }}"
                            class="flex items-center gap-3 px-4 py-2 text-gray-700 hover:bg-indigo-50 hover:text-indigo-600 rounded-lg transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4">
                                </path>
                            </svg>
                            Nueva Reunión
                        </a>

                        <a href="{{ route('reuniones.historial') }}"
                            class="flex items-center gap-3 px-4 py-2 text-gray-700 hover:bg-indigo-50 hover:text-indigo-600 rounded-lg transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z">
                                </path>
                            </svg>
                            Historial
                        </a>

                        <div class="border-t border-gray-200 pt-2 mt-2">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                    class="flex items-center gap-3 px-4 py-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors w-full text-left">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1">
                                        </path>
                                    </svg>
                                    Cerrar Sesión
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endauth
        </div>
    </header>

    {{-- Titulo (solo visible si hay contenido) --}}
    @hasSection('titulo')
        <div class="bg-white border-b border-gray-200 py-6">
            <div class="container mx-auto px-4">
                <h1 class="text-3xl font-bold text-gray-800">
                    @yield('titulo')
                </h1>
            </div>
        </div>
    @endif

    {{-- Contenido --}}
    <main class="flex-grow container mx-auto px-4 py-8">
        {{-- Mensaje de error --}}
        @if (session('error'))
            <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 5000)" x-show="show" x-transition
                class="fixed top-20 right-4 bg-red-100 border-l-4 border-red-500 text-red-700 px-6 py-4 rounded-lg shadow-2xl z-50 max-w-md">
                <div class="flex items-start gap-3">
                    <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                        </path>
                    </svg>
                    <div class="flex-1">
                        <p class="font-semibold">Error</p>
                        <p class="text-sm">{{ session('error') }}</p>
                    </div>
                    <button @click="show = false" class="text-red-500 hover:text-red-700">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>
        @endif

        {{-- Mensaje de éxito --}}
        @if (session('success'))
            <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 5000)" x-show="show" x-transition
                class="fixed top-20 right-4 bg-green-100 border-l-4 border-green-500 text-green-700 px-6 py-4 rounded-lg shadow-2xl z-50 max-w-md">
                <div class="flex items-start gap-3">
                    <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z">
                        </path>
                    </svg>
                    <div class="flex-1">
                        <p class="font-semibold">Éxito</p>
                        <p class="text-sm">{{ session('success') }}</p>
                    </div>
                    <button @click="show = false" class="text-green-500 hover:text-green-700">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>
        @endif

        @yield('content')
    </main>

    {{-- Footer --}}
    <footer class="bg-white border-t border-gray-200 shadow-lg mt-auto">
        <div class="container mx-auto px-4 py-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div
                        class="w-8 h-8 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z">
                            </path>
                        </svg>
                    </div>
                    <span class="font-semibold text-gray-800">DocuMeet</span>
                </div>

                <p class="text-sm text-gray-500">
                    &copy; {{ date('Y') }} DocuMeet. Todos los derechos reservados.
                </p>

                <div class="flex items-center gap-4 text-sm text-gray-500">
                    <a href="#" class="hover:text-indigo-600 transition-colors">Privacidad</a>
                    <a href="#" class="hover:text-indigo-600 transition-colors">Términos</a>
                    <a href="#" class="hover:text-indigo-600 transition-colors">Soporte</a>
                </div>
            </div>
        </div>
    </footer>

    {{-- Alpine.js --}}
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>


</body>

</html>
