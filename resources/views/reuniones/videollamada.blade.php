@extends('layouts.app')

@section('titulo')
    Videollamada — {{ $reunion->titulo }}
@endsection

@section('content')

{{-- Ocultar nav/footer del layout para experiencia inmersiva --}}
<style>
    nav, footer { display: none !important; }
    body { overflow: hidden; }
    #call-container iframe { width: 100% !important; height: 100% !important; border: 0 !important; }
</style>

<div class="fixed inset-0 bg-gray-950 flex flex-col" style="z-index:40;">

    {{-- ══════════════ TOPBAR ══════════════ --}}
    <div class="flex items-center justify-between px-5 py-3 bg-gray-900/95 backdrop-blur border-b border-gray-800 flex-shrink-0" style="z-index:50;">

        {{-- Logo + título reunión --}}
        <div class="flex items-center gap-3 min-w-0 flex-1">
            <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center flex-shrink-0">
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                </svg>
            </div>
            <div class="min-w-0">
                <p class="text-white font-semibold text-sm truncate leading-tight">{{ $reunion->titulo }}</p>
                <p class="text-gray-400 text-xs">{{ \Carbon\Carbon::parse($reunion->fecha_hora)->format('d/m/Y · H:i') }}</p>
            </div>
        </div>

        {{-- Centro: badge transcribiendo + timer --}}
        <div class="flex items-center gap-4">
            <div id="rec-badge" class="hidden items-center gap-2 bg-red-950/80 border border-red-700/60 px-3 py-1.5 rounded-full">
                <span class="w-2 h-2 bg-red-500 rounded-full animate-pulse"></span>
                <span class="text-red-300 text-xs font-semibold">Transcribiendo</span>
            </div>
            <span class="text-gray-500 text-xs font-mono hidden sm:block" id="duration-timer">00:00</span>
        </div>

        {{-- Acciones derecha --}}
        <div class="flex items-center gap-2 ml-4">
            <button id="copy-link-btn"
                class="flex items-center gap-1.5 px-3 py-1.5 bg-gray-700 hover:bg-gray-600 text-gray-200 text-xs font-medium rounded-lg transition-all">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                </svg>
                <span class="hidden sm:inline">Copiar link</span>
            </button>

            <button id="fullscreen-btn"
                class="flex items-center gap-1.5 px-3 py-1.5 bg-gray-700 hover:bg-gray-600 text-gray-200 text-xs font-medium rounded-lg transition-all">
                <svg class="w-3.5 h-3.5" id="fs-icon-expand" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/>
                </svg>
                <svg class="w-3.5 h-3.5 hidden" id="fs-icon-compress" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 9V4.5M9 9H4.5M9 9L3.75 3.75M9 15v4.5M9 15H4.5M9 15l-5.25 5.25M15 9h4.5M15 9V4.5M15 9l5.25-5.25M15 15h4.5M15 15v4.5m0-4.5l5.25 5.25"/>
                </svg>
                <span class="hidden sm:inline">Pantalla completa</span>
            </button>

            <a href="{{ route('reuniones.show', $reunion->id) }}"
               class="flex items-center gap-1.5 px-3 py-1.5 bg-gray-700 hover:bg-gray-600 text-gray-200 text-xs font-medium rounded-lg transition-all">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                <span class="hidden sm:inline">Salir</span>
            </a>
        </div>
    </div>

    {{-- ══════════════ VIDEO (100% del espacio) ══════════════ --}}
    <div class="flex-1 relative overflow-hidden" id="video-wrapper">

        {{-- Daily.co se monta aquí --}}
        <div id="call-container" class="absolute inset-0">
            <div id="video-loader" class="absolute inset-0 flex items-center justify-center bg-gray-950 z-10">
                <div class="text-center">
                    <div class="w-14 h-14 border-4 border-indigo-500 border-t-transparent rounded-full animate-spin mx-auto mb-4"></div>
                    <p class="text-white font-medium text-sm">Conectando a la videollamada...</p>
                    <p class="text-gray-500 text-xs mt-1">Espera un momento</p>
                </div>
            </div>
        </div>

        {{-- ══════ PANTALLA POST-LLAMADA ══════ --}}
        <div id="post-call-screen" class="absolute inset-0 bg-gray-950 flex items-center justify-center hidden z-20">
            <div class="text-center max-w-sm px-6">
                <div class="w-20 h-20 bg-green-500/20 rounded-full flex items-center justify-center mx-auto mb-6 border-2 border-green-500/40">
                    <svg class="w-10 h-10 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <h2 class="text-white text-2xl font-bold mb-2">Reunión finalizada</h2>
                <p class="text-gray-400 text-sm mb-8">La transcripción fue guardada automáticamente.</p>

                <div class="flex flex-col gap-3">
                    @if ($reunion->user_id === Auth::id())
                    <div id="acta-action-container">
                        @if ($reunion->acta)
                            <a href="{{ route('actas.show', $reunion->acta->id) }}"
                               class="w-full flex items-center justify-center gap-2 px-6 py-3.5 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-xl transition-all shadow-lg">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                Ver Acta Generada
                            </a>
                        @else
                            <div id="acta-btn-placeholder"
                                 class="w-full px-6 py-3.5 bg-gray-800 text-gray-500 font-medium rounded-xl text-sm text-center">
                                Guardando transcripción...
                            </div>
                        @endif
                    </div>
                    @endif

                    <a href="{{ route('reuniones.show', $reunion->id) }}"
                       class="w-full flex items-center justify-center gap-2 px-6 py-3.5 bg-gray-800 hover:bg-gray-700 text-gray-300 font-medium rounded-xl transition-all">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                        Volver a la reunión
                    </a>

                    <a href="{{ route('reuniones.index') }}"
                       class="w-full flex items-center justify-center gap-2 px-6 py-3 bg-gray-800 hover:bg-gray-700 text-gray-500 text-sm font-medium rounded-xl transition-all">
                        Ir a Mis Reuniones
                    </a>
                </div>
            </div>
        </div>

    </div>

    {{-- ══════════════ BOTTOMBAR (solo moderador) ══════════════ --}}
    @if ($reunion->user_id === Auth::id())
    <div id="moderator-bar"
         class="flex-shrink-0 bg-gray-900/95 backdrop-blur border-t border-gray-800 px-5 py-3 hidden" style="z-index:50;">
        <div class="max-w-4xl mx-auto flex items-center justify-between gap-4 flex-wrap">

            <div class="flex items-center gap-3">
                <div class="w-8 h-8 bg-indigo-500/20 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-white text-xs font-semibold">Modo moderador</p>
                    <p class="text-gray-500 text-xs">Transcripción en segundo plano</p>
                </div>
            </div>

            <div class="flex items-center gap-3">
                {{-- Iniciar transcripción manual (por si falla el auto) --}}
                <button id="manual-start-btn"
                    class="flex items-center gap-2 px-4 py-2 bg-green-700 hover:bg-green-600 text-white text-xs font-medium rounded-lg transition-all">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Iniciar transcripción
                </button>

                <button id="manual-stop-btn" disabled
                    class="flex items-center gap-2 px-4 py-2 bg-gray-700 hover:bg-gray-600 text-gray-400 text-xs font-medium rounded-lg transition-all disabled:opacity-40 disabled:cursor-not-allowed">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 10a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1v-4z"/>
                    </svg>
                    Detener
                </button>

                {{-- Separador --}}
                <div class="w-px h-6 bg-gray-700"></div>

                {{-- Finalizar reunión --}}
                @if ($reunion->acta && $reunion->acta->estado === 'final')
                    <a href="{{ route('actas.show', $reunion->acta->id) }}"
                       class="flex items-center gap-2 px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold rounded-lg transition-all shadow-lg">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Ver Acta Final
                    </a>
                @else
                    <button id="btn-finalizar-reunion"
                        class="flex items-center gap-2 px-5 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-bold rounded-lg transition-all shadow-lg">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 10a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1v-4z"/>
                        </svg>
                        Finalizar reunión
                    </button>
                @endif
            </div>
        </div>
    </div>
    @endif

    {{-- Toast --}}
    <div id="toast" class="fixed bottom-24 left-1/2 -translate-x-1/2 hidden z-50 px-5 py-3 rounded-xl text-white text-sm font-medium shadow-2xl transition-all duration-300"></div>

