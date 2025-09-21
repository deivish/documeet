<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Compromiso extends Model
{
    //
    protected $fillable = ['reunion_id','descripcion','responsable','fecha','resultado'];

    public function reunion() {
        return $this->belongsTo(Reunion::class);
    }
}
