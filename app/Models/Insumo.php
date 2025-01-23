<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Insumo extends Model
{
  protected $primaryKey = 'id';

  protected $fillable = [
    'nombre',
    'imagen',
    'descripcion',
    'precio',
    'vida_util_dias'
  ];

  protected $hidden = [
    
  ];
}
