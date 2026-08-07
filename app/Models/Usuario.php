<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Usuario extends Model
{
    use HasFactory, Notifiable;

    protected $table = 'usuarios';
    protected $primaryKey = 'id_usuario';
    public $timestamps = false;

    protected $fillable = [
        'email',
        'password_hash',
        'nombres',
        'apellidos',
        'dni',
        'telefono',
        'rol',
        'activo',
        'fecha_registro',
        'ultimo_acceso',
        'foto_perfil',
    ];

    protected $hidden = [
        'password_hash',
    ];

    protected $casts = [
        'activo' => 'boolean',
        'fecha_registro' => 'datetime',
        'ultimo_acceso' => 'datetime',
    ];

    // Relaciones 1:1 (herencia de tabla)
    public function voluntario()
    {
        return $this->hasOne(Voluntario::class, 'id_voluntario', 'id_usuario');
    }

    public function organizador()
    {
        return $this->hasOne(Organizador::class, 'id_organizador', 'id_usuario');
    }

    // Relaciones 1:N
    public function notificaciones()
    {
        return $this->hasMany(Notificacion::class, 'id_usuario', 'id_usuario');
    }

    public function logsAuditoria()
    {
        return $this->hasMany(LogAuditoria::class, 'id_usuario', 'id_usuario');
    }

    public function asistenciasRegistradas()
    {
        return $this->hasMany(Asistencia::class, 'registrado_por', 'id_usuario');
    }

    public function configuracionesModificadas()
    {
        return $this->hasMany(Configuracion::class, 'modificado_por', 'id_usuario');
    }
}
