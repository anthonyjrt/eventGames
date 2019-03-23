<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Ranking extends Model
{
    protected $fillable = [
        'player_id',
        'game_id',
        'player_number',
        'score'
    ];


    public function game()
    {
        return $this->belongsTo('App\Game', 'game_id');
    }
    public function player()
    {
        return $this->belongsTo('App\Player','player_id');
    }
}
