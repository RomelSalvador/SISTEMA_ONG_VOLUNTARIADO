<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Campana extends Model
{
    use HasFactory;

    protected $table = 'campanas';
    protected $primaryKey = 'id_campana';

    // La tabla usa columnas personalizadas para timestamps
    public $timestamps = true;
    const CREATED_AT = 'fecha_creacion';
    const UPDATED_AT = 'fecha_modificacion';

    protected $fillable = [
        'id_organizador',
        'nombre',
        'descripcion',
        'lugar',
        'latitud',
        'longitud',
        'fecha_inicio',
        'fecha_fin',
        'hora_inicio',
        'hora_fin',
        'capacidad_max',
        'meta_voluntarios',
        'id_categoria',
        'requisitos',
        'imagen_banner',
        'cronograma',
        'estado',
        'puntos_ecologicos',
        'impacto_ambiental',
        'impacto_social',
        'presupuesto_estimado',
        'patrocinadores',
    ];

    protected $casts = [
        'latitud' => 'decimal:8',
        'longitud' => 'decimal:8',
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
        'presupuesto_estimado' => 'decimal:2',
    ];

    public function organizador()
    {
        return $this->belongsTo(Organizador::class, 'id_organizador', 'id_organizador');
    }

    public function categoria()
    {
        return $this->belongsTo(CategoriaCampana::class, 'id_categoria', 'id_categoria');
    }

    public function actividades()
    {
        return $this->hasMany(Actividad::class, 'id_campana', 'id_campana');
    }

    public function inscripciones()
    {
        return $this->hasMany(Inscripcion::class, 'id_campana', 'id_campana');
    }

    public function certificados()
    {
        return $this->hasMany(Certificado::class, 'id_campana', 'id_campana');
    }

    public function materiales()
    {
        return $this->hasMany(MaterialCampana::class, 'id_campana', 'id_campana');
    }
}
