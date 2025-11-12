<?php

namespace App\Http\Controllers;

use App\Models\Reunion;
use App\Models\Transcripcion;
use App\Services\DeepgramService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class TranscripcionController extends Controller
{
    /**
     * Guardar transcripción (desde Web Speech API o manual)
     */
    public function store(Request $request, Reunion $reunion)
    {
        // Validar acceso (organizador o invitado)
        if ($reunion->user_id !== Auth::id() && !$reunion->invitados->contains(Auth::id())) {
            return response()->json([
                'ok' => false,
                'error' => 'No tienes permiso para esta reunión'
            ], 403);
        }

        // Validación de campos
        $data = $request->validate([
            'contenido' => 'required|string',
            'fuente' => 'in:manual,stt,webspeech,deepgram'
        ]);

        try {
            // Guardar transcripción
            $trans = $reunion->transcripciones()->create([
                'user_id' => Auth::id(),
                'contenido' => $data['contenido'],
                'fuente' => $data['fuente'] ?? 'webspeech'
            ]);

            Log::info('Transcripción guardada', [
                'reunion_id' => $reunion->id,
                'user_id' => Auth::id(),
                'fuente' => $trans->fuente,
                'longitud' => strlen($trans->contenido)
            ]);

            return response()->json([
                'ok' => true,
                'transcripcion' => [
                    'id' => $trans->id,
                    'contenido' => $trans->contenido,
                    'fuente' => $trans->fuente,
                    'created_at' => $trans->created_at->format('H:i:s')
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error al guardar transcripción: ' . $e->getMessage());

            return response()->json([
                'ok' => false,
                'error' => 'Error al guardar la transcripción'
            ], 500);
        }
    }

    /**
     * Obtener la última transcripción
     */
    public function showLast(Reunion $reunion)
    {
        $transcripcion = $reunion->transcripciones()
            ->with('autor:id,name,email')
            ->orderBy('created_at', 'desc')
            ->first();

        return response()->json([
            'ok' => true,
            'transcripcion' => $transcripcion
        ]);
    }

    /**
     * Obtener todas las transcripciones de una reunión
     */
    public function getAll(Reunion $reunion)
    {
        $transcripciones = $reunion->transcripciones()
            ->with('autor:id,name,email')
            ->orderBy('created_at', 'asc')
            ->get();

        // Concatenar todo el contenido
        $contenidoCompleto = $transcripciones->pluck('contenido')->implode("\n\n");

        return response()->json([
            'ok' => true,
            'transcripciones' => $transcripciones,
            'contenido_completo' => $contenidoCompleto,
            'total' => $transcripciones->count()
        ]);
    }

    /**
     * Ver todas las transcripciones (para debugging)
     */
    public function index(Reunion $reunion)
    {
        $transcripciones = $reunion->transcripciones()
            ->with('autor:id,name,email')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'ok' => true,
            'reunion' => [
                'id' => $reunion->id,
                'titulo' => $reunion->titulo
            ],
            'transcripciones' => $transcripciones->map(function ($t) {
                return [
                    'id' => $t->id,
                    'autor' => $t->autor->name ?? 'Usuario eliminado',
                    'contenido' => $t->contenido,
                    'fuente' => $t->fuente,
                    'fecha' => $t->created_at->format('d/m/Y H:i:s')
                ];
            })
        ]);
    }

    /**
     * Procesar audio con Deepgram
     */
    public function procesarAudio(Request $request, Reunion $reunion)
{
    // Validar acceso
    if ($reunion->user_id !== Auth::id() && !$reunion->invitados->contains(Auth::id())) {
        return response()->json(['ok' => false, 'error' => 'Sin permiso'], 403);
    }

    $request->validate([
        'audio' => 'required|file|max:25600'
    ]);

    try {
        $audioFile = $request->file('audio');
        $fileName = uniqid('audio_') . '.webm';
        $directory = storage_path('app/temp_audio');

        if (!file_exists($directory)) {
            mkdir($directory, 0755, true);
        }

        $fullPath = $directory . DIRECTORY_SEPARATOR . $fileName;
        $audioFile->move($directory, $fileName);

        Log::info('Audio guardado para transcripción', [
            'reunion_id' => $reunion->id,
            'user_id' => Auth::id(),
            'tamaño' => filesize($fullPath)
        ]);

        if (!file_exists($fullPath)) {
            throw new \Exception('El archivo no se guardó correctamente');
        }

        // Transcribir con Deepgram
        $deepgram = new DeepgramService();
        $texto = $deepgram->transcribir($fullPath);

        // Eliminar archivo temporal
        @unlink($fullPath);

        if ($texto && strlen(trim($texto)) > 0) {
            // Buscar transcripción existente de esta reunión para este usuario
            $trans = $reunion->transcripciones()
                ->where('user_id', Auth::id())
                ->where('fuente', 'deepgram')
                ->where('created_at', '>=', now()->subHours(3)) // Solo en las últimas 3 horas
                ->first();

            if ($trans) {
                // Actualizar transcripción existente (concatenar)
                $trans->update([
                    'contenido' => $trans->contenido . ' ' . trim($texto)
                ]);

                Log::info('Transcripción actualizada (concatenada)', [
                    'transcripcion_id' => $trans->id,
                    'reunion_id' => $reunion->id,
                    'nueva_longitud' => strlen($trans->contenido)
                ]);
            } else {
                // Crear nueva transcripción
                $trans = $reunion->transcripciones()->create([
                    'user_id' => Auth::id(),
                    'contenido' => trim($texto),
                    'fuente' => 'deepgram'
                ]);

                Log::info('Nueva transcripción creada', [
                    'transcripcion_id' => $trans->id,
                    'reunion_id' => $reunion->id
                ]);
            }

            return response()->json([
                'ok' => true,
                'texto' => $texto,
                'id' => $trans->id,
                'created_at' => $trans->created_at->format('H:i:s')
            ]);
        }

        Log::warning('Deepgram no detectó voz en el audio');

        return response()->json([
            'ok' => false,
            'error' => 'No se detectó voz en el audio (silencio)'
        ], 400);

    } catch (\Exception $e) {
        Log::error('Error procesando audio con Deepgram', [
            'error' => $e->getMessage(),
            'reunion_id' => $reunion->id,
            'user_id' => Auth::id()
        ]);

        return response()->json([
            'ok' => false,
            'error' => 'Error al procesar audio: ' . $e->getMessage()
        ], 500);
    }
}
}