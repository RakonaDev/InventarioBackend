<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Compra extends Model
{
  protected $table = 'compras';
  protected $primaryKey = 'id';

  protected $fillable = [
    'cantidad',
    'comprobante',
    'total',
    'id_producto',
    'fecha_ingreso',
    'fecha_vencimiento',
    'vida_utiles_dias',
  ];

  public function producto() {
    return $this->belongsTo(Insumo::class,'id_producto');
  }
}
