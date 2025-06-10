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
            $table->integer('id_punto')->unique(); // ID CTAN
            $table->string('id_municipio');
            $table->string('id_nucleo');
            $table->string('direccion');
            $table->string('tipo'); // Estanco, bar, etc.
            $table->decimal('latitud', 18, 15);
            $table->decimal('longitud', 18, 15);
            $table->foreign('id_municipio')->references('id_municipio')->on('municipios');
            $table->foreign('id_nucleo')->references('id_nucleo')->on('nucleos');
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
