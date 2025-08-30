@extends('layouts.app')

@section('titulo')
    Videollamada — {{ $reunion->titulo }}
@endsection

@section('content')
<div class="max-w-6xl mx-auto mt-6 px-4">
    {{-- Barra superior: título + botón volver a detalles del historial o a la reunión --}}
    <div class="mb-4 flex items-center justify-between">
        <h2 class="text-2xl font-bold text-gray-800">
            {{ $reunion->titulo }}
        </h2>

        {{-- Ajusta a donde quieras volver: historial o detalles de la reunión --}}
        <div class="flex gap-2">
            <a href="{{ route('reuniones.detalle_historial', $reunion->id) }}"
               class="inline-flex items-center px-4 py-2 bg-gray-200 text-gray-700 rounded-xl hover:bg-gray-300 transition">
                ← Volver al historial
            </a>
            <a href="{{ route('reuniones.show', $reunion->id) }}"
               class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition">
                Ver detalles
            </a>
        </div>
    </div>

    {{-- Contenedor unificado (Jitsi + panel lateral para transcripción/notas) --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Jitsi --}}
        <div class="lg:col-span-2 bg-white shadow-md rounded-xl p-2">
            <div id="jitsi-container" class="w-full" style="height: 70vh;"></div>
        </div>

        {{-- Panel derecho (ganchos para tu transcripción) --}}
        <div class="bg-white shadow-md rounded-xl p-4 space-y-4">
            <h3 class="text-lg font-semibold text-gray-800">Acta / Notas de la reunión</h3>

            {{-- Área de texto para MVP (puedes llenarlo con tu transcriptor luego) --}}
            <textarea id="notes"
                      class="w-full border border-gray-200 rounded-lg p-3 min-h-[280px]"
                      placeholder="Aquí aparecerá la transcripción o puedes tomar notas manualmente..."></textarea>

            {{-- Botones MVP: guardar notas (luego lo integrarás con PDF/plantilla) --}}
            <div class="flex items-center justify-end gap-2">
                <button id="btn-copy"
                        class="px-4 py-2 bg-gray-100 rounded-lg text-gray-700 hover:bg-gray-200 transition">
                    Copiar
                </button>
                {{-- <form action="{{ route('reuniones.transcripciones.store', $reunion->id) }}" method="POST" id="form-save">
                    @csrf
                    <input type="hidden" name="contenido" id="contenido">
                    <button type="submit"
                            class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
                        Guardar acta
                    </button>
                </form> --}}
            </div>

            <p class="text-xs text-gray-400">
                * Próximo paso: conectar este panel con tu servicio de transcripción automática.
            </p>
        </div>
    </div>
</div>

{{-- Script de Jitsi --}}
<script src="https://meet.jit.si/external_api.js"></script>
<script>
    (function () {
        const domain = "meet.jit.si"; // Para producción privada: tu propio servidor o JaaS
        const options = {
            roomName: @json($roomName),
            width: "100%",
            height: "100%",
            parentNode: document.getElementById("jitsi-container"),
            userInfo: { displayName: @json($userName) },
            configOverwrite: {
                prejoinPageEnabled: true,
                disableDeepLinking: true,
            },
            interfaceConfigOverwrite: {
                // Interfaz limpia
                SHOW_BRAND_WATERMARK: false,
                HIDE_DEEP_LINKING_LOGO: true,
            }
        };

        const api = new JitsiMeetExternalAPI(domain, options);

        // Ejemplos de eventos útiles (para métricas futuras):
        api.addListener('videoConferenceJoined', () => {
            // Aquí puedes marcar "inicio de reunión" para tu analytics
            console.log('joined');
        });

        api.addListener('participantJoined', (e) => {
            // e.id, e.displayName
            console.log('participant joined', e);
        });

        // --- MVP para notas ---
        const notes = document.getElementById('notes');
        document.getElementById('btn-copy').addEventListener('click', () => {
            notes.select();
            document.execCommand('copy');
        });

        // Guardar acta (manda el texto al backend)
        const formSave = document.getElementById('form-save');
        formSave.addEventListener('submit', () => {
            document.getElementById('contenido').value = notes.value;
        });

        // GANCHOS FUTUROS:
        // 1) Si usas Web Speech API (solo Chrome) para MVP:
        //    - Reconoce en el cliente y escribe en #notes.
        // 2) Si usas STT externo (Whisper/Google/etc.):
        //    - Captura audio con MediaRecorder y envía chunks a tu backend (WebSocket o fetch).
        //    - Tu backend llama al STT y devuelve texto para rellenar #notes.
    })();
</script>
@endsection
