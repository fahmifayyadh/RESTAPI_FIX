<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Place extends Model
{
  protected $guarded = [];

  public function PIC()
  {
      return $this->hasMany('App\PIC');
  }
}
