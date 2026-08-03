<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('actividades', function (Blueprint $table) {
            $table->integer('id_actividad', true);
            $table->integer('id_campana');
            $table->string('nombre', 150);
            $table->text('descripcion')->nullable();
            $table->date('fecha');
            $table->time('hora_inicio');
            $table->time('hora_fin')->nullable();
            $table->integer('capacidad_max')->nullable();
            $table->enum('estado', ['programada', 'en_curso', 'completada', 'cancelada'])->default('programada');
            $table->string('responsable', 100)->nullable();
            $table->text('observaciones')->nullable();
            $table->integer('duracion_estimada')->nullable()->comment('Duración en minutos');
            $table->boolean('requiere_materiales')->default(0);
            
            $table->index('fecha');
            $table->index('estado');
            
            $table->foreign('id_campana')
                  ->references('id_campana')
                  ->on('campanas')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('actividades');
    }
};
