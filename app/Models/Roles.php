<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Roles extends Model
{
  protected $primaryKey = 'id';

  protected $fillable = [
    'name'
  ];

  public function ListPaginas() {
    return $this->belongsToMany(Paginas::class, 'permisos', 'id_rol', 'id_pagina')->withTimestamps();
  }
}
