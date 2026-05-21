<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompromisoSeguimiento extends Model
{
    protected $table = 'compromiso_seguimiento';

    protected $fillable = [
        'compromiso_id', 'reunion_id', 'estado', 'nota'
    ];

    public function compromiso()
    {
        return $this->belongsTo(Compromiso::class);
    }

    public function reunion()
    {
        return $this->belongsTo(Reunion::class);
    }
}