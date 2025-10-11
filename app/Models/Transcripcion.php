<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transcripcion extends Model
{   
    protected $table = 'transcripciones';
    protected $fillable = ['archivo', 'texto'];
    public function reunion(){ return $this->belongsTo(Reunion::class); }
    public function autor(){ return $this->belongsTo(\App\Models\User::class, 'user_id'); }

}
