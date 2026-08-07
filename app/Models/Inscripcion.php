<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inscripcion extends Model
{
    use HasFactory;

    protected $table = 'inscripciones';
    protected $primaryKey = 'id_inscripcion';
    public $timestamps = false;

    protected $fillable = [
        'id_voluntario',
        'id_campana',
        'id_actividad',
        'fecha_inscripcion',
        'estado',
        'asistencia_confirmada',
        'horas_acreditadas',
        'comentarios',
        'fecha_aprobacion',
        'fecha_cancelacion',
        'motivo_cancelacion',
        'calificacion_voluntario',
    ];

    protected $casts = [
        'fecha_inscripcion' => 'datetime',
        'asistencia_confirmada' => 'boolean',
        'horas_acreditadas' => 'decimal:2',
        'fecha_aprobacion' => 'datetime',
        'fecha_cancelacion' => 'datetime',
    ];

    public function voluntario()
    {
        return $this->belongsTo(Voluntario::class, 'id_voluntario', 'id_voluntario');
    }

    public function campana()
    {
        return $this->belongsTo(Campana::class, 'id_campana', 'id_campana');
    }

    public function actividad()
    {
        return $this->belongsTo(Actividad::class, 'id_actividad', 'id_actividad');
    }

    public function asistencias()
    {
        return $this->hasMany(Asistencia::class, 'id_inscripcion', 'id_inscripcion');
    }

    public function horasVoluntariado()
    {
        return $this->hasMany(HoraVoluntariado::class, 'id_inscripcion', 'id_inscripcion');
    }

    public function evaluacion()
    {
        return $this->hasOne(EvaluacionCampana::class, 'id_inscripcion', 'id_inscripcion');
    }
}
