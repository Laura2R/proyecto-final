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
        Schema::create('lineas', function (Blueprint $table) {
            $table->id();
            $table->string('id_linea')->unique(); // ID CTAN
            $table->string('codigo');
            $table->string('nombre');
            $table->string('modo');
            $table->string('operadores');
            $table->boolean('hay_noticias')->default(false);
            $table->string('termometro_ida')->nullable();
            $table->string('termometro_vuelta')->nullable();
            $table->json('polilinea')->nullable(); // Para almacenar la ruta geográfica
            $table->string('color')->nullable();
            $table->boolean('tiene_ida')->default(true);
            $table->boolean('tiene_vuelta')->default(true);
            $table->string('pmr')->nullable(); // Accesibilidad
            $table->string('concesion')->nullable();
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lineas');
    }
};