</div>

{{-- Daily.co SDK --}}
<script src="https://unpkg.com/@daily-co/daily-js"></script>

<script>
(function () {
    const REUNION_ID   = {{ $reunion->id }};
    const ES_MODERADOR = {{ $reunion->user_id === Auth::id() ? 'true' : 'false' }};
    const DAILY_URL    = "{{ $dailyUrl }}";
    const CSRF         = document.querySelector('meta[name="csrf-token"]').content;

    // ── Estado transcripción ──
    let mediaRecorder        = null;
    let audioChunks          = [];
    let transcripcionTotal   = '';
    let isRecording          = false;
    let intervalId           = null;

    // ── Timer ──
    let timerInterval = null;
    let segundos      = 0;

    // ── DOM ──
    const loader     = document.getElementById('video-loader');
    const recBadge   = document.getElementById('rec-badge');
    const modBar     = document.getElementById('moderator-bar');
    const postScreen = document.getElementById('post-call-screen');
    const durationEl = document.getElementById('duration-timer');

    // ════════════════════════════════
    // 1. DAILY.CO
    // ════════════════════════════════
    const callFrame = window.DailyIframe.createFrame(
        document.getElementById('call-container'), {
            url: DAILY_URL,
            showLeaveButton: true,
            showFullscreenButton: true,
            iframeStyle: { width: '100%', height: '100%', border: '0' }
        }
    );

    callFrame.join({ userName: "{{ $userName }}" }).catch(err => {
        if (loader) loader.innerHTML = `
            <div class="text-center">
                <p class="text-red-400 font-medium">Error al conectar</p>
                <p class="text-gray-500 text-sm mt-1">${err.message || 'Intenta recargar'}</p>
                <button onclick="location.reload()" class="mt-4 px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm">Reintentar</button>
            </div>`;
    });

    callFrame.on('loaded', () => {
        if (loader) loader.style.display = 'none';
    });

    callFrame.on('joined-meeting', () => {
        if (loader) loader.style.display = 'none';
        if (ES_MODERADOR && modBar) modBar.classList.remove('hidden');
        registrarEntrada();
        iniciarTimer();
    });

    callFrame.on('left-meeting', () => {
        detener();
        detenerTimer();
        registrarSalida();
        mostrarPostCall();
    });

    callFrame.on('error', () => {
        if (loader) loader.innerHTML = `
            <div class="text-center">
                <p class="text-red-400 font-medium">Error de conexión</p>
                <button onclick="location.reload()" class="mt-4 px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm">Reintentar</button>
            </div>`;
    });

    setTimeout(() => {
        if (loader && loader.style.display !== 'none') loader.style.display = 'none';
    }, 30000);

    // ════════════════════════════════
    // 2. TIMER
    // ════════════════════════════════
    function iniciarTimer() {
        timerInterval = setInterval(() => {
            segundos++;
            const m = String(Math.floor(segundos / 60)).padStart(2, '0');
            const s = String(segundos % 60).padStart(2, '0');
            if (durationEl) durationEl.textContent = `${m}:${s}`;
        }, 1000);
    }
    function detenerTimer() { clearInterval(timerInterval); }

    // ════════════════════════════════
    // 3. TRANSCRIPCIÓN (lógica original completa)
    // ════════════════════════════════
    function enviarAudio(audioBlob) {
        const formData = new FormData();
        formData.append('audio', audioBlob, 'audio.webm');

        fetch(`/reuniones/${REUNION_ID}/transcripciones/procesar-audio`, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': CSRF },
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.ok && data.texto) {
                transcripcionTotal += data.texto + ' ';
            }
        })
        .catch(() => {});
    }

    async function iniciar() {
        try {
            const displayStream = await navigator.mediaDevices.getDisplayMedia({
                audio: {
                    echoCancellation: false,
                    noiseSuppression: false,
                    autoGainControl: false,
                    sampleRate: 48000,
                    channelCount: 2
                },
                video: true
            });

            const audioTracks = displayStream.getAudioTracks();

            if (audioTracks.length === 0) {
                displayStream.getTracks().forEach(t => t.stop());
                showToast('⚠️ Debes marcar "Compartir audio" al seleccionar la pestaña', 'bg-yellow-700');
                isRecording = false;
                actualizarBotonesTranscripcion(false);
                return;
            }

            displayStream.getVideoTracks().forEach(t => t.stop());

            let micStream;
            try {
                micStream = await navigator.mediaDevices.getUserMedia({
                    audio: { echoCancellation: false, noiseSuppression: false, autoGainControl: false, sampleRate: 48000 }
                });
            } catch (_) { micStream = null; }

            let streamFinal;
            if (micStream) {
                const audioCtx  = new AudioContext();
                const dest       = audioCtx.createMediaStreamDestination();
                audioCtx.createMediaStreamSource(new MediaStream(audioTracks)).connect(dest);
                audioCtx.createMediaStreamSource(micStream).connect(dest);
                streamFinal = dest.stream;
            } else {
                streamFinal = new MediaStream(audioTracks);
            }

            iniciarGrabacion(streamFinal, audioTracks);

        } catch (error) {
            if (error.name !== 'NotAllowedError') {
                showToast('❌ No se pudo capturar el audio', 'bg-red-700');
            }
            isRecording = false;
            actualizarBotonesTranscripcion(false);
        }
    }

    function iniciarGrabacion(audioStream, tracks) {
        const mimeType = MediaRecorder.isTypeSupported('audio/webm;codecs=opus')
            ? 'audio/webm;codecs=opus' : 'audio/webm';

        mediaRecorder = new MediaRecorder(audioStream, { mimeType, audioBitsPerSecond: 128000 });

        mediaRecorder.ondataavailable = (e) => {
            if (e.data && e.data.size > 0) audioChunks.push(e.data);
        };

        mediaRecorder.onstop = () => {
            if (audioChunks.length > 0) {
                const blob = new Blob(audioChunks, { type: 'audio/webm' });
                if (blob.size > 5000) enviarAudio(blob);
                audioChunks = [];
            }
        };

        mediaRecorder.start();

        // Badge "Transcribiendo"
        if (recBadge) { recBadge.classList.remove('hidden'); recBadge.classList.add('flex'); }
        actualizarBotonesTranscripcion(true);

        intervalId = setInterval(() => {
            if (mediaRecorder && mediaRecorder.state === 'recording' && isRecording) {
                mediaRecorder.stop();
                setTimeout(() => {
                    if (isRecording && tracks[0].readyState === 'live') {
                        mediaRecorder.start();
                    } else {
                        detener();
                    }
                }, 1000);
            }
        }, 15000);
    }

    function detener() {
        isRecording = false;
        if (intervalId) { clearInterval(intervalId); intervalId = null; }
        if (mediaRecorder && mediaRecorder.state !== 'inactive') mediaRecorder.stop();
        if (recBadge) { recBadge.classList.add('hidden'); recBadge.classList.remove('flex'); }
        actualizarBotonesTranscripcion(false);
    }

    function actualizarBotonesTranscripcion(grabando) {
        const btnStart = document.getElementById('manual-start-btn');
        const btnStop  = document.getElementById('manual-stop-btn');
        if (btnStart) btnStart.disabled = grabando;
        if (btnStop)  btnStop.disabled  = !grabando;
    }

    // ════════════════════════════════
    // 4. GUARDAR ACTA
    // ════════════════════════════════
    async function guardarActa() {
        const contenido = transcripcionTotal.trim();
        if (!contenido) return null;
        try {
            const res  = await fetch(`/reuniones/${REUNION_ID}/acta`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
                body: JSON.stringify({ contenido })
            });
            const data = await res.json();
            return data.acta_id || null;
        } catch (_) { return null; }
    }

    // ════════════════════════════════
    // 5. POST CALL
    // ════════════════════════════════
    async function mostrarPostCall() {
        document.getElementById('call-container').style.display = 'none';
        if (modBar) modBar.classList.add('hidden');
        postScreen.classList.remove('hidden');

        if (ES_MODERADOR && transcripcionTotal.trim()) {
            const actaId = await guardarActa();
            if (actaId) {
                const placeholder = document.getElementById('acta-btn-placeholder');
                if (placeholder) {
                    placeholder.outerHTML = `
                        <a href="/actas/${actaId}"
                           class="w-full flex items-center justify-center gap-2 px-6 py-3.5 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-xl transition-all shadow-lg">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            Ver Acta Generada
                        </a>`;
                }
            }
        }
    }

    // ════════════════════════════════
    // 6. ASISTENCIA
    // ════════════════════════════════
    function registrarEntrada() {
        fetch(`/reuniones/${REUNION_ID}/asistencia/entrada`, {
            method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF }
        }).catch(() => {});
    }
    function registrarSalida() {
        navigator.sendBeacon(`/reuniones/${REUNION_ID}/asistencia/salida`);
    }

    window.addEventListener('beforeunload', () => {
        detener();
        registrarSalida();
    });

    // ════════════════════════════════
    // 7. BOTONES MODERADOR
    // ════════════════════════════════
    document.getElementById('manual-start-btn')?.addEventListener('click', () => {
        isRecording = true;
        iniciar();
    });

    document.getElementById('manual-stop-btn')?.addEventListener('click', () => {
        detener();
    });

    document.getElementById('btn-finalizar-reunion')?.addEventListener('click', async () => {
        if (!confirm('¿Finalizar la reunión? La transcripción se guardará automáticamente.')) return;
        const btn = document.getElementById('btn-finalizar-reunion');
        btn.disabled = true;
        btn.textContent = 'Finalizando...';
        detener();
        await guardarActa();
        callFrame.leave();
    });

    // ════════════════════════════════
    // 8. COPIAR LINK
    // ════════════════════════════════
    document.getElementById('copy-link-btn')?.addEventListener('click', () => {
        navigator.clipboard.writeText(DAILY_URL)
            .then(() => showToast('✅ Link copiado', 'bg-gray-700'))
            .catch(() => alert('Link: ' + DAILY_URL));
    });

    // ════════════════════════════════
    // 9. PANTALLA COMPLETA
    // ════════════════════════════════
    const fsBtn      = document.getElementById('fullscreen-btn');
    const fsExpand   = document.getElementById('fs-icon-expand');
    const fsCompress = document.getElementById('fs-icon-compress');
    const wrapper    = document.getElementById('video-wrapper');

    fsBtn?.addEventListener('click', () => {
        if (!document.fullscreenElement) {
            wrapper.requestFullscreen().then(() => {
                fsExpand.classList.add('hidden');
                fsCompress.classList.remove('hidden');
            }).catch(() => {});
        } else {
            document.exitFullscreen().then(() => {
                fsExpand.classList.remove('hidden');
                fsCompress.classList.add('hidden');
            }).catch(() => {});
        }
    });

    document.addEventListener('fullscreenchange', () => {
        if (!document.fullscreenElement) {
            fsExpand.classList.remove('hidden');
            fsCompress.classList.add('hidden');
        }
    });

    // ════════════════════════════════
    // 10. TOAST
    // ════════════════════════════════
    function showToast(msg, bg = 'bg-gray-700') {
        const t = document.getElementById('toast');
        t.className = `fixed bottom-24 left-1/2 -translate-x-1/2 z-50 px-5 py-3 rounded-xl text-white text-sm font-medium shadow-2xl ${bg}`;
        t.textContent = msg;
        t.classList.remove('hidden');
        setTimeout(() => t.classList.add('hidden'), 3000);
    }

    console.log('🎉 DocuMeet Videollamada lista');
})();
</script>
@endsection