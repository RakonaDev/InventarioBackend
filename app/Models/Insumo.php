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
    'vida_util_dias'
  ];

  protected $hidden = [
    
  ];

  public function imagenes()
    {
        return $this->hasMany(ImagenInsumos::class, 'id_insumo');
    }
}
