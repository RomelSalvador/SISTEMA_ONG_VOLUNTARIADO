<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('horas_voluntariado', function (Blueprint $table) {
            $table->integer('id_hora', true);
            $table->integer('id_inscripcion');
            $table->decimal('horas_calculadas', 5, 2);
            $table->date('fecha_actividad');
            $table->time('hora_inicio')->nullable();
            $table->time('hora_fin')->nullable();
            $table->text('descripcion_actividad')->nullable();
            $table->integer('aprobado_por')->nullable();
            $table->enum('estado', ['pendiente', 'aprobado', 'rechazado'])->default('pendiente');
            $table->timestamp('fecha_aprobacion')->nullable();
            $table->text('comentario_aprobacion')->nullable();
            
            $table->index('estado');
            $table->index('fecha_actividad');
            
            $table->foreign('id_inscripcion')
                  ->references('id_inscripcion')
                  ->on('inscripciones')
                  ->onDelete('cascade');
            
            $table->foreign('aprobado_por')
                  ->references('id_organizador')
                  ->on('organizadores')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('horas_voluntariado');
    }
};
