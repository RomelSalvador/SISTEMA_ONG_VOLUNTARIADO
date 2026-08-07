<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comunicado extends Model
{
    use HasFactory;

    protected $table = 'comunicados';
    protected $primaryKey = 'id_comunicado';
    public $timestamps = false;

    protected $fillable = [
        'titulo',
        'contenido',
        'imagen',
        'fecha_publicacion',
        'fecha_expiracion',
        'estado',
        'publico',
        'categoria',
        'autor',
        'visitas',
    ];

    protected $casts = [
        'fecha_publicacion' => 'datetime',
        'fecha_expiracion' => 'datetime',
        'publico' => 'boolean',
    ];
}
