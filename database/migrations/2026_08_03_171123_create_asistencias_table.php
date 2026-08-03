<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asistencia', function (Blueprint $table) {
            $table->integer('id_asistencia', true);
            $table->integer('id_inscripcion');
            $table->time('hora_ingreso');
            $table->time('hora_salida')->nullable();
            $table->date('fecha_asistencia');
            $table->enum('metodo_verificacion', ['qr', 'manual', 'admin', 'biometrico', 'face'])->default('manual');
            $table->decimal('latitud_checkin', 10, 8)->nullable();
            $table->decimal('longitud_checkin', 11, 8)->nullable();
            $table->decimal('latitud_checkout', 10, 8)->nullable();
            $table->decimal('longitud_checkout', 11, 8)->nullable();
            $table->text('observacion')->nullable();
            $table->integer('registrado_por')->nullable();
            $table->timestamp('fecha_registro')->nullable()->useCurrent();
            $table->decimal('horas_calculadas', 5, 2)->nullable();
            $table->enum('estado_asistencia', ['presente', 'tarde', 'ausente', 'justificado'])->default('presente');
            
            $table->unique(['id_inscripcion', 'fecha_asistencia']);
            $table->index('fecha_asistencia');
            
            $table->foreign('id_inscripcion')
                  ->references('id_inscripcion')
                  ->on('inscripciones')
                  ->onDelete('cascade');
            
            $table->foreign('registrado_por')
                  ->references('id_usuario')
                  ->on('usuarios')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asistencia');
    }
};
