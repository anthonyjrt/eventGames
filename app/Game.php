<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    protected $fillable = [
        'libelle',
        'pegi',
        'nb_player'
    ];

    public function categories()
    {
        return $this->belongsToMany('App\Category');
    }
}
