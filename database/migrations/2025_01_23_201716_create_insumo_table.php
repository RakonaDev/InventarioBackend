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
    Schema::create('insumos', function (Blueprint $table) {
      $table->id('id')->primary()->autoIncrement();
      $table->string('nombre');
      $table->string('imagen');
      $table->string('descripcion');
      $table->double('precio');
      $table->foreign('id_tipo_consumo')->references('id')->on('tipo_consumo');
      $table->foreign('id_categoria')->references('id')->on('categoria');
      $table->integer('vida_util_dias');
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('insumos');
  }
};
