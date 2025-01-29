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
      $table->id('id');
      $table->string('nombre');
      $table->string('descripcion');
      $table->double('precio');

      $table->unsignedBigInteger('id_tipo_insumo');
      $table->foreign('id_tipo_insumo')->references('id')->on('tipo_insumo')->onDelete('cascade');

      $table->unsignedBigInteger('id_categoria');
      $table->foreign('id_categoria')->references('id')->on('categorias')->onDelete('cascade');

      $table->unsignedBigInteger('id_proveedor');
      $table->foreign('id_proveedor')->references('id')->on('proveedores')->onDelete('cascade');
      
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
