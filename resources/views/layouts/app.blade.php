<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">


        @auth
        <meta name="user-id" content="{{ auth()->id() }}">
        @endauth

        <title>DocuMeet - </title>
        {{-- @yield('titulo') --}}
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        {{-- <script src="{{ asset('js/app.js') }}" defer></script> --}}
    </head>
    <body class="bg-gray-50 text-gray-800 min-h-screen flex flex-col">

        {{-- Header --}}
        <header class="bg-white shadow">
            <div class="container mx-auto px-4 py-4 flex justify-between items-center">
                <div class="flex items-center space-x-2">
                    <img src="{{ asset('logo.svg') }}" alt="Logo DocuMeet" class="h-8 w-8">
                    <a href="/"><span class="text-xl font-semibold text-indigo-600">DocuMeet</span></a>
                </div>
 
                    @auth
        <nav class="flex items-center space-x-6">
            {{-- Notificación (campana) --}}
            <div class="relative">
                <button id="notification-bell" onclick="document.getElementById('notification-dropdown').classList.toggle('hidden')"
                class="relative text-gray-600 hover:text-indigo-600 focus:outline-none cursor-pointer">
                🔔
                @php
                $unreadCount = auth()->user()->unreadNotifications->count();
                @endphp
                <span id="notification-count"
                class="absolute -top-2 -right-2 bg-red-600 text-white text-xs rounded-full px-1 {{ $unreadCount == 0 ? 'hidden' : '' }}">
                {{ $unreadCount }}
                </span>
                </button>


            
                {{-- Dropdown de notificaciones --}}
                <div id="notification-dropdown"
                    class="absolute right-0 mt-2 w-80 bg-white border border-gray-200 rounded shadow-lg hidden z-50">
                    <div class="p-4 text-sm font-semibold border-b">Notificaciones</div>
                    <ul id="notification-list" class="max-h-64 overflow-y-auto divide-y">
                        @forelse (auth()->user()->unreadNotifications as $notification)
                            <li class="p-4 text-sm text-gray-700 hover:bg-gray-100">
                                <a href="{{ route('reuniones.show', $notification->data['reunion_id']) }}">
                                    <strong>{{ $notification->data['titulo'] }}</strong><br>
                                    Fecha: {{ \Carbon\Carbon::parse($notification->data['fecha_hora'])->format('d/m/Y H:i') }}
                                </a>
                            </li>
                        @empty
                            <li class="p-4 text-gray-500 text-sm">Sin notificaciones</li>
                        @endforelse
                    </ul>
                    
                </div>
            </div>
            

            <span class="text-gray-600 font-medium">
                Hola:
                <a href="{{ route('post.index') }}" class="text-indigo-600 hover:underline">
                    {{ auth()->user()->name }}
                </a>
            </span>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="text-gray-600 hover:text-indigo-600 font-medium">Cerrar Sesión</button>
            </form>
        </nav>
    @endauth

                @guest
                    <nav class="space-x-4">
                        <a href="/login" class="text-gray-600 hover:text-indigo-600 font-medium">Login</a>
                        <a href="{{ route('register')}}" class="text-gray-600 hover:text-indigo-600 font-medium">Registro</a>
                    </nav>  
                @endguest
                
            </div>
        </header>
        
        {{-- Titulo --}}
        <h1  class="text-center text-2xl font-semibold text-gray-800 mb-4 mt-4">@yield('titulo')</h1>


        {{-- Contenido --}}
        <main class="flex-grow container mx-auto px-4 py-8">

            @if(session('error'))
                <div 
                    x-data="{ show: true }"
                    x-init="setTimeout(() => show = false, 5000)"
                    x-show="show"
                    x-transition
                    class="fixed top-4 left-1/2 transform -translate-x-1/2 bg-red-100 border border-red-400 text-red-700 px-6 py-4 rounded-lg shadow-lg z-50">
                    {{ session('error') }}
                </div>
            @endif



            @yield('content')
        </main>
        
        {{-- Footer (opcional) --}}
        <footer class="bg-white shadow mt-8">
            <div class="container mx-auto px-4 py-4 text-center text-sm text-gray-500">
                &copy; {{ date('Y') }} DocuMeet. Todos los derechos reservados.
            </div>
        </footer>

    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    </body>
</html>