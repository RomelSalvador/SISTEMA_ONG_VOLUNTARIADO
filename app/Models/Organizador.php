<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Organizador extends Model
{
    use HasFactory;

    protected $table = 'organizadores';
    protected $primaryKey = 'id_organizador';
    public $incrementing = false; // comparte PK con usuarios (herencia)
    protected $keyType = 'int';
    public $timestamps = false;

    protected $fillable = [
        'id_organizador',
        'ong_nombre',
        'telefono_emergencia',
        'puesto',
        'fecha_contratacion',
        'departamento',
    ];

    protected $casts = [
        'fecha_contratacion' => 'date',
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_organizador', 'id_usuario');
    }

    public function campanas()
    {
        return $this->hasMany(Campana::class, 'id_organizador', 'id_organizador');
    }

    public function horasAprobadas()
    {
        return $this->hasMany(HoraVoluntariado::class, 'aprobado_por', 'id_organizador');
    }
}
