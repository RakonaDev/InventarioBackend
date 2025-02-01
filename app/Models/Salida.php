<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Salida extends Model
{
  protected $table = 'salidas';

  protected $primaryKey = 'id';

  protected $fillable = [
    'cantidad',
    'id_producto',
  ];

  public function producto() {
    return $this->belongsTo(Insumo::class,'id_producto');
  }
}
