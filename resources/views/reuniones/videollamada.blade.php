@extends('layouts.app')

@section('titulo')
    Videollamada
@endsection

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-900 via-gray-800 to-indigo-900">
    
    {{-- Barra Superior Mejorada --}}
    <div class="bg-gradient-to-r from-gray-800 to-gray-900 shadow-2xl sticky top-0 z-50 border-b border-gray-700">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                {{-- Logo y Título --}}
                <div class="flex items-center gap-4 flex-1 min-w-0">
                    <div class="w-10 h-10 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl flex items-center justify-center shadow-lg flex-shrink-0">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z">
                            </path>
                        </svg>
                    </div>
                    <div class="min-w-0 flex-1">
                        <h2 class="text-base sm:text-lg font-bold text-white truncate">
                            {{ $reunion->titulo }}
                        </h2>
                        <p class="text-xs text-gray-400 truncate hidden sm:block">
                            {{ \Carbon\Carbon::parse($reunion->fecha_hora)->format('d/m/Y H:i') }}
                        </p>
                    </div>
                </div>

                {{-- Botones de Acción --}}
                <div class="flex items-center gap-2 ml-4">
                    <button id="copy-link-btn"
                            class="inline-flex items-center px-3 py-2 text-sm bg-green-600 text-white rounded-lg hover:bg-green-700 transition-all shadow-md hover:shadow-lg">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z">
                            </path>
                        </svg>
                        <span class="hidden sm:inline">Copiar Link</span>
                    </button>

                    <a href="{{ route('reuniones.show', $reunion->id) }}"
                       class="inline-flex items-center px-3 py-2 text-sm bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-all shadow-md hover:shadow-lg">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                        <span class="hidden sm:inline">Volver</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Contenedor Principal --}}
    <div class="max-w-7xl mx-auto px-4 py-6">
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">

            {{-- Panel de Video (3/4) --}}
            <div class="lg:col-span-3 space-y-4">
                {{-- Contenedor de Video --}}
                <div class="bg-gray-800 rounded-2xl shadow-2xl overflow-hidden border-2 border-gray-700">
                    <div id="call-container" class="relative" style="min-height: 500px; height: 70vh;">
                        {{-- ✅ Loader mejorado (se oculta automáticamente) --}}
                        <div id="video-loader" class="absolute inset-0 flex items-center justify-center bg-gray-900 z-10">
                            <div class="text-center">
                                <div class="w-16 h-16 border-4 border-indigo-500 border-t-transparent rounded-full animate-spin mx-auto mb-4"></div>
                                <p class="text-white font-medium">Conectando a la videollamada...</p>
                                <p class="text-gray-400 text-sm mt-2">Espera un momento</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Info Card (Desktop) --}}
                <div class="hidden lg:block bg-gradient-to-r from-indigo-900/50 to-purple-900/50 backdrop-blur rounded-xl p-4 border border-indigo-700/50">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-indigo-500/20 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                                    </path>
                                </svg>
                            </div>
                            <div>
                                <p class="text-white font-semibold text-sm">Transcripción Activa</p>
                                <p class="text-gray-400 text-xs">Capturando audio en tiempo real</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
                            <span class="text-green-400 text-sm font-medium" id="status-indicator">Iniciando...</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Panel de Transcripción (1/4) --}}
            <div class="lg:col-span-1">
                <div class="bg-gray-800 rounded-2xl shadow-2xl border-2 border-gray-700 overflow-hidden sticky top-24"
                     style="max-height: calc(100vh - 120px);">
                    
                    {{-- Header --}}
                    <div class="bg-gradient-to-r from-purple-600 to-indigo-600 p-4 border-b border-gray-700">
                        <h3 class="text-lg font-bold text-white flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                    d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z">
                                </path>
                            </svg>
                            Transcripción en Vivo
                        </h3>
                        <p class="text-xs text-purple-200 mt-1">Captura automática de audio</p>
                    </div>

                    <div class="p-4 space-y-4 overflow-y-auto" style="max-height: calc(100vh - 250px);">
                        
                        {{-- Controles --}}
                        <div class="flex gap-2">
                            <button id="manual-start-btn"
                                    class="flex-1 flex items-center justify-center gap-2 px-3 py-3 bg-green-600 text-white text-sm font-semibold rounded-xl hover:bg-green-700 transition-all shadow-lg hover:shadow-xl">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Iniciar
                            </button>
                            <button id="manual-stop-btn"
                                    class="flex-1 flex items-center justify-center gap-2 px-3 py-3 bg-red-600 text-white text-sm font-semibold rounded-xl hover:bg-red-700 transition-all shadow-lg hover:shadow-xl"
                                    disabled>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 10a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1v-4z"></path>
                                </svg>
                                Detener
                            </button>
                        </div>

                        {{-- Estado --}}
                        <div id="transcription-status"
                             class="text-xs text-center px-4 py-3 rounded-xl bg-gray-700 text-gray-300 border border-gray-600">
                            <div class="flex items-center justify-center gap-2">
                                <span class="w-2 h-2 bg-gray-500 rounded-full"></span>
                                Detenida
                            </div>
                        </div>

                        {{-- Instrucciones --}}
                        <div class="bg-gradient-to-br from-blue-900/50 to-indigo-900/50 backdrop-blur rounded-xl p-4 border border-blue-700/50">
                            <p class="text-sm text-blue-300 font-bold mb-3 flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                                    </path>
                                </svg>
                                Cómo Usar
                            </p>
                            <div class="space-y-2 text-xs text-blue-200">
                                <div class="flex items-start gap-2">
                                    <span class="flex-shrink-0 w-5 h-5 bg-blue-500 rounded-full flex items-center justify-center text-white font-bold text-xs">1</span>
                                    <p>Haz clic en <strong>"Iniciar"</strong></p>
                                </div>
                                <div class="flex items-start gap-2">
                                    <span class="flex-shrink-0 w-5 h-5 bg-blue-500 rounded-full flex items-center justify-center text-white font-bold text-xs">2</span>
                                    <p>Selecciona la pestaña de videollamada</p>
                                </div>
                                <div class="flex items-start gap-2">
                                    <span class="flex-shrink-0 w-5 h-5 bg-blue-500 rounded-full flex items-center justify-center text-white font-bold text-xs">3</span>
                                    <p><strong class="text-yellow-300">¡Marca "Compartir audio"!</strong></p>
                                </div>
                                <div class="flex items-start gap-2">
                                    <span class="flex-shrink-0 w-5 h-5 bg-blue-500 rounded-full flex items-center justify-center text-white font-bold text-xs">4</span>
                                    <p>Transcripción cada 15 segundos</p>
                                </div>
                            </div>
                            <div class="mt-3 p-2 bg-green-900/50 rounded-lg border border-green-700/50">
                                <p class="text-xs text-green-300 flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    Captura TODOS los participantes
                                </p>
                            </div>
                        </div>

                        {{-- Texto Temporal --}}
                        <div>
                            <label class="block text-xs font-semibold text-gray-400 mb-2 uppercase tracking-wide">Texto en Proceso</label>
                            <div id="interim-text"
                                 class="min-h-[60px] p-3 bg-gray-900 border-2 border-gray-700 rounded-xl text-sm text-gray-300">
                                <span class="text-gray-500">Esperando...</span>
                            </div>
                        </div>

                        {{-- Transcripción Completa --}}
                        <div>
                            <label class="block text-xs font-semibold text-gray-400 mb-2 uppercase tracking-wide">Transcripción Completa</label>
                            <textarea id="notes"
                                      class="w-full bg-gray-900 border-2 border-gray-700 rounded-xl p-3 text-sm text-white resize-none focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                      style="min-height: 250px;" 
                                      placeholder="La transcripción completa aparecerá aquí automáticamente..." 
                                      readonly></textarea>
                        </div>

                        {{-- Estadísticas --}}
                        <div class="flex justify-between text-xs text-gray-400 px-1">
                            <span id="word-count" class="flex items-center gap-1">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path>
                                </svg>
                                0 palabras
                            </span>
                            <span id="char-count">0 caracteres</span>
                        </div>

                        {{-- Botones (Solo Moderador) --}}
                        @if ($reunion->user_id === Auth::id())
                        <div class="space-y-2 pt-4 border-t border-gray-700">
                            <button id="btn-guardar"
                                    class="w-full flex items-center justify-center gap-2 px-4 py-3 bg-blue-600 text-white text-sm font-semibold rounded-xl hover:bg-blue-700 transition-all shadow-lg hover:shadow-xl">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path>
                                </svg>
                                Guardar Notas
                            </button>

                            @if ($reunion->acta && $reunion->acta->estado !== 'final')
                            <form action="{{ route('actas.finalizar', $reunion->acta->id) }}" method="POST">
                                @csrf
                                <button type="submit"
                                        class="w-full flex items-center justify-center gap-2 px-4 py-3 bg-purple-600 text-white text-sm font-semibold rounded-xl hover:bg-purple-700 transition-all shadow-lg hover:shadow-xl">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    Finalizar Acta
                                </button>
                            </form>
                            @elseif($reunion->acta && $reunion->acta->estado === 'final')
                            <a href="{{ route('actas.show', $reunion->acta->id) }}"
                               class="block w-full flex items-center justify-center gap-2 px-4 py-3 bg-purple-600 text-white text-sm font-semibold rounded-xl hover:bg-purple-700 transition-all shadow-lg hover:shadow-xl text-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                Ver Acta
                            </a>
                            @endif
                        </div>
                        @endif
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

