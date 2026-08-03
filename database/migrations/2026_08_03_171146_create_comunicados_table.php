<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('comunicados', function (Blueprint $table) {
            $table->integer('id_comunicado', true);
            $table->string('titulo', 150);
            $table->text('contenido');
            $table->text('imagen')->nullable();
            $table->timestamp('fecha_publicacion')->nullable()->useCurrent();
            $table->timestamp('fecha_expiracion')->nullable();
            $table->enum('estado', ['publicado', 'borrador', 'archivado'])->default('publicado');
            $table->boolean('publico')->default(1);
            $table->string('categoria', 50)->default('general');
            $table->string('autor', 100)->nullable();
            $table->integer('visitas')->default(0);
            
            $table->index('fecha_publicacion');
            $table->index('estado');
            $table->index('publico');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('comunicados');
    }
};
