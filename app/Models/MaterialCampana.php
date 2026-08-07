<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaterialCampana extends Model
{
    use HasFactory;

    protected $table = 'materiales_campana';
    protected $primaryKey = 'id_material';
    public $timestamps = false;

    protected $fillable = [
        'id_campana',
        'nombre_material',
        'cantidad_necesaria',
        'cantidad_recolectada',
        'unidad_medida',
        'proveedor',
        'costo_unitario',
    ];

    protected $casts = [
        'costo_unitario' => 'decimal:2',
    ];

    public function campana()
    {
        return $this->belongsTo(Campana::class, 'id_campana', 'id_campana');
    }
}
