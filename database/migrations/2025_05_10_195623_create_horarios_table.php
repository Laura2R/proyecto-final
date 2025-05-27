<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('horarios', function (Blueprint $table) {
            $table->string('id_linea');
            $table->string('id_planificador');
            $table->date('fecha_inicio');
            $table->date('fecha_fin');
            $table->boolean('muestra_fecha_fin')->default(false);
            $table->enum('sentido', ['ida', 'vuelta']);
            $table->json('horas'); // Array de horas
            $table->string('frecuencia_acronimo');
            $table->text('observaciones')->nullable();
            $table->string('demanda_horas')->nullable();
            $table->json('nucleos')->nullable(); // Array de núcleos para este sentido
            $table->json('bloques')->nullable(); // Array de bloques para este sentido
            $table->timestamps();

            $table->foreign('id_linea')->references('id_linea')->on('lineas')->onDelete('cascade');
            $table->foreign('frecuencia_acronimo')->references('acronimo')->on('frecuencias');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('horarios');
    }
};
