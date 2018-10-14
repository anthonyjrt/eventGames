<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    protected $fillable = [
        'libelle',
        'pegi',
        'nb_player',
        'console_id'
    ];

    public function console()
    {
        return $this->hasOne(Console::class, 'id', 'console_id');
    }

    public function categories()
    {
        return $this->belongsToMany('App\Category');
    }
}
