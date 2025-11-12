<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class DailyService
{
    protected $client;
    protected $apiKey;

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => 'https://api.daily.co/v1/',
            'timeout' => 10,
            'verify' => false // ← Deshabilitar verificación SSL (solo desarrollo)
        ]);
        $this->apiKey = env('DAILY_API_KEY');
    }

    /**
     * Crear sala de videollamada
     */
    public function crearSala($nombre, $duracionHoras = 3)
    {
        try {
            Log::info('Intentando crear sala Daily.co', [
                'nombre' => $nombre,
                'api_key_presente' => !empty($this->apiKey)
            ]);

            $response = $this->client->post('rooms', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type' => 'application/json'
                ],
                'json' => [
                    'name' => $nombre,
                    'privacy' => 'public',
                    'properties' => [
                        'exp' => time() + ($duracionHoras * 3600),
                        'enable_chat' => true,
                        'enable_screenshare' => true,
                        'enable_recording' => 'local',
                        'enable_prejoin_ui' => false,
                        'max_participants' => 20,
                        'start_audio_off' => false,
                        'start_video_off' => false
                    ]
                ]
            ]);

            $data = json_decode($response->getBody(), true);
            
            Log::info('✅ Sala Daily.co creada exitosamente', [
                'nombre' => $data['name'],
                'url' => $data['url']
            ]);

            return [
                'nombre' => $data['name'],
                'url' => $data['url'],
                'expiracion' => $data['config']['exp'] ?? time() + ($duracionHoras * 3600)
            ];

        } catch (\GuzzleHttp\Exception\ClientException $e) {
            $statusCode = $e->getResponse()->getStatusCode();
            $body = $e->getResponse()->getBody()->getContents();
            
            Log::error('❌ Error HTTP de Daily.co', [
                'status' => $statusCode,
                'respuesta' => $body,
                'nombre' => $nombre
            ]);
            
            if ($statusCode == 401) {
                throw new \Exception('API Key de Daily.co inválida');
            }
            
            throw new \Exception('Error al crear sala: ' . $body);
            
        } catch (\Exception $e) {
            Log::error('❌ Error creando sala Daily.co', [
                'error' => $e->getMessage(),
                'nombre' => $nombre,
                'trace' => $e->getTraceAsString()
            ]);
            
            throw new \Exception('No se pudo crear la videollamada: ' . $e->getMessage());
        }
    }

    /**
     * Obtener información de una sala
     */
    public function obtenerSala($nombreSala)
    {
        try {
            $response = $this->client->get("rooms/{$nombreSala}", [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->apiKey
                ]
            ]);

            return json_decode($response->getBody(), true);

        } catch (\Exception $e) {
            Log::error('Error obteniendo sala: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Eliminar sala
     */
    public function eliminarSala($nombreSala)
    {
        try {
            $this->client->delete("rooms/{$nombreSala}", [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->apiKey
                ]
            ]);

            Log::info('Sala Daily.co eliminada', ['nombre' => $nombreSala]);
            return true;

        } catch (\Exception $e) {
            Log::warning('Error eliminando sala: ' . $e->getMessage());
            return false;
        }
    }
}