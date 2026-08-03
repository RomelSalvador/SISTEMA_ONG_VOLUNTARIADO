<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notificaciones', function (Blueprint $table) {
            $table->integer('id_notificacion', true);
            $table->integer('id_usuario');
            $table->string('titulo', 100);
            $table->text('mensaje');
            $table->boolean('leida')->default(0);
            $table->timestamp('fecha_envio')->nullable()->useCurrent();
            $table->enum('tipo', ['info', 'exito', 'advertencia', 'error', 'recordatorio'])->default('info');
            $table->timestamp('fecha_lectura')->nullable();
            $table->text('enlace_accion')->nullable();
            $table->enum('prioridad', ['baja', 'media', 'alta'])->default('media');
            $table->enum('categoria_notificacion', ['sistema', 'campana', 'actividad', 'asistencia', 'logro'])->default('sistema');
            
            $table->index(['id_usuario', 'leida']);
            $table->index('fecha_envio');
            $table->index('prioridad');
            
            $table->foreign('id_usuario')
                  ->references('id_usuario')
                  ->on('usuarios')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notificaciones');
    }
};