{{-- Daily.co SDK --}}
<script src="https://unpkg.com/@daily-co/daily-js"></script>

<script>
    console.log('🟢 Sistema iniciado: Daily.co + Deepgram');

    (function() {
        // ✅ OCULTAR LOADER cuando Daily.co cargue
        const loader = document.getElementById('video-loader');
        const statusIndicator = document.getElementById('status-indicator');

        // ========================================
        // PARTE 1: VIDEOLLAMADA CON DAILY.CO
        // ========================================

        const callFrame = window.DailyIframe.createFrame(
            document.getElementById('call-container'), {
                url: "{{ $dailyUrl }}",
                showLeaveButton: true,
                showFullscreenButton: true,
                iframeStyle: {
                    width: '100%',
                    height: '100%',
                    border: '0',
                    borderRadius: '8px'
                }
            }
        );

        // Unirse automáticamente
        callFrame.join({
            userName: "{{ $userName }}"
        }).then(() => {
            console.log('✅ Solicitud de unión enviada');
        }).catch(err => {
            console.error('❌ Error al unirse:', err);
            if (loader) {
                loader.innerHTML = `
                    <div class="text-center">
                        <svg class="w-16 h-16 text-red-500 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <p class="text-white font-medium">Error al conectar</p>
                        <p class="text-gray-400 text-sm mt-2">${err.message || 'Intenta recargar la página'}</p>
                    </div>
                `;
            }
        });

        console.log('📹 Conectando a Daily.co...');

        // ========================================
        // PARTE 2: EVENTOS DE DAILY.CO
        // ========================================

        callFrame.on('loading', () => {
            console.log('⏳ Daily.co cargando...');
        });

        callFrame.on('loaded', () => {
            console.log('✅ Daily.co cargado');
            // ✅ Ocultar loader cuando cargue
            if (loader) {
                loader.style.display = 'none';
            }
        });

        callFrame.on('joined-meeting', (event) => {
            console.log('✅ Unido a Daily.co:', event);
            
            // ✅ Ocultar loader
            if (loader) {
                loader.style.display = 'none';
            }
            
            // Actualizar indicador
            if (statusIndicator) {
                statusIndicator.textContent = 'En Vivo';
            }

            // Registrar asistencia
            fetch("{{ route('reuniones.asistencia.entrada', $reunion->id) }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });
        });

        callFrame.on('left-meeting', () => {
            console.log('👋 Saliendo de Daily.co');
            detener();
            if (statusIndicator) {
                statusIndicator.textContent = 'Desconectado';
            }
        });

        callFrame.on('error', (e) => {
            console.error('❌ Error Daily.co:', e);
            if (loader) {
                loader.innerHTML = `
                    <div class="text-center">
                        <svg class="w-16 h-16 text-red-500 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <p class="text-white font-medium">Error de conexión</p>
                        <p class="text-gray-400 text-sm mt-2">Verifica tu conexión e intenta nuevamente</p>
                        <button onclick="location.reload()" class="mt-4 px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                            Reintentar
                        </button>
                    </div>
                `;
            }
        });

        // ✅ Timeout de seguridad (si no carga en 30 segundos)
        setTimeout(() => {
            if (loader && loader.style.display !== 'none') {
                console.warn('⚠️ Timeout: Ocultando loader forzadamente');
                loader.style.display = 'none';
            }
        }, 30000);

        // ========================================
        // PARTE 3: TRANSCRIPCIÓN CONTINUA CON DEEPGRAM
        // ========================================

        const notes = document.getElementById('notes');
        const interimText = document.getElementById('interim-text');
        const statusIndicatorTranscription = document.getElementById('transcription-status');
        const wordCount = document.getElementById('word-count');
        const charCount = document.getElementById('char-count');

        let mediaRecorder = null;
        let audioChunks = [];
        let transcripcionCompleta = '';
        let isRecording = false;
        let intervalId = null;

        function updateStats() {
            const text = notes.value;
            const words = text.trim().split(/\s+/).filter(w => w.length > 0).length;
            wordCount.textContent = `${words} palabras`;
            charCount.textContent = `${text.length} caracteres`;
        }

        function enviarAudio(audioBlob) {
            console.log('📤 Enviando audio a Deepgram:', audioBlob.size, 'bytes');

            const formData = new FormData();
            formData.append('audio', audioBlob, 'audio.webm');

            interimText.innerHTML = '<span class="text-blue-600 animate-pulse">⏳ Transcribiendo...</span>';

            fetch("{{ route('reuniones.transcripciones.procesar-audio', $reunion->id) }}", {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: formData
                })
                .then(res => {
                    console.log('📥 Respuesta:', res.status);
                    return res.json();
                })
                .then(data => {
                    console.log('📥 Datos:', data);

                    if (data.ok && data.texto) {
                        transcripcionCompleta += data.texto + ' ';
                        notes.value = transcripcionCompleta;
                        updateStats();

                        const preview = data.texto.length > 60 ?
                            data.texto.substring(0, 60) + '...' :
                            data.texto;

                        interimText.innerHTML = `<span class="text-green-600">✅ ${preview}</span>`;
                        console.log('✅ Transcrito y concatenado:', data.texto);

                        notes.scrollTop = notes.scrollHeight;

                        setTimeout(() => {
                            if (isRecording) {
                                interimText.innerHTML =
                                    '<span class="text-gray-400">🎤 Escuchando...</span>';
                            }
                        }, 3000);
                    } else {
                        console.warn('⚠️ Sin texto:', data);
                        interimText.innerHTML = '<span class="text-yellow-600">⚠️ Silencio detectado</span>';

                        setTimeout(() => {
                            if (isRecording) {
                                interimText.innerHTML =
                                    '<span class="text-gray-400">🎤 Escuchando...</span>';
                            }
                        }, 2000);
                    }
                })
                .catch(err => {
                    console.error('❌ Error:', err);
                    interimText.innerHTML = '<span class="text-red-600">❌ Error al transcribir</span>';

                    setTimeout(() => {
                        if (isRecording) {
                            interimText.innerHTML =
                                '<span class="text-gray-400">🔄 Reintentando...</span>';
                        }
                    }, 2000);
                });
        }

        async function iniciar() {
            try {
                interimText.innerHTML = `
                    <div class="text-blue-700 text-xs">
                        <strong>📢 Configurando captura de audio...</strong><br>
                        Espera un momento...
                    </div>
                `;

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
                    displayStream.getTracks().forEach(track => track.stop());

                    alert('⚠️ NO se compartió el audio del sistema\n\n' +
                        '🔊 Debes marcar la casilla\n' +
                        '"Compartir audio de la pestaña"\n\n' +
                        'Intenta de nuevo.');

                    isRecording = false;
                    document.getElementById('manual-start-btn').disabled = false;
                    document.getElementById('manual-stop-btn').disabled = true;
                    interimText.innerHTML = '<span class="text-red-600">❌ Sin audio del sistema</span>';
                    return;
                }

                console.log('✅ Audio del sistema capturado:', audioTracks[0].getSettings());

                displayStream.getVideoTracks().forEach(track => track.stop());

                let micStream;
                try {
                    micStream = await navigator.mediaDevices.getUserMedia({
                        audio: {
                            echoCancellation: false,
                            noiseSuppression: false,
                            autoGainControl: false,
                            sampleRate: 48000
                        }
                    });

                    console.log('✅ Micrófono capturado:', micStream.getAudioTracks()[0].getSettings());
                } catch (micError) {
                    console.warn('⚠️ No se pudo capturar micrófono:', micError);

                    const audioStream = new MediaStream(audioTracks);
                    iniciarGrabacion(audioStream, audioTracks);
                    return;
                }

                const audioContext = new AudioContext();

                const systemSource = audioContext.createMediaStreamSource(
                    new MediaStream(audioTracks)
                );

                const micSource = audioContext.createMediaStreamSource(micStream);

                const destination = audioContext.createMediaStreamDestination();

                systemSource.connect(destination);
                micSource.connect(destination);

                console.log('✅ Audios mezclados:', {
                    sistema: audioTracks[0].label,
                    microfono: micStream.getAudioTracks()[0].label
                });

                iniciarGrabacion(destination.stream, [
                    ...audioTracks,
                    ...micStream.getAudioTracks()
                ]);

            } catch (error) {
                console.error('❌ Error:', error);

                let mensaje = '❌ Error al capturar audio';

                if (error.name === 'NotAllowedError') {
                    mensaje = '⚠️ Permiso denegado\n\n' +
                        'Debes dar permiso para:\n' +
                        '1. Compartir la pestaña con audio\n' +
                        '2. Usar tu micrófono';
                } else if (error.name === 'NotFoundError') {
                    mensaje = '⚠️ No se encontró audio\n\n' +
                        'Verifica que:\n' +
                        '1. La pestaña tenga audio activo\n' +
                        '2. Tu micrófono esté conectado';
                }

                alert(mensaje);
                isRecording = false;
                document.getElementById('manual-start-btn').disabled = false;
                document.getElementById('manual-stop-btn').disabled = true;
                interimText.innerHTML = '<span class="text-red-600">❌ Error</span>';
            }
        }

        function iniciarGrabacion(audioStream, tracks) {
            const mimeType = MediaRecorder.isTypeSupported('audio/webm;codecs=opus') ?
                'audio/webm;codecs=opus' :
                'audio/webm';

            mediaRecorder = new MediaRecorder(audioStream, {
                mimeType: mimeType,
                audioBitsPerSecond: 128000
            });

            console.log('🎤 MediaRecorder configurado:', mediaRecorder.mimeType);

            mediaRecorder.ondataavailable = (e) => {
                if (e.data && e.data.size > 0) {
                    console.log('📦 Chunk recibido:', e.data.size, 'bytes');
                    audioChunks.push(e.data);
                }
            };

            mediaRecorder.onstop = () => {
                console.log('🛑 Grabación detenida, chunks:', audioChunks.length);

                if (audioChunks.length > 0) {
                    const audioBlob = new Blob(audioChunks, {
                        type: 'audio/webm'
                    });
                    console.log('📦 Blob creado:', audioBlob.size, 'bytes');

                    if (audioBlob.size > 5000) {
                        enviarAudio(audioBlob);
                    } else {
                        console.warn('⚠️ Blob muy pequeño, descartando');
                    }

                    audioChunks = [];
                }
            };

            mediaRecorder.onerror = (e) => {
                console.error('❌ Error MediaRecorder:', e);
            };

            mediaRecorder.start();
            console.log('🎤 Grabación iniciada');

            intervalId = setInterval(() => {
                if (mediaRecorder && mediaRecorder.state === 'recording' && isRecording) {
                    console.log('⏱️ Intervalo: procesando audio...');
                    mediaRecorder.stop();

                    setTimeout(() => {
                        if (isRecording && tracks[0].readyState === 'live') {
                            console.log('🔄 Reiniciando grabación...');
                            mediaRecorder.start();
                        } else {
                            console.warn('⚠️ Pista inactiva, deteniendo');
                            detener();
                        }
                    }, 1000);
                }
            }, 15000);

            statusIndicatorTranscription.innerHTML = `
                <div class="flex items-center justify-center gap-2">
                    <span class="w-2 h-2 bg-red-500 rounded-full animate-pulse"></span>
                    <span class="text-red-300">Grabando</span>
                </div>
            `;
            statusIndicatorTranscription.className =
                'text-xs text-center px-4 py-3 rounded-xl bg-red-900 text-white border border-red-700';
            document.getElementById('manual-start-btn').disabled = true;
            document.getElementById('manual-stop-btn').disabled = false;
            interimText.innerHTML =
                '<span class="text-green-600">✅ Capturando: TODOS + Tu Micrófono</span>';

            console.log('✅ Sistema de transcripción completa activo');
        }

        function detener() {
            isRecording = false;

            if (intervalId) {
                clearInterval(intervalId);
                intervalId = null;
            }

            if (mediaRecorder && mediaRecorder.state !== 'inactive') {
                mediaRecorder.stop();
            }

            statusIndicatorTranscription.innerHTML = `
                <div class="flex items-center justify-center gap-2">
                    <span class="w-2 h-2 bg-gray-500 rounded-full"></span>
                    Detenida
                </div>
            `;
            statusIndicatorTranscription.className = 'text-xs text-center px-4 py-3 rounded-xl bg-gray-700 text-gray-300 border border-gray-600';
            document.getElementById('manual-start-btn').disabled = false;
            document.getElementById('manual-stop-btn').disabled = true;
            interimText.innerHTML = '<span class="text-gray-400">Transcripción detenida</span>';

            console.log('⏹️ Transcripción detenida');
        }

        document.getElementById('manual-start-btn').addEventListener('click', () => {
            isRecording = true;
            iniciar();
        });

        document.getElementById('manual-stop-btn').addEventListener('click', detener);

        window.addEventListener('beforeunload', () => {
            detener();
            navigator.sendBeacon("{{ route('reuniones.asistencia.salida', $reunion->id) }}", '');
            callFrame.leave();
        });

        // ========================================
        // PARTE 4: GUARDAR NOTAS
        // ========================================

        const btnGuardar = document.getElementById('btn-guardar');
        if (btnGuardar) {
            btnGuardar.addEventListener('click', () => {
                const contenido = notes.value.trim();

                if (!contenido) {
                    alert("⚠️ No hay transcripción para guardar");
                    return;
                }

                btnGuardar.disabled = true;
                btnGuardar.innerHTML = '<svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg> Guardando...';

                fetch("{{ route('actas.store', $reunion->id) }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            contenido
                        })
                    })
                    .then(res => res.json())
                    .then(() => {
                        alert("✅ Notas guardadas correctamente");
                        btnGuardar.innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg> Guardar Notas';
                    })
                    .catch(() => {
                        alert("❌ Error al guardar");
                        btnGuardar.innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg> Guardar Notas';
                    })
                    .finally(() => {
                        btnGuardar.disabled = false;
                    });
            });
        }

        // ========================================
        // PARTE 5: COPIAR LINK DE LA VIDEOLLAMADA
        // ========================================

        const copyLinkBtn = document.getElementById('copy-link-btn');
        const dailyUrl = "{{ $dailyUrl }}";

        if (copyLinkBtn) {
            copyLinkBtn.addEventListener('click', () => {
                navigator.clipboard.writeText(dailyUrl).then(() => {
                    const originalHTML = copyLinkBtn.innerHTML;
                    copyLinkBtn.innerHTML = '<svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg><span class="hidden sm:inline">¡Copiado!</span>';
                    copyLinkBtn.classList.remove('bg-green-600', 'hover:bg-green-700');
                    copyLinkBtn.classList.add('bg-green-800');

                    setTimeout(() => {
                        copyLinkBtn.innerHTML = originalHTML;
                        copyLinkBtn.classList.remove('bg-green-800');
                        copyLinkBtn.classList.add('bg-green-600', 'hover:bg-green-700');
                    }, 2000);

                    console.log('📋 Link copiado:', dailyUrl);
                }).catch(err => {
                    console.error('Error al copiar:', err);
                    alert('No se pudo copiar el link. Aquí está:\n\n' + dailyUrl);
                });
            });
        }

        console.log('🎉 Sistema completo listo');
    })();
</script>
@endsection