<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('materiales_campana', function (Blueprint $table) {
            $table->integer('id_material', true);
            $table->integer('id_campana');
            $table->string('nombre_material', 100);
            $table->integer('cantidad_necesaria')->nullable();
            $table->integer('cantidad_recolectada')->default(0);
            $table->string('unidad_medida', 20)->nullable();
            $table->string('proveedor', 100)->nullable();
            $table->decimal('costo_unitario', 10, 2)->nullable();
            
            $table->foreign('id_campana')
                  ->references('id_campana')
                  ->on('campanas')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('materiales_campana');
    }
};
