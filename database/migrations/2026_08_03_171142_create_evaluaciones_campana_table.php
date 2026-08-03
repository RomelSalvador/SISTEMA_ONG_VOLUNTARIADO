<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('evaluaciones_campana', function (Blueprint $table) {
            $table->integer('id_evaluacion', true);
            $table->integer('id_inscripcion')->unique();
            $table->integer('puntuacion')->nullable();
            $table->text('comentario')->nullable();
            $table->text('recomendaciones')->nullable();
            $table->timestamp('fecha_evaluacion')->nullable()->useCurrent();
            $table->text('aspectos_positivos')->nullable();
            $table->text('aspectos_mejorar')->nullable();
            
            $table->foreign('id_inscripcion')
                  ->references('id_inscripcion')
                  ->on('inscripciones')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('evaluaciones_campana');
    }
};
