@extends('layouts.app')

@section('titulo')
    Acta de la Reunión — {{ $reunion->titulo }}
@endsection

@section('content')
    <div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 py-8">
        <div class="max-w-6xl mx-auto px-4">

            <!-- Header con botón de regreso -->
            <div class="mb-6 flex items-center justify-between">
                <a href="{{ route('reuniones.show', $reunion->id) }}"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-white text-gray-700 hover:text-indigo-600 font-medium rounded-lg shadow-sm hover:shadow-md transition-all duration-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                    Volver a la reunión
                </a>

                <!-- Badge de estado -->
                <span
                    class="px-4 py-2 rounded-full text-sm font-semibold shadow-sm
                    {{ $acta->estado === 'final' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                    {{ $acta->estado === 'final' ? '✓ Finalizada' : '⏳ Borrador' }}
                </span>
            </div>

            <!-- Card principal del acta -->
            <div class="bg-white shadow-xl rounded-2xl overflow-hidden border border-gray-100">

                <!-- Header del acta -->
                <div class="bg-gradient-to-r from-indigo-600 to-purple-600 px-8 py-6 text-white">
                    <div class="flex items-center gap-3 mb-2">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                            </path>
                        </svg>
                        <h1 class="text-3xl font-bold">Acta de Reunión</h1>
                    </div>
                    <p class="text-indigo-100 text-sm">Documento generado automáticamente</p>
                </div>

                <!-- Información de la reunión -->
                <div class="px-8 py-6 bg-gradient-to-b from-indigo-50 to-white border-b border-gray-200">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="flex items-start gap-3">
                            <div class="flex-shrink-0 w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 font-medium uppercase">Título</p>
                                <p class="text-gray-900 font-semibold">{{ $reunion->titulo }}</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-3">
                            <div class="flex-shrink-0 w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                    </path>
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 font-medium uppercase">Fecha</p>
                                <p class="text-gray-900 font-semibold">
                                    {{ \Carbon\Carbon::parse($reunion->fecha_hora)->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>
                    </div>

                    @if ($reunion->descripcion)
                        <div class="mt-4 p-4 bg-white rounded-lg border border-gray-200">
                            <p class="text-xs text-gray-500 font-medium uppercase mb-1">Descripción</p>
                            <p class="text-gray-700">{{ $reunion->descripcion }}</p>
                        </div>
                    @endif
                </div>

                <!-- Contenido del acta -->
                <div class="px-8 py-6 space-y-8">

                    <!-- Sección: Actividades -->
                    <section>
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                                        </path>
                                    </svg>
                                </div>
                                <h2 class="text-xl font-bold text-gray-800">Actividades</h2>
                            </div>
                        </div>

                        <!-- Tabla de actividades -->
                        <div class="bg-white rounded-lg border border-gray-200 overflow-hidden mb-4">
                            <table class="w-full">
                                <thead class="bg-gray-50 border-b border-gray-200">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">
                                            Actividad</th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">
                                            Responsable</th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Fecha
                                            límite</th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">
                                            Descripción</th>
                                        <th
                                            class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase w-20">
                                            Acción</th>
                                    </tr>
                                </thead>
                                <tbody id="actividades-table" class="divide-y divide-gray-200">
                                    @forelse ($reunion->actividades as $actividad)
                                        <tr id="actividad-{{ $actividad->id }}" class="hover:bg-gray-50 transition-colors">
                                            <td class="px-4 py-3 text-sm text-gray-900" contenteditable="true"
                                                data-field="nombre">{{ $actividad->nombre }}</td>
                                            <td class="px-4 py-3 text-sm text-gray-700" contenteditable="true"
                                                data-field="responsable">{{ $actividad->responsable }}</td>
                                            <td class="px-4 py-3 text-sm text-gray-700" contenteditable="true"
                                                data-field="fecha_entrega">
                                                {{ \Carbon\Carbon::parse($actividad->fecha_entrega)->format('Y-m-d') }}
                                            </td>
                                            <td class="px-4 py-3 text-sm text-gray-700" contenteditable="true"
                                                data-field="descripcion">{{ $actividad->descripcion }}</td>
                                            <td class="px-4 py-3 text-center">
                                                <button onclick="eliminarActividad({{ $actividad->id }})"
                                                    class="text-red-600 hover:text-red-800 hover:bg-red-50 p-2 rounded-lg transition-colors">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                        </path>
                                                    </svg>
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="px-4 py-8 text-center text-gray-500">
                                                <svg class="w-12 h-12 mx-auto mb-2 text-gray-300" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                                                    </path>
                                                </svg>
                                                No hay actividades registradas
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- Acciones de actividades -->
                        <div class="flex gap-3 mb-4">
                            <button onclick="actualizarActividades()"
                                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-all shadow-sm hover:shadow-md flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                                    </path>
                                </svg>
                                Actualizar Actividades
                            </button>
                        </div>

                        <!-- Formulario nueva actividad -->
                        <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                            <p class="text-sm font-semibold text-gray-700 mb-3">➕ Agregar nueva actividad</p>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3 mb-3">
                                <input type="text" id="nombre" placeholder="Nombre de la actividad"
                                    class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <input type="text" id="responsableAct" placeholder="Responsable"
                                    class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <input type="date" id="fecha_entrega"
                                    class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <input type="text" id="descripcionAct" placeholder="Descripción"
                                    class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>
                            <button id="btn-guardar-actividad"
                                class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-all shadow-sm hover:shadow-md flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4v16m8-8H4"></path>
                                </svg>
                                Guardar Actividad
                            </button>
                        </div>
                    </section>

                    <!-- Divisor con IA -->
                    <div class="border-t border-gray-200 pt-8">
                        <div
                            class="bg-gradient-to-r from-purple-50 to-indigo-50 rounded-xl p-6 border border-purple-200 mb-6">
                            <div class="flex items-start gap-4">
                                <div
                                    class="flex-shrink-0 w-12 h-12 bg-gradient-to-br from-purple-500 to-indigo-600 rounded-xl flex items-center justify-center shadow-lg">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <h3 class="text-lg font-bold text-gray-800 mb-1">Herramientas con IA</h3>
                                    <p class="text-sm text-gray-600 mb-4">Extrae compromisos y genera resúmenes
                                        automáticamente usando inteligencia artificial</p>
                                    <div class="flex flex-wrap gap-3">
                                        <button id="btn-extraer-compromisos"
                                            class="px-4 py-2 bg-gradient-to-r from-purple-600 to-purple-700 text-white rounded-lg hover:from-purple-700 hover:to-purple-800 transition-all shadow-md hover:shadow-lg flex items-center gap-2">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4">
                                                </path>
                                            </svg>
                                            Extraer Compromisos
                                        </button>

                                        <button id="btn-generar-resumen"
                                            class="px-4 py-2 bg-gradient-to-r from-indigo-600 to-indigo-700 text-white rounded-lg hover:from-indigo-700 hover:to-indigo-800 transition-all shadow-md hover:shadow-lg flex items-center gap-2">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                                </path>
                                            </svg>
                                            Generar Resumen
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ✅ MOSTRAR RESUMEN GENERADO --}}
                    @if ($acta->resumen)
                        <div class="bg-white rounded-xl p-6 border border-indigo-200 shadow-sm mb-6">
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center gap-2">
                                    <div class="w-8 h-8 bg-indigo-100 rounded-lg flex items-center justify-center">
                                        <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                            </path>
                                        </svg>
                                    </div>
                                    <h3 class="text-lg font-bold text-gray-800">Resumen Ejecutivo</h3>
                                    <span
                                        class="px-3 py-1 bg-gradient-to-r from-indigo-100 to-purple-100 text-indigo-700 text-xs font-semibold rounded-full border border-indigo-200">
                                        ✨ Generado con Claude AI
                                    </span>
                                </div>

                                {{-- Botón para regenerar el resumen --}}
                                <button id="btn-regenerar-resumen"
                                    class="px-3 py-1.5 bg-indigo-50 text-indigo-600 text-sm rounded-lg hover:bg-indigo-100 transition-all flex items-center gap-2 border border-indigo-200">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                                        </path>
                                    </svg>
                                    Regenerar
                                </button>
                            </div>

                            <div class="prose prose-sm max-w-none">
                                <div
                                    class="text-gray-700 leading-relaxed whitespace-pre-line bg-indigo-50/30 p-4 rounded-lg border border-indigo-100">
                                    {{ $acta->resumen }}
                                </div>
                            </div>

                            <div class="mt-3 flex items-center gap-2 text-xs text-gray-500">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                                    </path>
                                </svg>
                                <span>Este resumen se incluirá automáticamente en el PDF del acta</span>
                            </div>
                        </div>
                    @endif

                    <!-- Sección: Compromisos -->
                    <section>
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center">
                                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4">
                                        </path>
                                    </svg>
                                </div>
                                <h2 class="text-xl font-bold text-gray-800">Compromisos</h2>
                            </div>
                        </div>

                        <!-- Tabla de compromisos -->
                        <div class="bg-white rounded-lg border border-gray-200 overflow-hidden mb-4">
                            <table class="w-full">
                                <thead class="bg-gray-50 border-b border-gray-200">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">
                                            Compromiso</th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">
                                            Responsable</th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Fecha
                                        </th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">
                                            Resultado esperado</th>
                                        <th
                                            class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase w-20">
                                            Acción</th>
                                    </tr>
                                </thead>
                                <tbody id="compromisos-table" class="divide-y divide-gray-200">
                                    @forelse ($reunion->compromisos as $compromiso)
                                        <tr id="compromiso-{{ $compromiso->id }}"
                                            class="hover:bg-gray-50 transition-colors">
                                            <td class="px-4 py-3 text-sm text-gray-900" contenteditable="true"
                                                data-field="descripcion">{{ $compromiso->descripcion }}</td>
                                            <td class="px-4 py-3 text-sm text-gray-700" contenteditable="true"
                                                data-field="responsable">{{ $compromiso->responsable }}</td>
                                            <td class="px-4 py-3 text-sm text-gray-700" contenteditable="true"
                                                data-field="fecha">
                                                {{ \Carbon\Carbon::parse($compromiso->fecha)->format('Y-m-d') }}</td>
                                            <td class="px-4 py-3 text-sm text-gray-700" contenteditable="true"
                                                data-field="resultado">{{ $compromiso->resultado }}</td>
                                            <td class="px-4 py-3 text-center">
                                                <button onclick="eliminarCompromiso({{ $compromiso->id }})"
                                                    class="text-red-600 hover:text-red-800 hover:bg-red-50 p-2 rounded-lg transition-colors">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                        </path>
                                                    </svg>
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="px-4 py-8 text-center text-gray-500">
                                                <svg class="w-12 h-12 mx-auto mb-2 text-gray-300" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4">
                                                    </path>
                                                </svg>
                                                No hay compromisos registrados
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- Acciones de compromisos -->
                        <div class="flex gap-3 mb-4">
                            <button onclick="actualizarCompromisos()"
                                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-all shadow-sm hover:shadow-md flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                                    </path>
                                </svg>
                                Actualizar Compromisos
                            </button>
                        </div>

                        <!-- Formulario nuevo compromiso -->
                        <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                            <p class="text-sm font-semibold text-gray-700 mb-3">➕ Agregar nuevo compromiso</p>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3 mb-3">
                                <input type="text" id="descripcion" placeholder="Descripción del compromiso"
                                    class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                                <input type="text" id="responsable" placeholder="Responsable"
                                    class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                                <input type="date" id="fecha"
                                    class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                                <input type="text" id="resultado" placeholder="Resultado esperado"
                                    class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                            </div>
                            <button id="btn-guardar"
                                class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-all shadow-sm hover:shadow-md flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4v16m8-8H4"></path>
                                </svg>
                                Guardar Compromiso
                            </button>
                        </div>
                    </section>

                    <!-- Botones de descarga -->
                    <section class="pt-6 border-t border-gray-200">
                        <h3 class="text-sm font-semibold text-gray-700 mb-3">📥 Descargar acta</h3>
                        <div class="flex flex-wrap gap-3">
                            @if ($acta->archivo_pdf)
                                <a href="{{ route('actas.pdf', $acta) }}" target="_blank"
                                    class="px-5 py-3 bg-gradient-to-r from-red-600 to-red-700 text-white rounded-lg hover:from-red-700 hover:to-red-800 transition-all shadow-md hover:shadow-lg flex items-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z">
                                        </path>
                                    </svg>
                                    Descargar PDF
                                </a>
                            @endif

                            @if ($acta->archivo_docx)
                                <a href="{{ route('actas.docx', $acta) }}" 
    class="px-5 py-3 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-lg hover:from-blue-700 hover:to-blue-800 transition-all shadow-md hover:shadow-lg flex items-center gap-2">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
        </path>
    </svg>
    Descargar Word
</a>
                            @endif
                        </div>
                    </section>

                </div>
            </div>
        </div>

        <!-- Toast de notificación -->
        <div id="toast"
            class="hidden fixed bottom-6 right-6 px-6 py-4 rounded-xl shadow-2xl transition-all duration-300 transform translate-y-2 opacity-0">
        </div>
    </div>

    <!-- Scripts (sin cambios funcionales) -->
    <script>
        const reunionId = {{ $reunion->id }};

        // Actualizar actividades
        function actualizarActividades() {
            document.querySelectorAll('#actividades-table tr').forEach(row => {
                const id = row.id.replace('actividad-', '');
                const data = {};
                row.querySelectorAll('[contenteditable]').forEach(cell => {
                    data[cell.dataset.field] = cell.innerText.trim();
                });

                fetch(`/actas/actividades/${id}`, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify(data)
                    })
                    .then(res => res.json())
                    .then(resp => {
                        if (resp.ok) {
                            showToast(`✅ Actividad actualizada`, 'bg-green-600');
                        } else {
                            showToast(`⚠️ Error al actualizar`, 'bg-red-600');
                        }
                    });
            });
        }

        // Actualizar compromisos
        function actualizarCompromisos() {
            document.querySelectorAll('#compromisos-table tr').forEach(row => {
                const id = row.id.replace('compromiso-', '');
                const data = {};
                row.querySelectorAll('[contenteditable]').forEach(cell => {
                    data[cell.dataset.field] = cell.innerText.trim();
                });

                fetch(`/compromisos/${id}`, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify(data)
                    })
                    .then(res => res.json())
                    .then(resp => {
                        if (resp.ok) {
                            showToast(`✅ Compromiso actualizado`, 'bg-green-600');
                        } else {
                            showToast(`⚠️ Error al actualizar`, 'bg-red-600');
                        }
                    });
            });
        }

        // Toast mejorado
        function showToast(message, bgClass = 'bg-green-600') {
            const toast = document.getElementById('toast');
            toast.innerHTML = `
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <span class="text-white font-medium">${message}</span>
                </div>
            `;
            toast.className =
                `fixed bottom-6 right-6 px-6 py-4 rounded-xl shadow-2xl transition-all duration-300 ${bgClass}`;
            toast.classList.remove('hidden');

            setTimeout(() => toast.classList.add('opacity-100', 'translate-y-0'), 10);

            setTimeout(() => {
                toast.classList.remove('opacity-100', 'translate-y-0');
                toast.classList.add('opacity-0', 'translate-y-2');
                setTimeout(() => toast.classList.add('hidden'), 300);
            }, 3000);
        }

        // Extraer compromisos con IA
        document.getElementById('btn-extraer-compromisos').addEventListener('click', function() {
            const btn = this;
            btn.disabled = true;
            const originalHTML = btn.innerHTML;
            btn.innerHTML = `
                <svg class="animate-spin w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
                Extrayendo...
            `;

            fetch("{{ route('actas.extraer-compromisos', $acta) }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                })
                .then(res => res.json())
                .then(data => {
                    if (data.ok) {
                        showToast(`✅ ${data.total} compromisos extraídos`, 'bg-green-600');
                        setTimeout(() => location.reload(), 1500);
                    } else {
                        showToast('❌ ' + data.error, 'bg-red-600');
                    }
                })
                .catch(err => {
                    console.error(err);
                    showToast('❌ Error al extraer compromisos', 'bg-red-600');
                })
                .finally(() => {
                    btn.disabled = false;
                    btn.innerHTML = originalHTML;
                });
        });

        // Generar resumen con IA
        document.getElementById('btn-generar-resumen').addEventListener('click', function() {
            const btn = this;
            btn.disabled = true;
            const originalHTML = btn.innerHTML;
            btn.innerHTML = `
                <svg class="animate-spin w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
                Generando...
            `;

            fetch("{{ route('actas.generar-resumen', $acta) }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                })
                .then(res => res.json())
                .then(data => {
                    if (data.ok) {
                        showToast('✅ Resumen generado', 'bg-green-600');
                        setTimeout(() => location.reload(), 1500);
                    } else {
                        showToast('❌ ' + data.error, 'bg-red-600');
                    }
                })
                .catch(err => {
                    console.error(err);
                    showToast('❌ Error al generar resumen', 'bg-red-600');
                })
                .finally(() => {
                    btn.disabled = false;
                    btn.innerHTML = originalHTML;
                });
        });

        // Regenerar resumen (mismo código que generar, pero con confirmación)
        @if ($acta->resumen)
            document.getElementById('btn-regenerar-resumen').addEventListener('click', function() {
                if (!confirm('¿Estás seguro de regenerar el resumen? El resumen actual se reemplazará.')) {
                    return;
                }

                const btn = this;
                btn.disabled = true;
                const originalHTML = btn.innerHTML;
                btn.innerHTML = `
        <svg class="animate-spin w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
            </path>
        </svg>
        Regenerando...
    `;

                fetch("{{ route('actas.generar-resumen', $acta) }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.ok) {
                            showToast('✅ Resumen regenerado exitosamente', 'bg-green-600');
                            setTimeout(() => location.reload(), 1500);
                        } else {
                            showToast('❌ ' + data.error, 'bg-red-600');
                        }
                    })
                    .catch(err => {
                        console.error(err);
                        showToast('❌ Error al regenerar resumen', 'bg-red-600');
                    })
                    .finally(() => {
                        btn.disabled = false;
                        btn.innerHTML = originalHTML;
                    });
            });
        @endif
    </script>
    <script>
        // ======================= FUNCIONES PARA AGREGAR =======================

        // Agregar nueva actividad
        document.getElementById('btn-guardar-actividad').addEventListener('click', function() {
            const nombre = document.getElementById('nombre').value.trim();
            const responsable = document.getElementById('responsableAct').value.trim();
            const fecha = document.getElementById('fecha_entrega').value;
            const descripcion = document.getElementById('descripcionAct').value.trim();

            if (!nombre || !responsable || !fecha) {
                showToast('⚠️ Completa todos los campos obligatorios', 'bg-yellow-600');
                return;
            }

            fetch(`/actas/{{ $acta->id }}/actividades`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        nombre: nombre,
                        responsable: responsable,
                        fecha_entrega: fecha,
                        descripcion: descripcion
                    })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.ok) {
                        showToast('✅ Actividad agregada', 'bg-green-600');

                        // Limpiar campos
                        document.getElementById('nombre').value = '';
                        document.getElementById('responsableAct').value = '';
                        document.getElementById('fecha_entrega').value = '';
                        document.getElementById('descripcionAct').value = '';

                        // Recargar después de 1 segundo
                        setTimeout(() => location.reload(), 1000);
                    } else {
                        showToast('❌ Error al agregar actividad', 'bg-red-600');
                    }
                })
                .catch(err => {
                    console.error(err);
                    showToast('❌ Error al agregar actividad', 'bg-red-600');
                });
        });

        // Agregar nuevo compromiso
        document.getElementById('btn-guardar').addEventListener('click', function() {
            const descripcion = document.getElementById('descripcion').value.trim();
            const responsable = document.getElementById('responsable').value.trim();
            const fecha = document.getElementById('fecha').value;
            const resultado = document.getElementById('resultado').value.trim();

            if (!descripcion || !responsable || !fecha) {
                showToast('⚠️ Completa todos los campos obligatorios', 'bg-yellow-600');
                return;
            }

            fetch(`/reuniones/{{ $reunion->id }}/compromisos`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        descripcion: descripcion,
                        responsable: responsable,
                        fecha: fecha,
                        resultado: resultado
                    })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.ok) {
                        showToast('✅ Compromiso agregado', 'bg-green-600');

                        // Limpiar campos
                        document.getElementById('descripcion').value = '';
                        document.getElementById('responsable').value = '';
                        document.getElementById('fecha').value = '';
                        document.getElementById('resultado').value = '';

                        // Recargar después de 1 segundo
                        setTimeout(() => location.reload(), 1000);
                    } else {
                        showToast('❌ Error al agregar compromiso', 'bg-red-600');
                    }
                })
                .catch(err => {
                    console.error(err);
                    showToast('❌ Error al agregar compromiso', 'bg-red-600');
                });
        });

        // ======================= FUNCIONES PARA ELIMINAR =======================

        function eliminarActividad(id) {
            if (!confirm('¿Estás seguro de eliminar esta actividad?')) {
                return;
            }

            fetch(`/actas/actividades/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                })
                .then(res => res.json())
                .then(data => {
                    if (data.ok) {
                        showToast('✅ Actividad eliminada', 'bg-green-600');
                        document.getElementById(`actividad-${id}`).remove();
                    } else {
                        showToast('❌ Error al eliminar actividad', 'bg-red-600');
                    }
                })
                .catch(err => {
                    console.error(err);
                    showToast('❌ Error al eliminar actividad', 'bg-red-600');
                });
        }

        function eliminarCompromiso(id) {
            if (!confirm('¿Estás seguro de eliminar este compromiso?')) {
                return;
            }

            fetch(`/compromisos/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                })
                .then(res => res.json())
                .then(data => {
                    if (data.ok) {
                        showToast('✅ Compromiso eliminado', 'bg-green-600');
                        document.getElementById(`compromiso-${id}`).remove();
                    } else {
                        showToast('❌ Error al eliminar compromiso', 'bg-red-600');
                    }
                })
                .catch(err => {
                    console.error(err);
                    showToast('❌ Error al eliminar compromiso', 'bg-red-600');
                });
        }
    </script>
@endsection
