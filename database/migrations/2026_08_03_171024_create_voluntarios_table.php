<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('voluntarios', function (Blueprint $table) {
            $table->integer('id_voluntario')->primary();
            $table->string('matricula_universitaria', 20)->unique();
            $table->string('facultad', 100);
            $table->string('carrera', 100);
            $table->string('ciclo', 10)->nullable();
            $table->text('codigo_qr')->nullable();
            $table->decimal('horas_acumuladas', 10, 2)->default(0.00);
            $table->date('fecha_graduacion')->nullable();
            $table->enum('disponibilidad', ['disponible', 'ocupado', 'no_disponible'])->default('disponible');
            $table->text('habilidades')->nullable();
            $table->date('fecha_nacimiento')->nullable();
            $table->text('direccion')->nullable();
            
            $table->index('matricula_universitaria');
            $table->index('facultad');
            $table->index('disponibilidad');
            
            $table->foreign('id_voluntario')
                    ->references('id_usuario')
                    ->on('usuarios')
                    ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('voluntarios');
    }
};

