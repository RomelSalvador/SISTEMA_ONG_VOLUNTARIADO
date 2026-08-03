<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('usuarios', function (Blueprint $table) {
            $table->integer('id_usuario', true); // AUTO_INCREMENT
            $table->string('email', 100)->unique();
            $table->string('password_hash', 255);
            $table->string('nombres', 60);
            $table->string('apellidos', 60);
            $table->char('dni', 8)->unique();
            $table->string('telefono', 15)->nullable();
            $table->enum('rol', ['voluntario', 'organizador', 'administrador']);
            $table->boolean('activo')->default(1);
            $table->timestamp('fecha_registro')->nullable()->useCurrent();
            $table->timestamp('ultimo_acceso')->nullable();
            $table->text('foto_perfil')->nullable();
            
            $table->index('email');
            $table->index('rol');
            $table->index('activo');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('usuarios');
    }
};
