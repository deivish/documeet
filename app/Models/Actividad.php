<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Actividad extends Model
{
    //
    use SoftDeletes;

    protected $table = 'actividades';

    protected $fillable = ['reunion_id', 'nombre', 'descripcion', 'responsable', 'fecha_entrega', 'estado'];

    protected $casts = [
    'fecha_entrega' => 'date',
    ];

    public function reunion()
    {
        return $this->belongsTo(Reunion::class);
    }
}
