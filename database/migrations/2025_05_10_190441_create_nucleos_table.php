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
        Schema::create('nucleos', function (Blueprint $table) {
            $table->id();
            $table->string('id_nucleo')->unique(); // ID CTAN
            $table->string('id_municipio');
            $table->string('id_zona');
            $table->string('nombre');
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
        Schema::dropIfExists('nucleos');
    }
};
