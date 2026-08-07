<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Configuracion extends Model
{
    use HasFactory;

    protected $table = 'configuracion';
    protected $primaryKey = 'id_config';
    public $timestamps = false;

    protected $fillable = [
        'clave',
        'valor',
        'descripcion',
        'tipo',
        'modificado_por',
        'fecha_modificacion',
        'categoria',
    ];

    protected $casts = [
        'fecha_modificacion' => 'datetime',
    ];

    public function modificadoPor()
    {
        return $this->belongsTo(Usuario::class, 'modificado_por', 'id_usuario');
    }
}
