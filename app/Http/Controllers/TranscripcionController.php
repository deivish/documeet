<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use App\Models\Transcripcion;
use App\Models\Reunion;

class TranscripcionController extends Controller
{
    /**
     * Guarda el audio enviado por el botón Stop Recording,
     * lo transcribe con Whisper y crea un archivo de texto.
     */
    public function store(Request $request, Reunion $reunion)
    {
        // --- 1. Validar acceso ---
        if ($reunion->user_id !== Auth::id() && !$reunion->invitados->contains(Auth::id())) {
            abort(403, 'No tienes permiso para esta reunión.');
        }

        // --- 2. Validar archivo de audio ---
        $request->validate([
            'audio' => 'required|file|mimes:webm,wav,mp3,m4a,ogg|max:102400', // máx 100MB
        ]);

        // --- 3. Guardar el audio en storage/app/audios ---
        $file = $request->file('audio');
        $filename = time() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('audios', $filename);
        $fullPath = storage_path('app/' . $path);

        \Log::info('API KEY leída: ' . substr(config('services.openai.key'), 0, 8) . '********');


        // --- 4. Enviar a Whisper ---
        $response = Http::withToken(config('services.openai.key'))
            ->attach('file', file_get_contents($fullPath), $filename)
            ->post('https://api.openai.com/v1/audio/transcriptions', [
                'model' => 'whisper-1',
                'language' => 'es',   // fuerza español
            ]);

        // --- 5. Verificar respuesta de Whisper ---
        if ($response->failed()) {
            \Log::error('Error al transcribir con Whisper', $response->json());

            return response()->json([
                'error' => 'Error en la transcripción',
                'details' => $response->json(),
            ], 500);
        }

        $texto = $response->json()['text'] ?? 'No se pudo transcribir';

        // --- 6. Guardar en la base de datos ---
        $transcripcion = Transcripcion::create([
            'archivo' => $filename,
            'texto'   => $texto,
            'reunion_id' => $reunion->id,
        ]);

        // --- 7. Guardar también en un archivo .txt (bloc de notas) ---
        $jsonData = [
            'archivo'        => $filename,
            'transcripcion'  => $texto,
            'fecha'          => now()->toDateTimeString(),
            'reunion_id'     => $reunion->id,
        ];

        $jsonFileName = 'transcripciones/' . pathinfo($filename, PATHINFO_FILENAME) . '.txt';

        Storage::disk('local')->put(
            $jsonFileName,
            json_encode($jsonData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
        );

        // --- 8. Respuesta final ---
        return response()->json([
            'success'       => true,
            'message'       => 'Transcripción generada correctamente',
            'transcription' => $texto,
            'filename'      => $filename,
            'json_file'     => $jsonFileName,
        ]);
    }
}
