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
    Schema::create('compras', function (Blueprint $table) {
      $table->id();
      $table->integer('cantidad');
      $table->string('comprobante')->nullable();
      $table->double('total');
      $table->unsignedBigInteger('id_producto');
      $table->timestamp('fecha_ingreso')->nullable();
      $table->timestamp('fecha_vencimiento')->nullable();
      $table->integer('vida_utiles_dias')->nullable();
      $table->foreign('id_producto')->references('id')->on('insumos')->onDelete('cascade');
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('compras');
  }
};
