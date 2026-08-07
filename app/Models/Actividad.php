<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Actividad extends Model
{
    use HasFactory;

    protected $table = 'actividades';
    protected $primaryKey = 'id_actividad';
    public $timestamps = false;

    protected $fillable = [
        'id_campana',
        'nombre',
        'descripcion',
        'fecha',
        'hora_inicio',
        'hora_fin',
        'capacidad_max',
        'estado',
        'responsable',
        'observaciones',
        'duracion_estimada',
        'requiere_materiales',
    ];

    protected $casts = [
        'fecha' => 'date',
        'requiere_materiales' => 'boolean',
    ];

    public function campana()
    {
        return $this->belongsTo(Campana::class, 'id_campana', 'id_campana');
    }

    public function inscripciones()
    {
        return $this->hasMany(Inscripcion::class, 'id_actividad', 'id_actividad');
    }
}
