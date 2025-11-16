<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class ClaudeService
{
    protected $client;
    protected $apiKey;

    public function __construct()
    {
        $this->client = new Client([
            'timeout' => 60,
            'verify' => false
        ]);
        $this->apiKey = env('CLAUDE_API_KEY');
    }

    public function extraerCompromisos($transcripcion)
    {
        try {
            Log::info('🤖 Extrayendo compromisos con Claude', [
                'longitud' => strlen($transcripcion),
                'preview' => substr($transcripcion, 0, 100)
            ]);

            if (empty($this->apiKey)) {
                throw new \Exception('CLAUDE_API_KEY no está configurada en .env');
            }

            if (empty($transcripcion) || strlen($transcripcion) < 20) {
                throw new \Exception('La transcripción está vacía o es muy corta');
            }

            $prompt = $this->construirPrompt($transcripcion);

            $response = $this->client->post('https://api.anthropic.com/v1/messages', [
                'headers' => [
                    'x-api-key' => $this->apiKey,
                    'anthropic-version' => '2023-06-01',
                    'content-type' => 'application/json'
                ],
                'json' => [
                    'model' => 'claude-3-haiku-20240307', // Más barato y rápido
                    'max_tokens' => 4096,
                    'temperature' => 0.2,
                    'messages' => [
                        [
                            'role' => 'user',
                            'content' => $prompt
                        ]
                    ]
                ]
            ]);

            $data = json_decode($response->getBody(), true);
            
            Log::info('✅ Respuesta de Claude recibida', [
                'tiene_contenido' => isset($data['content'][0]['text']),
                'stop_reason' => $data['stop_reason'] ?? 'N/A'
            ]);

            if (!isset($data['content'][0]['text'])) {
                Log::error('❌ No se recibió texto de Claude', ['data' => $data]);
                throw new \Exception('No se recibió respuesta válida de Claude');
            }

            $textoRespuesta = $data['content'][0]['text'];

            Log::info('📝 Texto recibido de Claude', [
                'longitud' => strlen($textoRespuesta),
                'texto' => $textoRespuesta
            ]);

            $compromisos = $this->parsear($textoRespuesta);

            Log::info('✅ Compromisos extraídos exitosamente', [
                'cantidad' => count($compromisos),
                'compromisos' => $compromisos
            ]);

            return $compromisos;

        } catch (\GuzzleHttp\Exception\ClientException $e) {
            $response = $e->getResponse();
            $body = json_decode($response->getBody(), true);
            
            Log::error('❌ Error HTTP de Claude', [
                'status' => $response->getStatusCode(),
                'error' => $body
            ]);

            $statusCode = $response->getStatusCode();
            
            if ($statusCode === 401) {
                throw new \Exception('API Key de Claude inválida. Verifica tu key en https://console.anthropic.com');
            }

            if ($statusCode === 429) {
                throw new \Exception('Límite de peticiones excedido. Espera unos minutos');
            }

            throw new \Exception('Error de Claude: ' . ($body['error']['message'] ?? 'Desconocido'));

        } catch (\Exception $e) {
            Log::error('❌ Error general con Claude', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            throw $e;
        }
    }

    protected function construirPrompt($transcripcion)
    {
        return <<<PROMPT
Analiza esta transcripción de una reunión y extrae TODOS los compromisos mencionados.

Formato JSON esperado:
[
  {
    "descripcion": "Descripción clara del compromiso",
    "responsable": "Nombre de la persona responsable",
    "fecha": "YYYY-MM-DD",
    "resultado": "Resultado esperado"
  }
]

Reglas importantes:
1. Si no se menciona fecha específica, infiere una fecha razonable (ej: 2025-11-20)
2. Si no hay responsable explícito, usa "Sin asignar"
3. Si no hay resultado claro, usa "Completar tarea"
4. Incluye SOLO compromisos reales (no saludos ni charla informal)
5. Si NO hay compromisos, devuelve un array vacío: []

Transcripción:
$transcripcion

IMPORTANTE: Responde ÚNICAMENTE con el array JSON, sin texto adicional antes o después.
PROMPT;
    }

    protected function parsear($texto)
    {
        try {
            // Limpiar texto
            $texto = str_replace(['```json', '```', '`'], '', $texto);
            $texto = trim($texto);

            Log::info('🔍 Parseando respuesta de Claude', [
                'texto_limpio' => $texto
            ]);

            // Decodificar JSON directamente
            $compromisos = json_decode($texto, true);

            if (json_last_error() === JSON_ERROR_NONE && is_array($compromisos)) {
                Log::info('✅ JSON parseado directamente');
                
                return array_map(function($c) {
                    return [
                        'descripcion' => $c['descripcion'] ?? 'Sin descripción',
                        'responsable' => $c['responsable'] ?? 'Sin asignar',
                        'fecha' => $c['fecha'] ?? now()->addWeek()->format('Y-m-d'),
                        'resultado' => $c['resultado'] ?? 'Completar tarea'
                    ];
                }, $compromisos);
            }

            // Buscar JSON en el texto
            if (preg_match('/\[.*\]/s', $texto, $matches)) {
                Log::info('🔍 JSON encontrado con regex');
                
                $compromisos = json_decode($matches[0], true);
                
                if (json_last_error() === JSON_ERROR_NONE && is_array($compromisos)) {
                    return array_map(function($c) {
                        return [
                            'descripcion' => $c['descripcion'] ?? 'Sin descripción',
                            'responsable' => $c['responsable'] ?? 'Sin asignar',
                            'fecha' => $c['fecha'] ?? now()->addWeek()->format('Y-m-d'),
                            'resultado' => $c['resultado'] ?? 'Completar tarea'
                        ];
                    }, $compromisos);
                }
            }

            Log::warning('⚠️ No se pudo parsear JSON', [
                'error' => json_last_error_msg(),
                'texto' => substr($texto, 0, 500)
            ]);

            return [];

        } catch (\Exception $e) {
            Log::error('❌ Error parseando respuesta: ' . $e->getMessage());
            return [];
        }
    }

    public function generarResumen($transcripcion)
    {
        try {
            Log::info('📄 Generando resumen con Claude');

            if (empty($this->apiKey)) {
                throw new \Exception('CLAUDE_API_KEY no configurada');
            }

            // Limitar longitud
            $transcripcionCorta = strlen($transcripcion) > 4000 
                ? substr($transcripcion, 0, 4000) 
                : $transcripcion;

            $response = $this->client->post('https://api.anthropic.com/v1/messages', [
                'headers' => [
                    'x-api-key' => $this->apiKey,
                    'anthropic-version' => '2023-06-01',
                    'content-type' => 'application/json'
                ],
                'json' => [
                    'model' => 'claude-3-haiku-20240307',
                    'max_tokens' => 2048,
                    'temperature' => 0.5,
                    'messages' => [
                        [
                            'role' => 'user',
                            'content' => "Resume esta reunión en 3 párrafos, destacando:\n1. Temas principales discutidos\n2. Decisiones tomadas\n3. Próximos pasos\n\nTranscripción:\n" . $transcripcionCorta
                        ]
                    ]
                ]
            ]);

            $data = json_decode($response->getBody(), true);
            
            Log::info('✅ Resumen generado');

            return $data['content'][0]['text'] ?? 'No se pudo generar el resumen';

        } catch (\Exception $e) {
            Log::error('❌ Error generando resumen: ' . $e->getMessage());
            return 'Error al generar el resumen';
        }
    }
}