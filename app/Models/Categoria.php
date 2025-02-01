<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    //
  protected $table = 'categorias';

  protected $primaryKey = 'id';

  protected $fillable = [
    'nombre',
    'descripcion'
  ];

  public function producto()
  {
    return $this->belongsTo(Insumo::class, 'id_categoria');
  }
}
