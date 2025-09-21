<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Acta extends Model
{
    //
    protected $table = 'actas';
    protected $fillable = [
        'reunion_id','numero','estado','resumen','desarrollo',
        'redactada_por','aprobada_por','aprobada_at','archivo_pdf','archivo_docx'
    ];

    public function reunion(){ return $this->belongsTo(Reunion::class); }
    public function redactadaPor(){ return $this->belongsTo(\App\Models\User::class,'redactada_por'); }
    public function aprobadaPor(){ return $this->belongsTo(\App\Models\User::class,'aprobada_por'); }
}
