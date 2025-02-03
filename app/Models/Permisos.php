<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Permisos extends Model
{
  protected $table = 'permisos';

  protected $primaryKey = 'id';

  protected $fillable = [
    'id_rol',
    'id_pagina'
  ];
}
