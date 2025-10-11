<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;

class AudioController extends Controller
{
    public function store(Request $request)
    {
        // 1. Validar y guardar el audio
        if (!$request->hasFile('audio')) {
            return response()->json([
                'success' => false,
                'message' => 'No se recibió ningún archivo de audio.'
            ], 400);
        }

        $file = $request->file('audio');
        $path = $file->store('audios', 'public');                 // audios en storage/app/public/audios
        $fullPath = Storage::disk('public')->path($path);         // ruta completa en el servidor
        $filename = basename($path);

        Log::info("Audio guardado en: {$fullPath}");

        // 2. Configuración de Speechmatics
        $apiKey = config('services.speechmatics.key');
        $lang = config('services.speechmatics.lang', 'es');

        $client = new Client();

        try {
            // 3. Crear el job en Speechmatics
            $payload = [
                'type' => 'transcription',
                'transcription_config' => [
                    'language' => $lang
                ]
            ];

            Log::info("Payload config enviado a Speechmatics: " . json_encode($payload));

            $response = $client->request('POST', 'https://asr.api.speechmatics.com/v2/jobs', [
                'headers' => [
                    'Authorization' => "Bearer {$apiKey}"
                ],
                'multipart' => [
                    [
                        'name'     => 'config',
                        'contents' => json_encode($payload),
                        'headers'  => ['Content-Type' => 'application/json']
                    ],
                    [
                        'name'     => 'data_file',
                        'contents' => fopen($fullPath, 'r'),
                        'filename' => $filename
                    ]
                ]
            ]);

            $job = json_decode($response->getBody(), true);
            $jobId = $job['id'] ?? null;

            if (!$jobId) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se pudo obtener el ID del job.'
                ], 500);
            }

            Log::info("Job Speechmatics creado: {$jobId}");

            // 4. Polling hasta que termine la transcripción
            $texto = null;
            $pollingUrl = "https://asr.api.speechmatics.com/v2/jobs/{$jobId}";

            for ($i = 0; $i < 20; $i++) { // Máximo 20 intentos (~60 seg)
                sleep(3); // espera entre intentos
                $statusResp = $client->request('GET', $pollingUrl, [
                    'headers' => [
                        'Authorization' => "Bearer {$apiKey}"
                    ]
                ]);

                $status = json_decode($statusResp->getBody(), true);

                if (($status['job']['status'] ?? '') === 'done') {
                    Log::info("Job Speechmatics completado: {$jobId}");
                    break;
                }
            }

            // 5. Descargar el transcript
            $resultsResp = $client->request('GET', "https://asr.api.speechmatics.com/v2/jobs/{$jobId}/transcript", [
                'headers' => [
                    'Authorization' => "Bearer {$apiKey}"
                ]
            ]);

            $results = json_decode($resultsResp->getBody(), true);
            $texto = '';

            // ✅ Extraer solo el texto plano
            if (isset($results['results'])) {
                foreach ($results['results'] as $item) {
                    if (isset($item['alternatives'][0]['content'])) {
                        $texto .= $item['alternatives'][0]['content'];

                        // Agrega espacio si no es puntuación
                        if (($item['type'] ?? '') !== 'punctuation') {
                            $texto .= ' ';
                        }
                    }
                }
                $texto = trim($texto);
            } else {
                // Si no hay results, guarda todo el JSON
                $texto = json_encode($results, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            }

            // 6. Guardar la transcripción en un archivo .txt
            $txtFile = 'transcripciones/' . pathinfo($filename, PATHINFO_FILENAME) . '.txt';
            Storage::disk('local')->put($txtFile, $texto);

            Log::info("Transcripción guardada en: storage/app/{$txtFile}");

            // 7. Respuesta final
            return response()->json([
                'success'       => true,
                'message'       => 'Transcripción completada con éxito.',
                'audio_file'    => $path,
                'transcription' => $texto,
                'txt_file'      => $txtFile
            ]);

        } catch (\Exception $e) {
            Log::error("Excepción al transcribir con Speechmatics: " . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error al procesar el audio.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
}
