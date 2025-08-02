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


}
