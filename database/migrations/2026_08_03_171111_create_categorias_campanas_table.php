<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categorias_campanas', function (Blueprint $table) {
            $table->integer('id_categoria', true);
            $table->string('nombre', 50)->unique();
            $table->string('icono', 50)->nullable();
            $table->string('color_hex', 7)->default('#2E7D32');
            $table->text('descripcion')->nullable();
            $table->boolean('activo')->default(1);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('categorias_campanas');
    }
};
