<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoriaCampana extends Model
{
    use HasFactory;

    protected $table = 'categorias_campanas';
    protected $primaryKey = 'id_categoria';
    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'icono',
        'color_hex',
        'descripcion',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    public function campanas()
    {
        return $this->hasMany(Campana::class, 'id_categoria', 'id_categoria');
    }
}
