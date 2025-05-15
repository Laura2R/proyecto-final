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
            $table->id();
            $table->string('id_linea');
            $table->enum('sentido', ['ida', 'vuelta']);
            $table->string('tipo_dia', 255);
            $table->json('horas'); // Almacenar array de horas en JSON
            $table->string('id_frecuencia')->nullable();
            $table->foreign('id_linea')->references('id_linea')->on('lineas')->onDelete('cascade');
            $table->foreign('id_frecuencia')->references('id_frecuencia')->on('frecuencias')->onDelete('set null');
            $table->timestamps();
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
