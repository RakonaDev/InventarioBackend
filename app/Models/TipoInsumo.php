<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoInsumo extends Model
{
  use HasFactory;
  
  protected $table = 'tipo_insumo';

  protected $fillable = ['nombre'];
}
