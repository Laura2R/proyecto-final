<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('favoritos_lineas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->unsignedBigInteger('id_linea'); // CAMBIO: de string a unsignedBigInteger
            $table->foreign('id_linea')->references('id_linea')->on('lineas')->onDelete('cascade');
            $table->timestamps();

            $table->unique(['user_id', 'id_linea']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('favoritos_lineas');
    }
};
