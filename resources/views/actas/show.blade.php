@extends('layouts.app')

@section('titulo')
    Acta de la Reunión — {{ $reunion->titulo }}
@endsection

@section('content')
<div class="max-w-4xl mx-auto mt-8 px-4">
    <!-- Botón de regreso -->
    <div class="mb-6">
        <a href="{{ route('reuniones.show', $reunion->id) }}"
           class="inline-flex items-center text-indigo-600 hover:text-indigo-800 font-medium transition">
            ← Volver a la reunión
        </a>
    </div>

    <!-- Detalles del Acta -->
    <div class="bg-white shadow-md rounded-lg p-6 space-y-4">
        <h2 class="text-2xl font-bold text-indigo-700 mb-4">📑 Acta de la reunión</h2>

        <!-- Información básica de la reunión -->
        <p><strong>Título:</strong> {{ $reunion->titulo }}</p>
        <p><strong>Descripción:</strong> {{ $reunion->descripcion }}</p>
        <p><strong>Fecha de reunión:</strong> {{ \Carbon\Carbon::parse($reunion->fecha_hora)->format('d/m/Y H:i') }}</p>
        <p><strong>Estado:</strong>
            <span class="px-2 py-1 rounded-lg text-white 
                {{ $acta->estado === 'final' ? 'bg-green-600' : 'bg-yellow-500' }}">
                {{ ucfirst($acta->estado) }}
            </span>
        </p>

        <hr class="my-4">

<!-- Tabla de actividades -->
<h3 class="text-lg font-semibold text-gray-800 mb-2">📝 Actividades</h3>

<table class="w-full border border-gray-300 rounded-lg mb-4">
    <thead>
        <tr class="bg-gray-100">
            <th class="p-2 border">Nombre</th>
            <th class="p-2 border">Responsable</th>
            <th class="p-2 border">Fecha entrega</th>
            <th class="p-2 border">Descripción</th>
            <th class="p-2 border text-center">Acciones</th>
        </tr>
    </thead>
    <tbody id="actividades-table">
        @foreach($reunion->actividades as $actividad)
            <tr id="actividad-{{ $actividad->id }}">
                <td class="p-2 border">{{ $actividad->nombre }}</td>
                <td class="p-2 border">{{ $actividad->responsable }}</td>
                <td class="p-2 border">{{ \Carbon\Carbon::parse($actividad->fecha_entrega)->format('d/m/Y') }}</td>
                <td class="p-2 border">{{ $actividad->descripcion }}</td>
                <td class="p-2 border text-center">
                    <button onclick="eliminarActividad({{ $actividad->id }})"
                            class="bg-red-500 text-white px-2 py-1 rounded cursor-pointer">❌</button>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

<!-- Formulario AJAX para actividades -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-2 mb-3">
    <input type="text" id="nombre" placeholder="Nombre" class="border p-2 rounded">
    <input type="text" id="responsableAct" placeholder="Responsable" class="border p-2 rounded">
    <input type="date" id="fecha_entrega" class="border p-2 rounded">
    <input type="text" id="descripcionAct" placeholder="Descripción" class="border p-2 rounded">
</div>

<button id="btn-guardar-actividad"
        class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
    ➕ Guardar Actividad
</button>


        <hr class="my-4">

        <!-- Tabla de compromisos -->
        <h3 class="text-lg font-semibold text-gray-800 mb-2">📌 Compromisos</h3>

        <table class="w-full border border-gray-300 rounded-lg mb-4">
            <thead>
                <tr class="bg-gray-100">
                    <th class="p-2 border">Compromiso</th>
                    <th class="p-2 border">Responsable</th>
                    <th class="p-2 border">Fecha</th>
                    <th class="p-2 border">Resultado esperado</th>
                    <th class="p-2 border text-center">Acciones</th>
                </tr>
            </thead>
            <tbody id="compromisos-table">
                @foreach($reunion->compromisos as $compromiso)
                    <tr id="compromiso-{{ $compromiso->id }}">
                        <td class="p-2 border">{{ $compromiso->descripcion }}</td>
                        <td class="p-2 border">{{ $compromiso->responsable }}</td>
                        <td class="p-2 border">{{ \Carbon\Carbon::parse($compromiso->fecha)->format('d/m/Y') }}</td>
                        <td class="p-2 border">{{ $compromiso->resultado }}</td>
                        <td class="p-2 border text-center">
                            <button onclick="eliminarCompromiso({{ $compromiso->id }})"
                                    class="bg-red-500 text-white px-2 py-1 rounded cursor-pointer">
                                ❌
                            </button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Formulario AJAX -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-2 mb-3">
            <input type="text" id="descripcion" placeholder="Compromiso" class="border p-2 rounded">
            <input type="text" id="responsable" placeholder="Responsable" class="border p-2 rounded">
            <input type="date" id="fecha" class="border p-2 rounded">
            <input type="text" id="resultado" placeholder="Resultado esperado" class="border p-2 rounded">
        </div>

        <button id="btn-guardar"
                class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
            ➕ Guardar Compromiso
        </button>

        <hr class="my-4">

        <!-- Botones de descarga -->
        <div class="flex gap-4">
            @if($acta->archivo_pdf)
                <a href="{{ route('actas.pdf', $acta) }}"
                    target="_blank"
                    class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                    📄 Descargar PDF
                </a>
            @endif

            @if($acta->archivo_docx)
                <a href="{{ asset('storage/'.$acta->archivo_docx) }}"
                   target="_blank"
                   class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    📘 Descargar Word
                </a>
            @endif
        </div>
    </div>
</div>

<!-- Script AJAX -->
<script>
const reunionId = {{ $reunion->id }};

// Guardar compromiso
document.getElementById('btn-guardar').addEventListener('click', () => {
    const descripcion = document.getElementById('descripcion').value;
    const responsable = document.getElementById('responsable').value;
    const fecha = document.getElementById('fecha').value;
    const resultado = document.getElementById('resultado').value;

    if (!descripcion || !responsable || !fecha) {
        alert("Por favor complete los campos obligatorios");
        return;
    }

    fetch(`/reuniones/${reunionId}/compromisos`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ descripcion, responsable, fecha, resultado })
    })
    .then(res => res.json())
    .then(data => {
        if (data.ok) {
            const comp = data.compromiso;
            const row = `
                <tr id="compromiso-${comp.id}">
                    <td class="p-2 border">${comp.descripcion}</td>
                    <td class="p-2 border">${comp.responsable}</td>
                    <td class="p-2 border">${comp.fecha}</td>
                    <td class="p-2 border">${comp.resultado ?? ''}</td>
                    <td class="p-2 border text-center">
                        <button onclick="eliminarCompromiso(${comp.id})"
                                class="bg-red-500 text-white px-2 py-1 rounded">❌</button>
                    </td>
                </tr>`;
            document.getElementById('compromisos-table').insertAdjacentHTML('beforeend', row);

            // limpiar inputs
            document.getElementById('descripcion').value = '';
            document.getElementById('responsable').value = '';
            document.getElementById('fecha').value = '';
            document.getElementById('resultado').value = '';
        }
    });
});

// Eliminar compromiso
function eliminarCompromiso(id) {
    if (!confirm("¿Seguro que deseas eliminar este compromiso?")) return;

    fetch(`/compromisos/${id}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(res => res.json())
    .then(data => {
        if (data.ok) {
            document.getElementById(`compromiso-${id}`).remove();
        }
    });
}
</script>
@endsection
