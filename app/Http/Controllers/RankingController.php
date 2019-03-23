<?php

namespace App\Http\Controllers;

use App\Player;
use App\Ranking;
use Illuminate\Http\Request;

class RankingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $rankings = Ranking::with('player')->with('game')->orderBy('player_id', 'asc')->get();
        return response()->json(compact('rankings'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $player = Player::with('games')->findOrFail(request('id'));
        $player_n =$player->games->last();
        $player_number = \request('id')."_1";
        $last_ranking = Ranking::where('player_number', $player_number)->where('game_id', $player_n->pivot->game_id )->first();
        if ($last_ranking == null){
            for ($i = 1; $i <= $player_n->pivot->life;$i++){
                $ranking = new Ranking();
                $ranking->player_id = $player_n->pivot->player_id;
                $ranking->game_id = $player_n->pivot->game_id;
                $ranking->player_number = $ranking->player_id."_".$i;
                $ranking->life = 1;
                $ranking->score = 0;
                $ranking->save();
            }

        }

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
