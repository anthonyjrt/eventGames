<?php

namespace App\Http\Controllers;

use App\Player;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

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

        $modelRead = Player::orderBy('name', 'asc')->get();
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
        return response()->json(compact('modelRead'));
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
        $this->validate($request, [
            'name' => 'required',
            'firstname' => 'required',
            'birthdate' => 'required|date',

        ]);

        $player = Player::findOrFail($id);
        $player->name=request('name');
        $player->firstname= request('firstname');
        $player->birthdate = request('birthdate');





        //$player->birthdate = request('birthdate');

        $player->save();

        if($player->save()=== true)
            return response()->json(['message' => 'Player updated successfully!'], 200);
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
}
