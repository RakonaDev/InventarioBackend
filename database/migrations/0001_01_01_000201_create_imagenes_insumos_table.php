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
        Schema::create('imagenes_insumos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_insumo');
            $table->string('url');
            $table->timestamps();

            $table->foreign('id_insumo')->references('id')->on('insumos')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('imagenes_insumos');
    }
};
