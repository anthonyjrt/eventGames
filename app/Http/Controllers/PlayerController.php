<?php

namespace App\Http\Controllers;

use App\Fight;
use App\Game;
use App\Player;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Pusher\Pusher;

class PlayerController extends Controller
{
    protected $fillable = [
        'id',
        'name',
        'fistname',
        'birthdate',
        'inGame'
    ];
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $modelRead = Player::orderBy('name', 'asc')->with('games')->get();

        foreach ($modelRead as $player) {
            $player->age = $player->birthdate;
            $player->age = Carbon::parse($player->age);
            $player->birthdate = $player->age->format('d/m/Y');
            $player->age->format('Y-m-d');
            $player->age = $player->age->age;

            if ($player->inGame == 0){
                $player->inGame = "Non";
            } else {
                $player->inGame = "Oui";
            }

        }
        $games = Game::with('categories')->get();
        /**$games = [];
        foreach ($mgames as $mgame){
            if ($mgame->categories[0]->id !== 6){
                array_push($games, $mgame);
            }
        }*/

        $modelRead2 = $games->reject(function ($game){
            return $game->categories[0]->id == 6;
        })->map(function($g) {
                return [
                    "game_id" => $g->id,
                    "label" => $g->libelle
                ];
            }
        )->values();


        return response()->json(compact('modelRead', 'modelRead2'));
    }

    public function fightIndex(){
        $fights = Fight::all();
        return response()->json(compact('fights'));
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        /*$request->validate([
            'name' => 'required',
            'firstname' => 'required',
            'birthdate' => 'required|date',
            'inGame' => 0,
        ]);*/
        $p_name = [];
        $p_all = [];
        $last_player = Player::where('name', request('name'))
            ->where('firstname', request('firstname'))
            ->where('birthdate', request('birthdate'))
            ->first();

        if ($last_player == null){
            $player = new Player();
            $player->name = request('name');
            $player->firstname =request('firstname');
            $player->birthdate = request('birthdate');
            $player->inGame = 0;
            $player->save();
            Session::flash('success', 'Le joueur vient d\'être créé');
            //Session::flash('notificationType', 'alert-success');

            $options = array(
                'cluster' => 'eu',
                'useTLS' => false
            );
            $pusher = new Pusher(
                '1f64241ae71172e1e018',
                '59e0e3c2a202c50aab87',
                '715517',
                $options
            );
            $player = Player::with('games')->findOrFail($player->id);
            $player->age = $player->birthdate;
            $player->age = Carbon::parse($player->age);
            $player->birthdate = $player->age->format('d/m/Y');
            $player->age->format('Y-m-d');
            $player->age = $player->age->age;

            if ($player->inGame == 0){
                $player->inGame = "Non";
            } else {
                $player->inGame = "Oui";
            }

            $data = $player;
            $pusher->trigger('players', 'player.created', $data);
            return response()->json(['data' => 'Le Joueur est bien créé' ],200);
            //return response()->with('notification', 'Le joueur vient d\'être créé') // The message
            //->with('notificationType', 'alert-success');
            //$request->session()->flash('status', 'Le joueur vient d\'être créé');
            //return response()->with()->json(['message' => 'Le joueur vient d\'être créé'], 200);
        }

        else{
            return response()->json(['message' => 'Le joueur existe déjà dans la base'], 403);
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
        $userGames = Player::with('games')
            ->findOrFail($id);
        $userGames = $userGames->games;

        return response()->json(compact('userGames'));
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
        $this->validate($request, [
            'lastname' => 'required',
            'firstname' => 'required',
            'birthdate' => 'required|date',
            'select' => 'required'

        ]);

        $selectgame = $request->get('select')[0];
        $selectlife = $request->get('select')[1];
        $player = Player::findOrFail($id);
        $player->name=request('lastname');
        $player->firstname= request('firstname');
        $player->birthdate = request('birthdate');
        //$selectgame = $request->get('selectgame');
        $select= $request->get('select');
        //$player->birthdate = request('birthdate');
        $player->save();
        $player->games()->attach(array($selectgame => array( 'life' => $selectlife)));

        if($player->save()=== true){
            $player->games();
            $options = array(
                'cluster' => 'eu',
                'useTLS' => false
            );
            $pusher = new Pusher(
                '1f64241ae71172e1e018',
                '59e0e3c2a202c50aab87',
                '715517',
                $options
            );
            $data = Player::with('games')->findOrFail($id);
            //$data = $data->games->last();

            $pusher->trigger('players', 'player.updated', $data);
            return response()->json(['message' => 'Player updated successfully!'], 200);
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $player = Player::findOrFail($id);
        $player->delete();

            return response()->json(['data' => 'Joueur supprimé avec succès!']);
    }

    public function treasury(){
        $lots = 501.8;
        $modelRead2 = Player::with('games')->get();
        $treasury = DB::select("select sum(a.life*2) as 'total' FROM game_player as a;");
        //$modelRead2 = $modelRead2->sum('pivot.life')->get();

        return response()->json(compact( 'lots','treasury'));
    }
}
