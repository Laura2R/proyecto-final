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
        Schema::create('tarifas_interurbanas', function (Blueprint $table) {
            $table->id();
            $table->integer('saltos');
            $table->decimal('bs', 8, 7); // Para manejar hasta 7 decimales
            $table->decimal('tarjeta', 8, 7);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tarifas_interurbanas');
    }
};
