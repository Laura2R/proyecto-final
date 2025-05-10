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
        Schema::create('linea_parada', function (Blueprint $table) {
            $table->id();
            $table->string('id_linea');
            $table->string('id_parada');
            $table->integer('orden')->nullable();
            $table->enum('sentido', ['ida', 'vuelta']);
            $table->foreign('id_linea')->references('id_linea')->on('lineas')->onDelete('cascade');
            $table->foreign('id_parada')->references('id_parada')->on('paradas')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('linea_parada');
    }
};
