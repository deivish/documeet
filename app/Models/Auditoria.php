<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Auditoria extends Model
{
    protected $table = 'auditoria';

    protected $fillable = [
        'reunion_id', 'user_id', 'accion',
        'modelo', 'modelo_id',
        'valores_anteriores', 'valores_nuevos'
    ];

    protected $casts = [
        'valores_anteriores' => 'array',
        'valores_nuevos'     => 'array',
    ];

    public function reunion()
    {
        return $this->belongsTo(Reunion::class);
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}