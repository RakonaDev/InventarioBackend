<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Insumo extends Model
{
  protected $primaryKey = 'id';

  protected $fillable = [
    'nombre',
    'descripcion',
    'precio',
    'id_categoria',
    'id_proveedor',
    'id_tipo_insumo',
    'vida_util_dias'
  ];

  protected $hidden = [];

  public function imagenes()
  {
    return $this->hasMany(ImagenInsumos::class, 'id_insumo');
  }

  public function categorias() {
    return $this->belongsTo(Categoria::class,'id_categoria');
  }
}
