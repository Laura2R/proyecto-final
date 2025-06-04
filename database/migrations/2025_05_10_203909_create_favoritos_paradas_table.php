<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('favoritos_paradas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->unsignedBigInteger('id_parada'); // CAMBIO: de string a unsignedBigInteger
            $table->foreign('id_parada')->references('id_parada')->on('paradas')->onDelete('cascade');
            $table->timestamps();

            $table->unique(['user_id', 'id_parada']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('favoritos_paradas');
    }
};
