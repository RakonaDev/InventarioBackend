<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Estado extends Model
{
  //

  protected $primaryKey = 'id';

  protected $fillable = ['nombre'];

  public function user () {
    return $this->hasMany(User::class, 'id_estado');
  }
}
