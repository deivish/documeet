<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Compromiso extends Model
{
    //
    protected $fillable = ['reunion_id','descripcion','responsable','fecha','resultado', 'estado'];

    protected $casts = [
    'fecha' => 'date',
    ];

    public function reunion() {
        return $this->belongsTo(Reunion::class);
    }

    public function seguimientos()
    {
        return $this->hasMany(CompromisoSeguimiento::class);
    }

}
