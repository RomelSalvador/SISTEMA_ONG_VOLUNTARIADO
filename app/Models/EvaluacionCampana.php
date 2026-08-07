<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EvaluacionCampana extends Model
{
    use HasFactory;

    protected $table = 'evaluaciones_campana';
    protected $primaryKey = 'id_evaluacion';
    public $timestamps = false;

    protected $fillable = [
        'id_inscripcion',
        'puntuacion',
        'comentario',
        'recomendaciones',
        'fecha_evaluacion',
        'aspectos_positivos',
        'aspectos_mejorar',
    ];

    protected $casts = [
        'fecha_evaluacion' => 'datetime',
    ];

    public function inscripcion()
    {
        return $this->belongsTo(Inscripcion::class, 'id_inscripcion', 'id_inscripcion');
    }
}
