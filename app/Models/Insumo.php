<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Insumo extends Model
{
  protected $table = 'insumos';
  protected $primaryKey = 'id';

  protected $fillable = [
    'nombre',
    'descripcion',
    'precio',
    'cantidad',
    'id_categoria',
    'id_proveedor',
  ];

  protected $hidden = [];

  public function imagenes()
  {
    return $this->hasMany(ImagenInsumos::class, 'id_insumo');
  }

  public function categorias() {
    return $this->belongsTo(Categoria::class,'id_categoria');
  }

  public function proveedor() {
    return $this->belongsTo(Proveedor::class,'id_proveedor');
  }
}
