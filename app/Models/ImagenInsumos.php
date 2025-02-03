<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ImagenInsumos extends Model
{
  //
  protected $table = 'imagenes_insumos';
  protected $primaryKey = 'id';
  protected $fillable = ['id_insumo', 'url'];

  public function producto()
  {
    return $this->belongsTo(Insumo::class, 'id_insumo');
  }
}
