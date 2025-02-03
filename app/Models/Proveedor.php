<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Proveedor extends Model
{
  use HasFactory;

  protected $table = 'proveedores';
  protected $fillable = [
    'name',
    'phone',
    'email',
    'ruc',
    'address',
  ];

  public function insumos() {
    return $this->belongsToMany(Insumo::class, 'id_proveedor');
  }
}
