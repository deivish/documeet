@extends('layouts.app')

@section('titulo')
    {{ isset($reunion) ? 'Editar Reunión' : 'Crear Reunión' }}
@endsection

@section('content')
<div class="max-w-3xl mx-auto bg-white p-6 rounded shadow-md">
    <form action="{{ isset($reunion) ? route('reuniones.update', $reunion->id) : route('reuniones.store') }}" method="POST">
        @csrf
        @if(isset($reunion))
            @method('PUT')
        @endif

        {{-- Datos de la reunión --}}
        <div class="mb-4">
            <label for="titulo" class="block text-gray-700 font-bold mb-2">Título</label>
            <input type="text" name="titulo" id="titulo"
                   value="{{ old('titulo', $reunion->titulo ?? '') }}"
                   class="w-full border border-gray-300 p-2 rounded" required>
        </div>

        <div class="mb-4">
            <label for="descripcion" class="block text-gray-700 font-bold mb-2">Descripción</label>
            <textarea name="descripcion" id="descripcion" rows="4"
                      class="w-full border border-gray-300 p-2 rounded"
                      required>{{ old('descripcion', $reunion->descripcion ?? '') }}</textarea>
        </div>

        <div class="mb-4">
            <label for="fecha_hora" class="block text-gray-700 font-bold mb-2">Fecha</label>
            <input type="datetime-local" name="fecha_hora" id="fecha_hora"
                   value="{{ old('fecha_hora', isset($reunion) ? \Carbon\Carbon::parse($reunion->fecha_hora)->format('Y-m-d\TH:i') : '') }}"
                   class="w-full border border-gray-300 p-2 rounded" required>
        </div>

        {{-- Actividades --}}
        <div class="mb-6">
            <label class="block text-gray-700 font-bold mb-2">Actividades</label>
            <div id="actividades-container">
    @php $actividadIndex = 0; @endphp
    @if(isset($reunion) && $reunion->actividades)
        @foreach($reunion->actividades as $actividad)
            <div class="actividad mb-4 border border-gray-200 p-4 rounded relative">
                <button type="button" class="absolute top-2 right-2 text-red-500 hover:text-red-700 cursor-pointer z-10 bg-white rounded-full px-1"  onclick="eliminarActividad(this)">✖</button>

                <input type="hidden" name="actividades[{{ $actividadIndex }}][id]" value="{{ $actividad->id }}">

                <input type="text" name="actividades[{{ $actividadIndex }}][nombre]" value="{{ $actividad->nombre }}"
                       class="w-full border border-gray-300 p-2 rounded mb-2" placeholder="Nombre" required>

                <input type="text" name="actividades[{{ $actividadIndex }}][responsable]" value="{{ $actividad->responsable }}"
                       class="w-full border border-gray-300 p-2 rounded mb-2" placeholder="Responsable" required>

                <input type="date" name="actividades[{{ $actividadIndex }}][fecha_entrega]" value="{{ $actividad->fecha_entrega }}"
                       class="w-full border border-gray-300 p-2 rounded mb-2" placeholder="Fecha de entrega" required>

                <textarea name="actividades[{{ $actividadIndex }}][descripcion]" class="w-full border border-gray-300 p-2 rounded" required>{{ $actividad->descripcion }}</textarea>
            </div>
            @php $actividadIndex++; @endphp
        @endforeach
    @endif

    {{-- Plantilla vacía por defecto si no hay reunión --}}
    @if(!isset($reunion))
        <div class="actividad mb-4 border border-gray-200 p-4 rounded relative">
            <button type="button" class="absolute top-2 right-2 text-red-500" onclick="eliminarActividad(this)">✖</button>

            <input type="text" name="actividades[0][nombre]" placeholder="Nombre de la actividad"
                   class="w-full border border-gray-300 p-2 rounded mb-2" required>

            <input type="text" name="actividades[0][responsable]" placeholder="Responsable"
                   class="w-full border border-gray-300 p-2 rounded mb-2" required>

            <input type="date" name="actividades[0][fecha_entrega]" placeholder="Fecha de entrega"
                   class="w-full border border-gray-300 p-2 rounded mb-2" required>

            <textarea name="actividades[0][descripcion]" rows="2" placeholder="Descripción"
                      class="w-full border border-gray-300 p-2 rounded" required></textarea>
        </div>
    @endif
</div>

{{-- campo oculto para actividades eliminadas --}}
<div id="actividades_eliminar_container"></div>


            <button type="button" onclick="agregarActividad()" style="cursor: pointer;" class="mt-2 bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                + Añadir otra actividad
            </button>


        {{-- Botón de crear reunión --}}
        <div class="flex justify-center mt-8">
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-10 rounded text-lg shadow-md">
                {{ isset($reunion) ? 'Actualizar' : 'Crear' }} Reunión
            </button>
        </div>
    </form>
</div>

{{-- JS para añadir/eliminar actividades --}}
<script>
    let actividadIndex = {{ isset($reunion) ? count($reunion->actividades) : 1 }};

    function agregarActividad() {
        const container = document.getElementById('actividades-container');

        const nuevaActividad = document.createElement('div');
        nuevaActividad.classList.add('actividad', 'mb-4', 'border', 'border-gray-200', 'p-4', 'rounded', 'relative');
        nuevaActividad.innerHTML = `
            <button type="button" class="absolute top-2 right-2 text-red-500 cursor-pointer hover:text-red-700 z-10 bg-white rounded-full px-1" onclick="eliminarActividad(this)">✖</button>

            <input type="text" name="actividades[${actividadIndex}][nombre]" placeholder="Nombre de la actividad"
                   class="w-full border border-gray-300 p-2 rounded mb-2" required>

            <input type="text" name="actividades[${actividadIndex}][responsable]" placeholder="Responsable"
                   class="w-full border border-gray-300 p-2 rounded mb-2" required>

            <input type="date" name="actividades[${actividadIndex}][fecha_entrega]" placeholder="Fecha de entrega"
                   class="w-full border border-gray-300 p-2 rounded mb-2" required>

            <textarea name="actividades[${actividadIndex}][descripcion]" rows="2" placeholder="Descripción"
                      class="w-full border border-gray-300 p-2 rounded" required></textarea>
        `;
        container.appendChild(nuevaActividad);
        actividadIndex++;
    }

    function eliminarActividad(button) {
        const actividadDiv = button.parentElement;
    const idInput = actividadDiv.querySelector('input[name*="[id]"]');

    if (idInput) {
        const id = idInput.value;

        // Agregar un nuevo input oculto al contenedor
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
