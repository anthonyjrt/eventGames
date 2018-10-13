<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = [
        'libelle',
    ];

    public function games()
    {
        return $this->belongsToMany('App\Game');
    }
}
