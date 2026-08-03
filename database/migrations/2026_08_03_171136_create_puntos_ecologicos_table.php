<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('puntos_ecologicos', function (Blueprint $table) {
            $table->integer('id_punto', true);
            $table->integer('id_voluntario')->unique();
            $table->integer('puntos')->default(0);
            $table->enum('nivel', ['Bronce', 'Plata', 'Oro', 'Platino', 'Diamante'])->default('Bronce');
            $table->timestamp('fecha_actualizacion')->nullable()->useCurrentOnUpdate();
            $table->integer('puntos_acumulados_mes')->default(0);
            $table->string('ultimo_logro', 100)->nullable();
            
            $table->foreign('id_voluntario')
                  ->references('id_voluntario')
                  ->on('voluntarios')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('puntos_ecologicos');
    }
};
