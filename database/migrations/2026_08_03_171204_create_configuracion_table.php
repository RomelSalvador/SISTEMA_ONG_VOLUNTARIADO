<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('configuracion', function (Blueprint $table) {
            $table->integer('id_config', true);
            $table->string('clave', 50)->unique();
            $table->text('valor')->nullable();
            $table->text('descripcion')->nullable();
            $table->enum('tipo', ['string', 'int', 'boolean', 'json'])->default('string');
            $table->integer('modificado_por')->nullable();
            $table->timestamp('fecha_modificacion')->nullable()->useCurrentOnUpdate();
            $table->string('categoria', 50)->default('general');
            
            $table->foreign('modificado_por')
                  ->references('id_usuario')
                  ->on('usuarios')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('configuracion');
    }
};
