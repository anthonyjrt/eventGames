<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Player extends Model
{
    protected $fillable = [
      'name',
      'firstname',
      'birthdate',
      'inGame'
    ];

    public function games(){
        return $this->belongsToMany('App\Game')->withPivot('life')->with('categories');
    }
}
