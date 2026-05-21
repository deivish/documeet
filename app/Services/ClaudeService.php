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
            'verify'  => false
        ]);
        $this->apiKey = env('CLAUDE_API_KEY');
    }

    // ══════════════════════════════════════════════════════════════
    // 1. EXTRAER COMPROMISOS (sin cambios — funciona bien)
    // ══════════════════════════════════════════════════════════════

    public function extraerCompromisos($transcripcion)
    {
        try {
            Log::info('🤖 Extrayendo compromisos con Claude', [
                'longitud' => strlen($transcripcion),
                'preview'  => substr($transcripcion, 0, 100)
            ]);

            $this->validarApiKey();
            $this->validarTranscripcion($transcripcion);

            $prompt = $this->construirPromptCompromisos($transcripcion);

            $respuesta = $this->llamarClaude($prompt, 4096, 0.2);
            $compromisos = $this->parsearCompromisos($respuesta);

            Log::info('✅ Compromisos extraídos', ['cantidad' => count($compromisos)]);

            return $compromisos;

        } catch (\GuzzleHttp\Exception\ClientException $e) {
            $this->manejarErrorHttp($e);
        } catch (\Exception $e) {
            Log::error('❌ Error extrayendo compromisos: ' . $e->getMessage());
            throw $e;
        }
    }

    // ══════════════════════════════════════════════════════════════
    // 2. GENERAR RESUMEN EJECUTIVO (mejorado)
    // ══════════════════════════════════════════════════════════════

    public function generarResumen($transcripcion)
    {
        try {
            Log::info('📄 Generando resumen ejecutivo con Claude');

            $this->validarApiKey();

            $transcripcionCorta = $this->truncarTranscripcion($transcripcion, 6000);
            $fechaHoy           = now()->format('d/m/Y');

            $prompt = <<<PROMPT
Eres un asistente experto en redacción de actas corporativas. Analiza la siguiente transcripción de una reunión y genera un resumen ejecutivo profesional.

El resumen SIEMPRE debe tener estas secciones, aunque la reunión haya sido corta o informal:

**RESUMEN EJECUTIVO**

**Contexto general:**
Describe brevemente de qué trató la reunión, cuál fue su propósito y el ambiente general de la conversación. (2-3 oraciones)

**Temas principales tratados:**
Lista los temas más importantes que se discutieron durante la reunión. Si no se discutieron temas formales, describe las conversaciones o intercambios relevantes.

**Decisiones tomadas:**
Enumera las decisiones o acuerdos concretos que surgieron. Si no hubo decisiones formales, indica que la reunión fue de carácter informativo o exploratorio.

**Compromisos y próximos pasos:**
Describe las acciones, tareas o compromisos mencionados. Si no se mencionaron explícitamente, indica los temas que quedaron pendientes de definir.

**Observaciones adicionales:**
Cualquier punto relevante que no encaje en las secciones anteriores, como inquietudes expresadas, sugerencias o temas para futuras reuniones.

Fecha de la reunión: $fechaHoy

Reglas importantes:
- Redacta en español, tono formal y profesional
- Nunca dejes una sección vacía — si no hay información, escribe una oración indicando que no aplica o que fue informativa
- El resumen debe ser útil aunque la reunión haya sido breve o sin compromisos formales
- Máximo 400 palabras en total
- No incluyas el JSON de compromisos aquí, solo el resumen narrativo

Transcripción:
$transcripcionCorta

Genera el resumen ahora:
PROMPT;

            $texto = $this->llamarClaude($prompt, 2048, 0.4);

            Log::info('✅ Resumen ejecutivo generado');

            return $texto;

        } catch (\Exception $e) {
            Log::error('❌ Error generando resumen: ' . $e->getMessage());
            return 'No se pudo generar el resumen automáticamente. Por favor redáctalo manualmente.';
        }
    }

    // ══════════════════════════════════════════════════════════════
    // 3. GENERAR ACTA COMPLETA (NUEVO)
    //    Genera el acta estructurada completa en un solo llamado.
    //    Útil cuando se quiere obtener resumen + compromisos juntos.
    // ══════════════════════════════════════════════════════════════

    public function generarActaCompleta($transcripcion, array $datosReunion = [])
    {
        try {
            Log::info('📋 Generando acta completa con Claude');

            $this->validarApiKey();

            $transcripcionCorta = $this->truncarTranscripcion($transcripcion, 6000);
            $fechaHoy           = now()->format('Y-m-d');

            $titulo      = $datosReunion['titulo']      ?? 'Reunión';
            $organizador = $datosReunion['organizador'] ?? 'Sin especificar';
            $fecha       = $datosReunion['fecha']       ?? $fechaHoy;
            $asistentes  = $datosReunion['asistentes']  ?? 'Sin especificar';

            $prompt = <<<PROMPT
Eres un asistente experto en actas corporativas. Analiza la transcripción y genera un acta de reunión completa y profesional en formato JSON.

Datos de la reunión:
- Título: $titulo
- Organizador: $organizador
- Fecha: $fecha
- Asistentes: $asistentes

INSTRUCCIONES:
Devuelve ÚNICAMENTE el siguiente JSON sin texto adicional:

{
  "resumen_ejecutivo": "Párrafo de 3-4 oraciones describiendo el propósito y resultado general de la reunión",
  "temas_tratados": [
    "Tema 1 discutido",
    "Tema 2 discutido"
  ],
  "decisiones": [
    "Decisión o acuerdo 1",
    "Decisión o acuerdo 2"
  ],
  "desarrollo": "Descripción narrativa de 2-3 párrafos de todo lo discutido en la reunión, de forma cronológica y detallada",
  "compromisos": [
    {
      "descripcion": "Descripción clara del compromiso",
      "responsable": "Nombre o 'Sin asignar'",
      "fecha": "YYYY-MM-DD",
      "resultado": "Resultado esperado"
    }
  ],
  "observaciones": "Notas adicionales relevantes o 'Sin observaciones adicionales'",
  "cierre": "Oración de cierre indicando cómo terminó la reunión"
}

REGLAS CRÍTICAS:
1. La fecha de hoy es $fechaHoy — úsala para calcular fechas relativas
2. Si no hay compromisos explícitos, devuelve "compromisos": []
3. Si no hay decisiones formales, escribe "La reunión tuvo carácter informativo"
4. El campo "desarrollo" NUNCA debe estar vacío — describe lo conversado aunque sea informal
5. Responde SOLO con el JSON, sin markdown ni texto antes/después

Transcripción:
$transcripcionCorta
PROMPT;

            $texto = $this->llamarClaude($prompt, 4096, 0.3);

            // Parsear el JSON
            $texto  = str_replace(['```json', '```', '`'], '', $texto);
            $texto  = trim($texto);
            $resultado = json_decode($texto, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::warning('⚠️ JSON del acta no parseó correctamente, usando fallback');
                return $this->actaFallback($transcripcionCorta, $datosReunion);
            }

            Log::info('✅ Acta completa generada');

            return $resultado;

        } catch (\Exception $e) {
            Log::error('❌ Error generando acta completa: ' . $e->getMessage());
            return $this->actaFallback($transcripcion, $datosReunion);
        }
    }

    // ══════════════════════════════════════════════════════════════
    // MÉTODOS PRIVADOS DE APOYO
    // ══════════════════════════════════════════════════════════════

    /**
     * Llamada HTTP a la API de Claude
     */
    protected function llamarClaude(string $prompt, int $maxTokens = 2048, float $temperature = 0.3): string
    {
        $response = $this->client->post('https://api.anthropic.com/v1/messages', [
            'headers' => [
                'x-api-key'         => $this->apiKey,
                'anthropic-version' => '2023-06-01',
                'content-type'      => 'application/json'
            ],
            'json' => [
                'model'       => 'claude-haiku-4-5-20251001',
                'max_tokens'  => $maxTokens,
                'temperature' => $temperature,
                'messages'    => [
                    ['role' => 'user', 'content' => $prompt]
                ]
            ]
        ]);

        $data = json_decode($response->getBody(), true);

        if (!isset($data['content'][0]['text'])) {
            Log::error('❌ Respuesta inesperada de Claude', ['data' => $data]);
            throw new \Exception('No se recibió respuesta válida de Claude');
        }

        return $data['content'][0]['text'];
    }

    /**
     * Prompt para extraer compromisos
     */
    protected function construirPromptCompromisos(string $transcripcion): string
    {
        $fechaHoy = now()->format('Y-m-d');

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

Reglas:
1. La fecha de hoy es $fechaHoy. Úsala para calcular fechas relativas ("el lunes", "esta semana").
2. Si no hay responsable explícito, usa "Sin asignar"
3. Si no hay resultado claro, usa "Completar tarea"
4. Incluye SOLO compromisos reales, no charla informal
5. Si NO hay compromisos, devuelve exactamente: []

Transcripción:
$transcripcion

IMPORTANTE: Responde ÚNICAMENTE con el array JSON.
PROMPT;
    }

    /**
     * Parsear la respuesta de compromisos
     */
    protected function parsearCompromisos(string $texto): array
    {
        try {
            $texto = str_replace(['```json', '```', '`'], '', $texto);
            $texto = trim($texto);

            $compromisos = json_decode($texto, true);

            if (json_last_error() === JSON_ERROR_NONE && is_array($compromisos)) {
                return array_map(fn($c) => [
                    'descripcion' => $c['descripcion'] ?? 'Sin descripción',
                    'responsable' => $c['responsable'] ?? 'Sin asignar',
                    'fecha'       => $c['fecha']       ?? now()->addWeek()->format('Y-m-d'),
                    'resultado'   => $c['resultado']   ?? 'Completar tarea'
                ], $compromisos);
            }

            // Fallback: buscar JSON en el texto
            if (preg_match('/\[.*\]/s', $texto, $matches)) {
                $compromisos = json_decode($matches[0], true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($compromisos)) {
                    return array_map(fn($c) => [
                        'descripcion' => $c['descripcion'] ?? 'Sin descripción',
                        'responsable' => $c['responsable'] ?? 'Sin asignar',
                        'fecha'       => $c['fecha']       ?? now()->addWeek()->format('Y-m-d'),
                        'resultado'   => $c['resultado']   ?? 'Completar tarea'
                    ], $compromisos);
                }
            }

            Log::warning('⚠️ No se pudo parsear compromisos', ['texto' => substr($texto, 0, 300)]);
            return [];

        } catch (\Exception $e) {
            Log::error('❌ Error parseando compromisos: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Acta de fallback si Claude falla
     */
    protected function actaFallback(string $transcripcion, array $datos): array
    {
        return [
            'resumen_ejecutivo'  => 'Se realizó la reunión programada. La transcripción fue registrada correctamente.',
            'temas_tratados'     => ['Reunión realizada — revisar transcripción para detalles'],
            'decisiones'         => ['Sin decisiones formales registradas'],
            'desarrollo'         => $transcripcion,
            'compromisos'        => [],
            'observaciones'      => 'El resumen automático no pudo generarse. Consultar la transcripción completa.',
            'cierre'             => 'Reunión finalizada.'
        ];
    }

    /**
     * Truncar transcripción para no exceder tokens
     */
    protected function truncarTranscripcion(string $transcripcion, int $maxChars = 6000): string
    {
        return strlen($transcripcion) > $maxChars
            ? substr($transcripcion, 0, $maxChars) . '...[transcripción truncada]'
            : $transcripcion;
    }

    /**
     * Validar que la API key esté configurada
     */
    protected function validarApiKey(): void
    {
        if (empty($this->apiKey)) {
            throw new \Exception('CLAUDE_API_KEY no está configurada en .env');
        }
    }

    /**
     * Validar que la transcripción no esté vacía
     */
    protected function validarTranscripcion(string $transcripcion): void
    {
        if (empty($transcripcion) || strlen($transcripcion) < 20) {
            throw new \Exception('La transcripción está vacía o es muy corta');
        }
    }

    /**
     * Manejar errores HTTP de Guzzle
     */
    protected function manejarErrorHttp(\GuzzleHttp\Exception\ClientException $e): void
    {
        $status = $e->getResponse()->getStatusCode();
        $body   = json_decode($e->getResponse()->getBody(), true);

        Log::error('❌ Error HTTP de Claude', ['status' => $status, 'error' => $body]);

        match ($status) {
            401 => throw new \Exception('API Key de Claude inválida'),
            429 => throw new \Exception('Límite de peticiones excedido. Espera unos minutos'),
            default => throw new \Exception('Error de Claude: ' . ($body['error']['message'] ?? 'Desconocido'))
        };
    }
}