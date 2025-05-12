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
        Schema::create('puntos_venta', function (Blueprint $table) {
            $table->id();
            $table->string('id_punto')->unique(); // ID CTAN
            $table->string('id_municipio');
            $table->string('nombre');
            $table->string('direccion');
            $table->string('tipo'); // Estación, quiosco, etc.
            $table->decimal('latitud', 18, 15);
            $table->decimal('longitud', 18, 15);
            $table->text('horario')->nullable();
            $table->json('servicios')->nullable(); // Venta, info, etc.
            $table->foreign('id_municipio')->references('id_municipio')->on('municipios');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('puntos_venta');
    }
};
