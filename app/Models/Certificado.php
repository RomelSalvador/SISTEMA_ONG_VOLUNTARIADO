<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Certificado extends Model
{
    use HasFactory;

    protected $table = 'certificados';
    protected $primaryKey = 'id_certificado';
    public $timestamps = false;

    protected $fillable = [
        'id_voluntario',
        'id_campana',
        'horas_certificadas',
        'fecha_emision',
        'codigo_verificacion',
        'pdf_url',
        'firmado_por',
        'fecha_expiracion',
        'tipo',
        'descripcion_logro',
    ];

    protected $casts = [
        'horas_certificadas' => 'decimal:2',
        'fecha_emision' => 'datetime',
        'fecha_expiracion' => 'date',
    ];

    public function voluntario()
    {
        return $this->belongsTo(Voluntario::class, 'id_voluntario', 'id_voluntario');
    }

    public function campana()
    {
        return $this->belongsTo(Campana::class, 'id_campana', 'id_campana');
    }
}
