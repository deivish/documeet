@extends('layouts.app')

@section('titulo')
    {{ isset($reunion) ? 'Editar Reunión' : 'Nueva Reunión' }}
@endsection

@section('content')
    <div class="max-w-4xl mx-auto px-4 py-8">

        {{-- Header con botón volver --}}
        <div class="mb-8">
            <a href="{{ route('reuniones.index') }}"
                class="inline-flex items-center text-gray-600 hover:text-indigo-600 font-medium transition-colors group mb-4">
                <svg class="w-5 h-5 mr-2 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                Volver a Reuniones
            </a>

            <div class="flex items-center gap-4">
                <div
                    class="w-14 h-14 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl flex items-center justify-center shadow-lg">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="{{ isset($reunion) ? 'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z' : 'M12 4v16m8-8H4' }}">
                        </path>
                    </svg>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-gray-800">
                        {{ isset($reunion) ? 'Editar Reunión' : 'Nueva Reunión' }}
                    </h1>
                    <p class="text-gray-600">
                        {{ isset($reunion) ? 'Actualiza los detalles de tu reunión' : 'Programa una reunión con tu equipo' }}
                    </p>
                </div>
            </div>
        </div>

        {{-- Formulario --}}
        <form action="{{ isset($reunion) ? route('reuniones.update', $reunion->id) : route('reuniones.store') }}"
            method="POST" class="bg-white rounded-2xl shadow-xl border border-gray-200 overflow-hidden">
            @csrf
            @if (isset($reunion))
                @method('PUT')
            @endif

            {{-- Sección: Información Básica --}}
            <div class="bg-gradient-to-r from-indigo-50 to-purple-50 px-8 py-6 border-b border-gray-200">
                <h2 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                    <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                        </path>
                    </svg>
                    Información de la Reunión
                </h2>
            </div>

            <div class="p-8 space-y-6">
                {{-- Título --}}
                <div>
                    <label for="titulo" class="block text-sm font-semibold text-gray-700 mb-2">
                        Título de la Reunión *
                    </label>
                    <input type="text" name="titulo" id="titulo" value="{{ old('titulo', $reunion->titulo ?? '') }}"
                        placeholder="Ej: Reunión de Sprint Planning"
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                        required>
                </div>

                {{-- Descripción --}}
                <div>
                    <label for="descripcion" class="block text-sm font-semibold text-gray-700 mb-2">
                        Descripción *
                    </label>
                    <textarea name="descripcion" id="descripcion" rows="4"
                        placeholder="Describe los objetivos y temas a tratar en la reunión..."
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition resize-none"
                        required>{{ old('descripcion', $reunion->descripcion ?? '') }}</textarea>
                </div>

                {{-- Fecha y Hora --}}
                <div>
                    <label for="fecha_hora" class="block text-sm font-semibold text-gray-700 mb-2">
                        Fecha y Hora *
                    </label>
                    <input type="datetime-local" name="fecha_hora" id="fecha_hora"
                        value="{{ old('fecha_hora', isset($reunion) ? \Carbon\Carbon::parse($reunion->fecha_hora)->format('Y-m-d\TH:i') : '') }}"
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                        required>
                </div>
            </div>

            {{-- Sección: Actividades --}}
            <div class="bg-gradient-to-r from-green-50 to-emerald-50 px-8 py-6 border-t border-b border-gray-200">
                <h2 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4">
                        </path>
                    </svg>
                    Actividades
                </h2>
                <p class="text-sm text-gray-600 mt-1">Agrega las tareas que se deben realizar</p>
            </div>

            <div class="p-8">
                <div id="actividades-container" class="space-y-4">
                    @php $actividadIndex = 0; @endphp

                    @if (isset($reunion) && $reunion->actividades)
                        @foreach ($reunion->actividades as $actividad)
                            <div
                                class="actividad bg-gray-50 border-2 border-gray-200 rounded-xl p-6 relative hover:border-indigo-300 transition">
                                <button type="button"
                                    class="absolute top-4 right-4 w-8 h-8 bg-red-100 text-red-600 rounded-full hover:bg-red-200 transition flex items-center justify-center"
                                    onclick="eliminarActividad(this)">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>

                                <input type="hidden" name="actividades[{{ $actividadIndex }}][id]"
                                    value="{{ $actividad->id }}">

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                    <input type="text" name="actividades[{{ $actividadIndex }}][nombre]"
                                        value="{{ $actividad->nombre }}" placeholder="Nombre de la actividad"
                                        class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                        required>

                                    <input type="text" name="actividades[{{ $actividadIndex }}][responsable]"
                                        value="{{ $actividad->responsable }}" placeholder="Responsable"
                                        class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                        required>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <input type="date" name="actividades[{{ $actividadIndex }}][fecha_entrega]"
                                        value="{{ $actividad->fecha_entrega }}"
                                        class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                        required>

                                    <textarea name="actividades[{{ $actividadIndex }}][descripcion]" placeholder="Descripción"
                                        class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent resize-none"
                                        required>{{ $actividad->descripcion }}</textarea>
                                </div>
                            </div>
                            @php $actividadIndex++; @endphp
                        @endforeach
                    @endif

                    {{-- Actividad inicial si es nueva reunión --}}
                    @if (!isset($reunion))
                        <div
                            class="actividad bg-gray-50 border-2 border-gray-200 rounded-xl p-6 relative hover:border-indigo-300 transition">
                            <button type="button"
                                class="absolute top-4 right-4 w-8 h-8 bg-red-100 text-red-600 rounded-full hover:bg-red-200 transition flex items-center justify-center"
                                onclick="eliminarActividad(this)">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                <input type="text" name="actividades[0][nombre]" placeholder="Nombre de la actividad"
                                    class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                    required>

                                <input type="text" name="actividades[0][responsable]" placeholder="Responsable"
                                    class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                    required>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <input type="date" name="actividades[0][fecha_entrega]"
                                    class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                    required>

                                <textarea name="actividades[0][descripcion]" rows="2" placeholder="Descripción"
                                    class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent resize-none"
                                    required></textarea>
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Contenedor para actividades eliminadas --}}
                <div id="actividades_eliminar_container"></div>

                {{-- Botón agregar actividad --}}
                <button type="button" onclick="agregarActividad()"
                    class="mt-6 inline-flex items-center px-6 py-3 bg-green-600 hover:bg-green-700 text-white font-medium rounded-xl transition-all shadow-md hover:shadow-lg">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Añadir Actividad
                </button>
            </div>

            {{-- Footer con botones --}}
            <div class="bg-gray-50 px-8 py-6 border-t border-gray-200 flex flex-col sm:flex-row gap-4 justify-end">
                <a href="{{ route('reuniones.index') }}"
                    class="inline-flex items-center justify-center px-6 py-3 bg-white border-2 border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 transition-all font-medium">
                    Cancelar
                </a>
                <button type="submit"
                    class="inline-flex items-center justify-center px-8 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-xl hover:from-indigo-700 hover:to-purple-700 transition-all shadow-lg hover:shadow-xl font-bold">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="{{ isset($reunion) ? 'M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15' : 'M5 13l4 4L19 7' }}">
                        </path>
                    </svg>
                    {{ isset($reunion) ? 'Actualizar Reunión' : 'Crear Reunión' }}
                </button>
            </div>
        </form>
    </div>

    {{-- JavaScript --}}
    <script>
        let actividadIndex = {{ isset($reunion) ? count($reunion->actividades) : 1 }};

        function agregarActividad() {
            const container = document.getElementById('actividades-container');
            const nuevaActividad = document.createElement('div');
            nuevaActividad.classList.add('actividad', 'bg-gray-50', 'border-2', 'border-gray-200', 'rounded-xl', 'p-6',
                'relative', 'hover:border-indigo-300', 'transition');

            nuevaActividad.innerHTML = `
            <button type="button" 
                    class="absolute top-4 right-4 w-8 h-8 bg-red-100 text-red-600 rounded-full hover:bg-red-200 transition flex items-center justify-center"  
                    onclick="eliminarActividad(this)">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <input type="text" 
                       name="actividades[${actividadIndex}][nombre]" 
                       placeholder="Nombre de la actividad"
                       class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                       required>

                <input type="text" 
                       name="actividades[${actividadIndex}][responsable]" 
                       placeholder="Responsable"
                       class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                       required>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <input type="date" 
                       name="actividades[${actividadIndex}][fecha_entrega]" 
                       class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                       required>

                <textarea name="actividades[${actividadIndex}][descripcion]" 
                          rows="2" 
                          placeholder="Descripción"
                          class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent resize-none"
                          required></textarea>
            </div>
        `;

            container.appendChild(nuevaActividad);
            actividadIndex++;
        }

        function eliminarActividad(button) {
            const actividadDiv = button.parentElement;
            const idInput = actividadDiv.querySelector('input[name*="[id]"]');

            if (idInput) {
                const id = idInput.value;
                const container = document.getElementById('actividades_eliminar_container');
                const hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                hiddenInput.name = 'actividades_eliminar[]';
                hiddenInput.value = id;
                container.appendChild(hiddenInput);
            }

            actividadDiv.remove();
        }
    </script>
@endsection
