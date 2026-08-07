<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PuntoEcologico extends Model
{
    use HasFactory;

    protected $table = 'puntos_ecologicos';
    protected $primaryKey = 'id_punto';
    public $timestamps = false;

    protected $fillable = [
        'id_voluntario',
        'puntos',
        'nivel',
        'fecha_actualizacion',
        'puntos_acumulados_mes',
        'ultimo_logro',
    ];

    protected $casts = [
        'fecha_actualizacion' => 'datetime',
    ];

    public function voluntario()
    {
        return $this->belongsTo(Voluntario::class, 'id_voluntario', 'id_voluntario');
    }
}
