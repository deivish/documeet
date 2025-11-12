@extends('layouts.app')

@section('titulo')
    Videollamada — {{ $reunion->titulo }}
@endsection

@section('content')
    <div class="min-h-screen bg-gray-50">
        {{-- Barra superior --}}
        <div class="bg-white shadow-sm sticky top-0 z-10">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between h-16">
                    <div class="flex-1">
                        <h2 class="text-lg sm:text-xl font-bold text-gray-900 truncate">
                            {{ $reunion->titulo }}
                        </h2>
                    </div>

                    {{-- Botón copiar link --}}
                    <div class="flex items-center gap-3">
                        <button id="copy-link-btn"
                            class="inline-flex items-center px-3 py-2 text-sm bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                            📋 Copiar Link
                        </button>

                        <a href="{{ route('reuniones.show', $reunion->id) }}"
                            class="inline-flex items-center px-3 py-2 text-sm bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
                            ← Volver
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Contenedor principal --}}
        <div class="max-w-7xl mx-auto px-4 py-6">
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">

                {{-- Video Daily.co --}}
                <div class="lg:col-span-3">
                    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                        <div id="call-container" style="min-height: 500px; height: 70vh;"></div>
                    </div>
                </div>

                {{-- Panel de transcripción --}}
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-xl shadow-lg p-4 sticky top-20"
                        style="max-height: calc(100vh - 120px); overflow-y: auto;">

                        <h3 class="text-lg font-semibold text-gray-900 mb-3">
                            🎙️ Transcripción en Vivo
                        </h3>

                        {{-- Controles --}}
                        <div class="flex gap-2 mb-3">
                            <button id="manual-start-btn"
                                class="flex-1 px-3 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition">
                                ▶️ Iniciar
                            </button>
                            <button id="manual-stop-btn"
                                class="flex-1 px-3 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 transition"
                                disabled>
                                ⏹️ Detener
                            </button>
                        </div>

                        {{-- Estado --}}
                        <div id="transcription-status"
                            class="text-xs text-center px-3 py-2 rounded-lg bg-gray-100 text-gray-600 mb-3">
                            ⏸️ Detenida
                        </div>

                        {{-- Aviso mejorado --}}
                        <div
                            class="mb-4 p-3 bg-gradient-to-r from-blue-50 to-indigo-50 border-2 border-blue-300 rounded-lg">
                            <p class="text-sm text-blue-900 font-bold mb-2 flex items-center">
                                <span class="text-xl mr-2">🎙️</span>
                                Transcripción Continua
                            </p>
                            <div class="text-xs text-blue-800 space-y-1">
                                <p class="flex items-start">
                                    <span class="mr-2">1️⃣</span>
                                    <span>Haz clic en <strong>"▶️ Iniciar"</strong></span>
                                </p>
                                <p class="flex items-start">
                                    <span class="mr-2">2️⃣</span>
                                    <span>Selecciona la <strong>pestaña de la videollamada</strong></span>
                                </p>
                                <p class="flex items-start">
                                    <span class="mr-2">3️⃣</span>
                                    <span><strong class="text-red-600">✅ MARCA "Compartir audio"</strong></span>
                                </p>
                                <p class="flex items-start">
                                    <span class="mr-2">4️⃣</span>
                                    <span>¡Habla! Se transcribe cada <strong>15 segundos</strong></span>
                                </p>
                            </div>
                            <div class="mt-2 p-2 bg-green-100 rounded border border-green-300">
                                <p class="text-xs text-green-800">
                                    ✅ Captura el audio de <strong>TODOS</strong> los participantes en <strong>UN solo
                                        registro continuo</strong>
                                </p>
                            </div>
                        </div>

                        {{-- Texto temporal --}}
                        <div class="mb-3">
                            <div id="interim-text"
                                class="min-h-[50px] p-3 bg-blue-50 border border-blue-200 rounded-lg text-sm">
                                <span class="text-gray-400">Esperando...</span>
                            </div>
                        </div>

                        {{-- Transcripción completa --}}
                        <div class="mb-3">
                            <textarea id="notes"
                                class="w-full border border-gray-300 rounded-lg p-3 text-sm resize-none focus:ring-2 focus:ring-blue-500"
                                style="min-height: 250px;" placeholder="La transcripción completa aparecerá aquí automáticamente..." readonly></textarea>
                        </div>

                        {{-- Estadísticas --}}
                        <div class="flex justify-between text-xs text-gray-500 mb-4">
                            <span id="word-count">0 palabras</span>
                            <span id="char-count">0 caracteres</span>
                        </div>

                        {{-- Botones (solo moderador) --}}
                        @if ($reunion->user_id === Auth::id())
                            <div class="space-y-2 pt-4 border-t border-gray-200">
                                <button id="btn-guardar"
                                    class="w-full px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition">
                                    💾 Guardar notas
                                </button>

                                @if ($reunion->acta && $reunion->acta->estado !== 'final')
                                    <form action="{{ route('actas.finalizar', $reunion->acta->id) }}" method="POST">
                                        @csrf
                                        <button type="submit"
                                            class="w-full px-4 py-2 bg-purple-600 text-white text-sm font-medium rounded-lg hover:bg-purple-700 transition">
                                            📋 Finalizar Acta
                                        </button>
                                    </form>
                                @elseif($reunion->acta && $reunion->acta->estado === 'final')
                                    <a href="{{ route('actas.show', $reunion->acta->id) }}"
                                        class="block w-full px-4 py-2 bg-purple-600 text-white text-sm font-medium rounded-lg hover:bg-purple-700 transition text-center">
                                        📄 Ver Acta
                                    </a>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Daily.co SDK --}}
    <script src="https://unpkg.com/@daily-co/daily-js"></script>

    <script>
        console.log('🟢 Sistema iniciado: Daily.co + Deepgram (Transcripción Continua)');

        (function() {
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
            });

            console.log('📹 Conectando a Daily.co...');

            // ========================================
            // PARTE 2: TRANSCRIPCIÓN CONTINUA CON DEEPGRAM
            // ========================================

            const notes = document.getElementById('notes');
            const interimText = document.getElementById('interim-text');
            const statusIndicator = document.getElementById('transcription-status');
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
                            // ✅ Concatenar en el textarea (acumulativo)
                            transcripcionCompleta += data.texto + ' ';
                            notes.value = transcripcionCompleta;
                            updateStats();

                            const preview = data.texto.length > 60 ?
                                data.texto.substring(0, 60) + '...' :
                                data.texto;

                            interimText.innerHTML = `<span class="text-green-600">✅ ${preview}</span>`;
                            console.log('✅ Transcrito y concatenado:', data.texto);

                            // Auto-scroll al final del textarea
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

                    // PASO 1: Capturar audio del sistema (Daily.co)
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

                    // Detener video (solo queremos audio)
                    displayStream.getVideoTracks().forEach(track => track.stop());

                    // PASO 2: Capturar micrófono local
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

                        // Continuar solo con audio del sistema
                        const audioStream = new MediaStream(audioTracks);
                        iniciarGrabacion(audioStream, audioTracks);
                        return;
                    }

                    // PASO 3: Mezclar ambos audios usando AudioContext
                    const audioContext = new AudioContext();

                    // Fuente 1: Audio del sistema (Daily.co)
                    const systemSource = audioContext.createMediaStreamSource(
                        new MediaStream(audioTracks)
                    );

                    // Fuente 2: Micrófono
                    const micSource = audioContext.createMediaStreamSource(micStream);

                    // Destino: Stream mezclado
                    const destination = audioContext.createMediaStreamDestination();

                    // Conectar ambas fuentes al destino
                    systemSource.connect(destination);
                    micSource.connect(destination);

                    console.log('✅ Audios mezclados:', {
                        sistema: audioTracks[0].label,
                        microfono: micStream.getAudioTracks()[0].label
                    });

                    // Usar el stream mezclado para grabación
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

            // Nueva función auxiliar para iniciar grabación
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

                // Iniciar grabación
                mediaRecorder.start();
                console.log('🎤 Grabación iniciada con ambos audios');

                // Enviar cada 15 segundos
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

                statusIndicator.textContent = '🔴 Grabando (Sistema + Micrófono)';
                statusIndicator.className =
                    'text-xs text-center px-3 py-2 rounded-lg bg-red-500 text-white font-medium mb-3 animate-pulse';
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

                statusIndicator.textContent = '⏸️ Detenida';
                statusIndicator.className = 'text-xs text-center px-3 py-2 rounded-lg bg-gray-100 text-gray-600 mb-3';
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

            // ========================================
            // PARTE 3: EVENTOS DE DAILY.CO
            // ========================================

            callFrame.on('joined-meeting', () => {
                console.log('✅ Unido a Daily.co');

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
            });

            callFrame.on('error', (e) => {
                console.error('❌ Error Daily.co:', e);
            });

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
                    btnGuardar.textContent = '⏳ Guardando...';

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
                            btnGuardar.textContent = '💾 Guardar notas';
                        })
                        .catch(() => {
                            alert("❌ Error al guardar");
                            btnGuardar.textContent = '💾 Guardar notas';
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
                        const originalText = copyLinkBtn.innerHTML;
                        copyLinkBtn.innerHTML = '✅ ¡Copiado!';
                        copyLinkBtn.classList.remove('bg-green-600', 'hover:bg-green-700');
                        copyLinkBtn.classList.add('bg-green-800');

                        setTimeout(() => {
                            copyLinkBtn.innerHTML = originalText;
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

    {{-- Modal de compartir link --}}
    <div id="share-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
        <div class="bg-white rounded-xl shadow-2xl max-w-md w-full mx-4 p-6">
            <div class="flex justify-between items-start mb-4">
                <h3 class="text-xl font-bold text-gray-900">🔗 Compartir Videollamada</h3>
                <button id="close-modal" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg>
                </button>
            </div>

            <p class="text-sm text-gray-600 mb-4">
                Comparte este link con los participantes para que se unan a la videollamada:
            </p>

            <div class="bg-gray-50 rounded-lg p-3 mb-4 break-all text-sm font-mono">
                {{ $dailyUrl }}
            </div>

            <div class="flex gap-2">
                <button id="copy-modal-btn"
                    class="flex-1 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                    📋 Copiar Link
                </button>
                <button id="close-modal-btn"
                    class="flex-1 px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                    Cerrar
                </button>
            </div>
        </div>
    </div>
@endsection
