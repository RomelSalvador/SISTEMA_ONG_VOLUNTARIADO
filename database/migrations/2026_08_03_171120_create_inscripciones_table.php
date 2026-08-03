<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inscripciones', function (Blueprint $table) {
            $table->integer('id_inscripcion', true);
            $table->integer('id_voluntario');
            $table->integer('id_campana');
            $table->integer('id_actividad')->nullable();
            $table->timestamp('fecha_inscripcion')->nullable()->useCurrent();
            $table->enum('estado', ['pendiente', 'aprobada', 'rechazada', 'cancelada', 'finalizada'])->default('pendiente');
            $table->boolean('asistencia_confirmada')->default(0);
            $table->decimal('horas_acreditadas', 5, 2)->default(0.00);
            $table->text('comentarios')->nullable();
            $table->timestamp('fecha_aprobacion')->nullable();
            $table->timestamp('fecha_cancelacion')->nullable();
            $table->text('motivo_cancelacion')->nullable();
            $table->integer('calificacion_voluntario')->nullable();
            
            $table->unique(['id_voluntario', 'id_campana']);
            $table->index('estado');
            $table->index('fecha_inscripcion');
            
            $table->foreign('id_voluntario')
                  ->references('id_voluntario')
                  ->on('voluntarios')
                  ->onDelete('cascade');
            
            $table->foreign('id_campana')
                  ->references('id_campana')
                  ->on('campanas')
                  ->onDelete('cascade');
            
            $table->foreign('id_actividad')
                  ->references('id_actividad')
                  ->on('actividades')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inscripciones');
    }
};
