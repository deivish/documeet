<?php

namespace App\Helpers;

use App\Models\Auditoria;
use Illuminate\Support\Facades\Auth;

class AuditoriaHelper
{
    /**
     * Registrar un evento de auditoría.
     *
     * @param  string      $accion            Descripción de la acción ("editó el título", "eliminó actividad")
     * @param  string      $modelo            Nombre del modelo afectado ('Reunion', 'Actividad', 'Compromiso', 'Acta')
     * @param  int|null    $modeloId          ID del registro afectado
     * @param  int|null    $reunionId         ID de la reunión relacionada
     * @param  array|null  $valoresAnteriores Valores antes del cambio
     * @param  array|null  $valoresNuevos     Valores después del cambio
     */
    public static function registrar(
        string $accion,
        string $modelo,
        ?int   $modeloId    = null,
        ?int   $reunionId   = null,
        ?array $valoresAnteriores = null,
        ?array $valoresNuevos     = null
    ): void {
        try {
            Auditoria::create([
                'reunion_id'         => $reunionId,
                'user_id'            => Auth::id(),
                'accion'             => $accion,
                'modelo'             => $modelo,
                'modelo_id'          => $modeloId,
                'valores_anteriores' => $valoresAnteriores,
                'valores_nuevos'     => $valoresNuevos,
            ]);
        } catch (\Exception $e) {
            // La auditoría nunca debe interrumpir el flujo principal
            \Log::warning('⚠️ Error registrando auditoría: ' . $e->getMessage());
        }
    }

    /**
     * Registrar edición comparando valores anteriores vs nuevos automáticamente.
     */
    public static function registrarEdicion(
        string $modelo,
        $modeloInstance,
        array  $camposAuditados,
        ?int   $reunionId = null
    ): void {
        $anteriores = [];
        $nuevos     = [];

        foreach ($camposAuditados as $campo) {
            $valorOriginal = $modeloInstance->getOriginal($campo);
            $valorNuevo    = $modeloInstance->getAttribute($campo);

            if ($valorOriginal !== $valorNuevo) {
                $anteriores[$campo] = $valorOriginal;
                $nuevos[$campo]     = $valorNuevo;
            }
        }

        if (!empty($nuevos)) {
            $cambiados = implode(', ', array_keys($nuevos));
            self::registrar(
                "Editó {$modelo}: {$cambiados}",
                $modelo,
                $modeloInstance->id,
                $reunionId,
                $anteriores,
                $nuevos
            );
        }
    }
}