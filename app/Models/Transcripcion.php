<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transcripcion extends Model
{
    protected $table = 'transcripciones';
    
    protected $fillable = [
        'reunion_id',
        'user_id',
        'contenido',
        'fuente'
    ];

    // Relaciones
    public function reunion()
    {
        return $this->belongsTo(Reunion::class);
    }

    public function autor()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}