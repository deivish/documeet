<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Reunion extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'reunions';

    protected $fillable = [
        'titulo',
        'descripcion',
        'fecha_hora',
        'user_id',
        'daily_url',           // ← AGREGADO para Daily.co
        'daily_room_name',     // ← AGREGADO para Daily.co
        'daily_expires_at',     // ← AGREGADO para Daily.co
        'reunion_padre_id'
    ];

    protected $casts = [
        'fecha_hora' => 'datetime',
        'daily_expires_at' => 'datetime'  // ← AGREGADO
    ];

    /**
     * Relación con el organizador (usuario).
     */
    public function organizador()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Alias para organizador (para compatibilidad con código existente)
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Invitados a la reunión (usuarios)
    public function invitados()
    {
        return $this->belongsToMany(User::class, 'reunion_user')
                ->withPivot('rol') // permite acceder al campo 'rol'
                ->withTimestamps();
    }

    public function actividades()
    {
        return $this->hasMany(Actividad::class);
    }

    public function transcripciones()
    {
        return $this->hasMany(\App\Models\Transcripcion::class);
    }

    public function actas()
    {
        return $this->hasMany(\App\Models\Acta::class);
    }

    public function acta()
    {
        return $this->hasOne(Acta::class);
    }

    public function compromisos() 
    {
        return $this->hasMany(Compromiso::class);
    }

    public function reunionPadre()
    {
        return $this->belongsTo(Reunion::class, 'reunion_padre_id');
    }

    public function reunionesHijas()
    {
        return $this->hasMany(Reunion::class, 'reunion_padre_id');
    }

    public function auditoria()
    {
        return $this->hasMany(Auditoria::class);
    }

    // ← AGREGADO para asistencias (si las usas)
    // public function asistencias()
    // {
    //     return $this->hasMany(Asistencia::class);
    // }
}