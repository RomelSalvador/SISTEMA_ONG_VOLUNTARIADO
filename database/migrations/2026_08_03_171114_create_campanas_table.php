<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('campanas', function (Blueprint $table) {
            $table->integer('id_campana', true);
            $table->integer('id_organizador')->nullable();
            $table->string('nombre', 150);
            $table->text('descripcion')->nullable();
            $table->string('lugar', 255);
            $table->decimal('latitud', 10, 8)->nullable();
            $table->decimal('longitud', 11, 8)->nullable();
            $table->date('fecha_inicio');
            $table->date('fecha_fin');
            $table->time('hora_inicio');
            $table->time('hora_fin')->nullable();
            $table->integer('capacidad_max');
            $table->integer('meta_voluntarios')->nullable();
            $table->integer('id_categoria')->nullable();
            $table->text('requisitos')->nullable();
            $table->text('imagen_banner')->nullable();
            $table->text('cronograma')->nullable();
            $table->enum('estado', ['activa', 'completada', 'cancelada', 'en_espera', 'archivada'])->default('activa');
            $table->timestamp('fecha_creacion')->nullable()->useCurrent();
            $table->timestamp('fecha_modificacion')->nullable()->useCurrentOnUpdate();
            $table->integer('puntos_ecologicos')->default(10);
            $table->text('impacto_ambiental')->nullable();
            $table->text('impacto_social')->nullable();
            $table->decimal('presupuesto_estimado', 12, 2)->nullable();
            $table->text('patrocinadores')->nullable();
            
            $table->index('fecha_inicio');
            $table->index('fecha_fin');
            $table->index('estado');
            $table->index('id_categoria');
            $table->fullText(['nombre', 'descripcion', 'lugar']);
            
            $table->foreign('id_organizador')
                  ->references('id_organizador')
                  ->on('organizadores')
                  ->onDelete('set null');
            
            $table->foreign('id_categoria')
                  ->references('id_categoria')
                  ->on('categorias_campanas')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('campanas');
    }
};
