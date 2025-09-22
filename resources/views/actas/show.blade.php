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
                        <td class="p-2 border" contenteditable="true" data-field="nombre">{{ $actividad->nombre }}</td>
                        <td class="p-2 border" contenteditable="true" data-field="responsable">{{ $actividad->responsable }}</td>
                        <td class="p-2 border" contenteditable="true" data-field="fecha_entrega">{{ \Carbon\Carbon::parse($actividad->fecha_entrega)->format('Y-m-d') }}</td>
                        <td class="p-2 border" contenteditable="true" data-field="descripcion">{{ $actividad->descripcion }}</td>
                        <td class="p-2 border text-center">
                            <button onclick="eliminarActividad({{ $actividad->id }})"
                                    class="bg-red-500 text-white px-2 py-1 rounded cursor-pointer">❌</button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Botón actualizar actividades -->
        <button onclick="actualizarActividades()" 
                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition mb-4 cursor-pointer">
            🔄 Actualizar Actividades
        </button>

        <!-- Formulario AJAX para actividades nuevas -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-2 mb-3">
            <input type="text" id="nombre" placeholder="Nombre" class="border p-2 rounded">
            <input type="text" id="responsableAct" placeholder="Responsable" class="border p-2 rounded">
            <input type="date" id="fecha_entrega" class="border p-2 rounded">
            <input type="text" id="descripcionAct" placeholder="Descripción" class="border p-2 rounded">
        </div>

        <button id="btn-guardar-actividad"
                class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition cursor-pointer">
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
                        <td class="p-2 border" contenteditable="true" data-field="descripcion">{{ $compromiso->descripcion }}</td>
                        <td class="p-2 border" contenteditable="true" data-field="responsable">{{ $compromiso->responsable }}</td>
                        <td class="p-2 border" contenteditable="true" data-field="fecha">{{ \Carbon\Carbon::parse($compromiso->fecha)->format('Y-m-d') }}</td>
                        <td class="p-2 border" contenteditable="true" data-field="resultado">{{ $compromiso->resultado }}</td>
                        <td class="p-2 border text-center">
                            <button onclick="eliminarCompromiso({{ $compromiso->id }})"
                                    class="bg-red-500 text-white px-2 py-1 rounded cursor-pointer">❌</button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Botón actualizar compromisos -->
        <button onclick="actualizarCompromisos()" 
                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition mb-4 cursor-pointer">
            🔄 Actualizar Compromisos
        </button>

        <!-- Formulario AJAX para compromisos nuevos -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-2 mb-3">
            <input type="text" id="descripcion" placeholder="Compromiso" class="border p-2 rounded">
            <input type="text" id="responsable" placeholder="Responsable" class="border p-2 rounded">
            <input type="date" id="fecha" class="border p-2 rounded">
            <input type="text" id="resultado" placeholder="Resultado esperado" class="border p-2 rounded">
        </div>

        <button id="btn-guardar"
                class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition cursor-pointer">
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

    <!-- Notificación flotante -->
    <div id="toast" 
        class="hidden fixed bottom-4 right-4 bg-green-600 text-white px-4 py-2 rounded-lg shadow-lg transition-opacity duration-500">
    </div>
</div>

<!-- Script AJAX -->
<script>
const reunionId = {{ $reunion->id }};

// ======================= ACTIVIDADES =========================

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
                showToast(`✅ Actividad ${id} actualizada`);
            } else {
                showToast(`⚠️ Error al actualizar actividad ${id}`, 'bg-red-600');
            }
        });
    });
}

// ======================= COMPROMISOS =========================

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
                showToast(`✅ Compromiso ${id} actualizado`);
            } else {
                showToast(`⚠️ Error al actualizar compromiso ${id}`, 'bg-red-600');
            }
        });
    });
}

// Mostrar notificación tipo "toast"
function showToast(message, color = 'bg-green-600') {
    const toast = document.getElementById('toast');
    toast.innerText = message;
    toast.className = `fixed bottom-4 right-4 text-white px-4 py-2 rounded-lg shadow-lg transition-opacity duration-500 ${color}`;
    toast.classList.remove('hidden');
    toast.style.opacity = '1';

    setTimeout(() => {
        toast.style.opacity = '0';
        setTimeout(() => toast.classList.add('hidden'), 500);
    }, 2000); // Se oculta en 2 segundos
}


</script>
@endsection
