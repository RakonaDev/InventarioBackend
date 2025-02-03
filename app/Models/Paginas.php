<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Paginas extends Model
{
  protected $primaryKey = 'id';
  // 'icon_html',
  // 'path'
  protected $fillable = [
    'nombre',
  ];

  public function roles() {
    return $this->belongsToMany(Roles::class, 'permisos', 'id_pagina', 'id_rol')->withTimestamps();
  }
}
