<?php

namespace App\Http\Controllers;

use App\Models\Reunion;
use App\Services\DailyService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class VideoCallController extends Controller
{
    public function join(Reunion $reunion)
    {
        // Validar acceso
        if ($reunion->user_id !== Auth::id() && !$reunion->invitados->contains(Auth::id())) {
            abort(403, 'No tienes permiso para acceder a esta reunión');
        }

        try {
            // Verificar si ya existe una sala activa
            if ($reunion->daily_url && $reunion->daily_expires_at && $reunion->daily_expires_at > now()) {
                // Usar sala existente
                $dailyUrl = $reunion->daily_url;
                $roomName = $reunion->daily_room_name;
                
                Log::info('Reutilizando sala Daily.co existente', [
                    'reunion_id' => $reunion->id,
                    'sala' => $roomName
                ]);
            } else {
                // Crear nueva sala en Daily.co
                $dailyService = new DailyService();
                
                // Nombre único de la sala
                $nombreSala = 'reunion-' . $reunion->id . '-' . uniqid();
                
                // Crear sala con duración de 3 horas
                $sala = $dailyService->crearSala($nombreSala, 3);
                
                // Guardar URL y expiración en la base de datos
                $reunion->update([
                    'daily_url' => $sala['url'],
                    'daily_room_name' => $sala['nombre'],
                    'daily_expires_at' => now()->addHours(3)
                ]);
                
                $dailyUrl = $sala['url'];
                $roomName = $sala['nombre'];
                
                Log::info('Nueva sala Daily.co creada', [
                    'reunion_id' => $reunion->id,
                    'sala' => $nombreSala,
                    'url' => $dailyUrl
                ]);
            }
            
            // Nombre del usuario
            $userName = Auth::user()->name ?? Auth::user()->fullName ?? Auth::user()->email;
            
            Log::info('Usuario accediendo a videollamada Daily.co', [
                'usuario' => $userName,
                'reunion_id' => $reunion->id
            ]);

            // Crear acta en borrador automáticamente si no existe
            if (!$reunion->acta) {
                $reunion->acta()->create([
                    'contenido' => '',
                    'estado' => 'borrador',
                    'creada_por' => Auth::id(),
                ]);
                
                Log::info('Acta borrador creada automáticamente', [
                    'reunion_id' => $reunion->id
                ]);
            }

            return view('reuniones.videollamada', [
                'reunion' => $reunion,
                'userName' => $userName,
                'dailyUrl' => $dailyUrl,
                'roomName' => $roomName
            ]);

        } catch (\Exception $e) {
            Log::error('Error al crear videollamada Daily.co', [
                'error' => $e->getMessage(),
                'reunion_id' => $reunion->id,
                'usuario_id' => Auth::id()
            ]);
            
            return back()->with('error', 'Error al iniciar videollamada. Por favor intenta de nuevo.');
        }
    }
}