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
    ];

    /**
     * Relación con el organizador (usuario).
     */
    public function organizador()
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



}
