<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class DeepgramService
{
    protected $client;
    protected $apiKey;

    public function __construct()
    {
        $this->client = new Client([
            'timeout' => 60,
            'verify' => false // Deshabilitar SSL (desarrollo)
        ]);
        $this->apiKey = env('DEEPGRAM_API_KEY');
    }

    public function transcribir($audioPath)
    {
        try {
            // Normalizar la ruta para Windows
            $audioPath = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $audioPath);
            
            Log::info('Iniciando transcripción Deepgram', [
                'archivo' => basename($audioPath),
                'ruta_normalizada' => $audioPath,
                'existe' => file_exists($audioPath),
                'tamaño' => file_exists($audioPath) ? filesize($audioPath) : 0,
                'api_key_configurada' => !empty($this->apiKey)
            ]);

            if (!file_exists($audioPath)) {
                throw new \Exception('Archivo de audio no encontrado: ' . $audioPath);
            }

            if (empty($this->apiKey)) {
                throw new \Exception('DEEPGRAM_API_KEY no está configurada');
            }

            // Verificar que el archivo tenga contenido
            $fileSize = filesize($audioPath);
            if ($fileSize < 1000) {
                Log::warning('Archivo muy pequeño para transcribir', ['tamaño' => $fileSize]);
                return null;
            }

            $response = $this->client->post('https://api.deepgram.com/v1/listen', [
                'headers' => [
                    'Authorization' => 'Token ' . $this->apiKey,
                    'Content-Type' => 'audio/webm',
                ],
                'query' => [
                    'model' => 'nova-2',
                    'language' => 'es',
                    'punctuate' => 'true',
                    'smart_format' => 'true',
                    'diarize' => 'false', // Desactivar diarización para mejor rendimiento
                    'utterances' => 'false',
                    'detect_language' => 'false', // Ya sabemos que es español
                    'filler_words' => 'false',
                    'interim_results' => 'false'
                ],
                'body' => fopen($audioPath, 'rb')
            ]);

            $data = json_decode($response->getBody(), true);
            
            // Log de respuesta completa para debugging
            Log::info('Respuesta completa de Deepgram', [
                'respuesta' => json_encode($data, JSON_PRETTY_PRINT)
            ]);

            $texto = $data['results']['channels'][0]['alternatives'][0]['transcript'] ?? null;

            // Verificar si Deepgram detectó algo
            $confidence = $data['results']['channels'][0]['alternatives'][0]['confidence'] ?? 0;
            
            Log::info('Resultado de transcripción', [
                'texto_length' => strlen($texto ?? ''),
                'confidence' => $confidence,
                'texto_preview' => substr($texto ?? '', 0, 100),
                'hay_texto' => !empty($texto)
            ]);

            // Si no hay texto pero sí confianza, retornar mensaje
            if (empty($texto) && $confidence > 0) {
                return '[Audio detectado pero sin palabras claras]';
            }

            return $texto;

        } catch (\GuzzleHttp\Exception\ClientException $e) {
            $statusCode = $e->getResponse()->getStatusCode();
            $body = $e->getResponse()->getBody()->getContents();
            
            Log::error('Error HTTP de Deepgram', [
                'status' => $statusCode,
                'respuesta' => $body,
                'archivo' => basename($audioPath ?? 'unknown')
            ]);
            
            if ($statusCode == 401) {
                throw new \Exception('API Key de Deepgram inválida');
            }
            
            return null;
            
        } catch (\Exception $e) {
            Log::error('Error Deepgram: ' . $e->getMessage(), [
                'archivo' => basename($audioPath ?? 'unknown')
            ]);
            return null;
        }
    }
}