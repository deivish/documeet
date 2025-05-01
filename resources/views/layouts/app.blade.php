<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>DocuMeet - </title>
        {{-- @yield('titulo') --}}
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <script src="{{ asset('js/app.js') }}" defer></script>
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
                        <span class="text-gray-600 font-medium">
                            Hola:
                            <a href="{{ route('post.index') }}" class="text-indigo-600 hover:underline">
                                {{ auth()->user()->name }}
                            </a>
                        </span>
                        
                        <form method="POST" action=" {{ route('logout')}}">
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
            @yield('content')
        </main>
        
        {{-- Footer (opcional) --}}
        <footer class="bg-white shadow mt-8">
            <div class="container mx-auto px-4 py-4 text-center text-sm text-gray-500">
                &copy; {{ date('Y') }} DocuMeet. Todos los derechos reservados.
            </div>
        </footer>
    
    </body>
</html>