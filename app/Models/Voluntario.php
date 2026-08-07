<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Voluntario extends Model
{
    use HasFactory;

    protected $table = 'voluntarios';
    protected $primaryKey = 'id_voluntario';
    public $incrementing = false; // comparte PK con usuarios (herencia)
    protected $keyType = 'int';
    public $timestamps = false;

    protected $fillable = [
        'id_voluntario',
        'matricula_universitaria',
        'facultad',
        'carrera',
        'ciclo',
        'codigo_qr',
        'horas_acumuladas',
        'fecha_graduacion',
        'disponibilidad',
        'habilidades',
        'fecha_nacimiento',
        'direccion',
    ];

    protected $casts = [
        'horas_acumuladas' => 'decimal:2',
        'fecha_graduacion' => 'date',
        'fecha_nacimiento' => 'date',
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_voluntario', 'id_usuario');
    }

    public function inscripciones()
    {
        return $this->hasMany(Inscripcion::class, 'id_voluntario', 'id_voluntario');
    }

    public function certificados()
    {
        return $this->hasMany(Certificado::class, 'id_voluntario', 'id_voluntario');
    }

    public function puntoEcologico()
    {
        return $this->hasOne(PuntoEcologico::class, 'id_voluntario', 'id_voluntario');
    }
}
