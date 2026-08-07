<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Asistencia extends Model
{
    use HasFactory;

    protected $table = 'asistencia';
    protected $primaryKey = 'id_asistencia';
    public $timestamps = false;

    protected $fillable = [
        'id_inscripcion',
        'hora_ingreso',
        'hora_salida',
        'fecha_asistencia',
        'metodo_verificacion',
        'latitud_checkin',
        'longitud_checkin',
        'latitud_checkout',
        'longitud_checkout',
        'observacion',
        'registrado_por',
        'fecha_registro',
        'horas_calculadas',
        'estado_asistencia',
    ];

    protected $casts = [
        'fecha_asistencia' => 'date',
        'latitud_checkin' => 'decimal:8',
        'longitud_checkin' => 'decimal:8',
        'latitud_checkout' => 'decimal:8',
        'longitud_checkout' => 'decimal:8',
        'fecha_registro' => 'datetime',
        'horas_calculadas' => 'decimal:2',
    ];

    public function inscripcion()
    {
        return $this->belongsTo(Inscripcion::class, 'id_inscripcion', 'id_inscripcion');
    }

    public function registradoPor()
    {
        return $this->belongsTo(Usuario::class, 'registrado_por', 'id_usuario');
    }
}
