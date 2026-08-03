<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('certificados', function (Blueprint $table) {
            $table->integer('id_certificado', true);
            $table->integer('id_voluntario');
            $table->integer('id_campana')->nullable();
            $table->decimal('horas_certificadas', 5, 2);
            $table->timestamp('fecha_emision')->nullable()->useCurrent();
            $table->string('codigo_verificacion', 100)->unique();
            $table->text('pdf_url')->nullable();
            $table->string('firmado_por', 100)->nullable();
            $table->date('fecha_expiracion')->nullable();
            $table->enum('tipo', ['participacion', 'horas', 'logro'])->default('participacion');
            $table->text('descripcion_logro')->nullable();
            
            $table->index('codigo_verificacion');
            $table->index('fecha_emision');
            
            $table->foreign('id_voluntario')
                  ->references('id_voluntario')
                  ->on('voluntarios')
                  ->onDelete('cascade');
            
            $table->foreign('id_campana')
                  ->references('id_campana')
                  ->on('campanas')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('certificados');
    }
};
