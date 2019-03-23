<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Fight extends Model
{
    protected $fillable = [
        'statut'];

    public function rankings(){
        return $this->belongsToMany('App\Ranking')->withPivot('color')->with('player')->with('game');
    }
}
