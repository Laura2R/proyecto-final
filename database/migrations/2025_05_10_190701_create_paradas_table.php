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
        Schema::create('paradas', function (Blueprint $table) {
            $table->id();
            $table->string('id_parada')->unique(); // ID CTAN
            $table->string('id_nucleo');
            $table->string('id_municipio');
            $table->string('id_zona');
            $table->string('nombre');
            $table->decimal('latitud', 18, 15);
            $table->decimal('longitud', 18, 15);
            $table->string('modos');
            $table->foreign('id_nucleo')->references('id_nucleo')->on('nucleos');
            $table->foreign('id_municipio')->references('id_municipio')->on('municipios');
            $table->foreign('id_zona')->references('id_zona')->on('zonas');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('paradas');
    }
};
