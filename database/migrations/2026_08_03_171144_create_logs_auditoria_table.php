<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('logs_auditoria', function (Blueprint $table) {
            $table->integer('id_log', true);
            $table->integer('id_usuario')->nullable();
            $table->string('accion', 255);
            $table->string('tabla_afectada', 50)->nullable();
            $table->integer('registro_id')->nullable();
            $table->json('datos_anteriores')->nullable();
            $table->json('datos_nuevos')->nullable();
            $table->string('ip_origen', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamp('fecha')->nullable()->useCurrent();
            
            $table->index('fecha');
            $table->index('tabla_afectada');
            $table->index('id_usuario');
            
            $table->foreign('id_usuario')
                  ->references('id_usuario')
                  ->on('usuarios')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('logs_auditoria');
    }
};
