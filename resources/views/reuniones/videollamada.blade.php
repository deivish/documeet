@extends('layouts.app')

@section('titulo')
    Videollamada — {{ $reunion->titulo }}
@endsection

@section('content')
<div class="max-w-6xl mx-auto mt-6 px-4">
    {{-- Barra superior: título + botón volver --}}
    <div class="mb-4 flex items-center justify-between">
        <h2 class="text-2xl font-bold text-gray-800">
            {{ $reunion->titulo }}
        </h2>

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

    {{-- Contenedor unificado (Jitsi + panel lateral) --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Jitsi --}}
        <div class="lg:col-span-2 bg-white shadow-md rounded-xl p-2">
            <div id="jitsi-container" class="w-full" style="height: 70vh;"></div>
        </div>

        {{-- Panel derecho --}}
        <div class="bg-white shadow-md rounded-xl p-4 space-y-4">
            <h3 class="text-lg font-semibold text-gray-800">Acta / Notas de la reunión</h3>

            <textarea id="notes"
                    class="w-full border border-gray-200 rounded-lg p-3 min-h-[280px]"
                    placeholder="Aquí aparecerá la transcripción o puedes tomar notas manualmente..."></textarea>

            @if ($reunion->user_id === Auth::id())
        <div class="flex flex-col gap-2 mt-3">
        
        <button type="button"class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition"onclick="startRecording()">
    🎙️ Iniciar Grabación
        </button>

        <button type="button"
            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition"
            onclick="stopRecording()">
            ⏹️ Detener y Enviar
        </button>

        
        {{-- Botón Guardar notas (borrador vía fetch) --}}
        <button id="btn-guardar"
            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
            Guardar notas
        </button>

        {{-- Botón Ver Acta (finaliza y redirige a actas.show) --}}
        @if ($reunion->acta && $reunion->acta->estado !== 'final')
            <form action="{{ route('actas.finalizar', $reunion->acta->id) }}" method="POST">
                @csrf
                <button type="submit"
                        class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition">
                    Ver Acta
                </button>
            </form>
        @elseif($reunion->acta && $reunion->acta->estado === 'final')
            <a href="{{ route('actas.show', $reunion->acta->id) }}"
               class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition text-center">
                Ver Acta
            </a>
        @endif

    </div>

@endif

            <p class="text-xs text-gray-400">
                * Las notas se guardan automáticamente cada 12 segundos.
            </p>
        </div>

    </div>
</div>

<script src="https://meet.jit.si/external_api.js"></script>
<script>
(function () {
    const domain = "meet.jit.si";
    const options = {
        roomName: @json($roomName),
        width: "100%",
        height: "100%",
        parentNode: document.getElementById("jitsi-container"),
        userInfo: { displayName: @json($userName) },
        configOverwrite: { prejoinPageEnabled: true, disableDeepLinking: true },
        interfaceConfigOverwrite: { SHOW_BRAND_WATERMARK: false, HIDE_DEEP_LINKING_LOGO: true }
    };

    const api = new JitsiMeetExternalAPI(domain, options);

    // --- Asistencia ---
    api.addListener('videoConferenceJoined', () => {
        fetch("{{ route('reuniones.asistencia.entrada', $reunion->id) }}", {
            method: 'POST',
            headers: {
                'Content-Type':'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({})
        });
    });

    window.addEventListener('beforeunload', function() {
        navigator.sendBeacon("{{ route('reuniones.asistencia.salida', $reunion->id) }}", '');
    });

    // --- Notas / Acta ---
    const notes = document.getElementById('notes');
    const btnGuardar = document.getElementById('btn-guardar');

    // Guardar manual
    if (btnGuardar) {
        btnGuardar.addEventListener('click', () => {
            const contenido = notes.value.trim();
            if (!contenido) {
                alert("No hay notas para guardar");
                return;
            }
            fetch("{{ route('actas.store', $reunion->id) }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ contenido })
            })
            .then(res => res.json())
            .then(data => {
                console.log("Notas guardadas", data);
                alert("Notas guardadas como borrador");
            })
            .catch(err => console.error("Error al guardar notas:", err));
        });
    }

    // Autoguardado cada 12s
    setInterval(()=> {
        const contenido = notes.value.trim();
        if(!contenido) return;
        fetch("{{ route('reuniones.transcripciones.store', $reunion->id) }}", {
            method:'POST',
            headers:{
                'Content-Type':'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ contenido, fuente: 'stt' })
        })
        .then(res => res.json())
        .then(json => console.log('Autoguardado:', json))
        .catch(err => console.error("Error en autoguardado:", err));
    }, 12000);

})();
</script>
<script>
let recorder, audioChunks = [];

async function startRecording() {
    // Pide permiso para capturar el audio del micrófono / pestaña
    const stream = await navigator.mediaDevices.getUserMedia({ audio: true, video: false });

    recorder = new MediaRecorder(stream);
    recorder.ondataavailable = e => audioChunks.push(e.data);
    recorder.onstop = async () => {
        const blob = new Blob(audioChunks, { type: 'audio/webm' });
        const formData = new FormData();
        formData.append('audio', blob);

        await fetch("{{ route('audio.store', $reunion->id) }}", {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: formData
        });

        audioChunks = [];
    };

    recorder.start();
    alert('Grabación iniciada');
}

function stopRecording() {
    recorder.stop();
    alert('Grabación detenida. Enviando al servidor...');
}
</script>



@endsection
