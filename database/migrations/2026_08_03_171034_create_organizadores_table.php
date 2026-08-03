<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('organizadores', function (Blueprint $table) {
            $table->integer('id_organizador')->primary();
            $table->string('ong_nombre', 100)->nullable();
            $table->string('telefono_emergencia', 15)->nullable();
            $table->string('puesto', 50)->nullable();
            $table->date('fecha_contratacion')->nullable();
            $table->string('departamento', 100)->nullable();
            
            $table->foreign('id_organizador')
                ->references('id_usuario')
                ->on('usuarios')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('organizadores');
    }
};
