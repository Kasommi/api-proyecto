<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Viaje extends Model
{
    protected $fillable = [
        'titulo',
        'descripcion',
        'imagen',
        'latitud',
        'longitud',
        'fecha_visita',
        'firebase_uid',
        'firebase_email',
    ];

    protected $casts = [
        'fecha_visita' => 'date',
    ];
}