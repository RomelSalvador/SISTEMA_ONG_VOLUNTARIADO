<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HoraVoluntariado extends Model
{
    use HasFactory;

    protected $table = 'horas_voluntariado';
    protected $primaryKey = 'id_hora';
    public $timestamps = false;

    protected $fillable = [
        'id_inscripcion',
        'horas_calculadas',
        'fecha_actividad',
        'hora_inicio',
        'hora_fin',
        'descripcion_actividad',
        'aprobado_por',
        'estado',
        'fecha_aprobacion',
        'comentario_aprobacion',
    ];

    protected $casts = [
        'horas_calculadas' => 'decimal:2',
        'fecha_actividad' => 'date',
        'fecha_aprobacion' => 'datetime',
    ];

    public function inscripcion()
    {
        return $this->belongsTo(Inscripcion::class, 'id_inscripcion', 'id_inscripcion');
    }

    public function aprobadoPor()
    {
        return $this->belongsTo(Organizador::class, 'aprobado_por', 'id_organizador');
    }
}
