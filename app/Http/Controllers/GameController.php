<?php

namespace App\Http\Controllers;

use App\Category;
use App\Game;
use Illuminate\Http\Request;

class GameController extends Controller
{
    public function index(){
        $modelRead = Game::orderBy('libelle', 'asc')->with('categories')

            ->get();

        $categories = Category::all();
        $modelRead2 = $categories->pluck('libelle', 'id');
        $modelRead2 = $categories->map(
            function($category) {
                return [
                    "category_id" => $category->id,
                    "label" => $category->libelle
                ];
            }
        );
        return response()->json(compact('modelRead','modelRead2'));
    }
    public function store(Request $request){

        $select = $request->get('select');

       $game = new Game();
       $game->libelle = \request('libelle');
       $game->pegi = \request('pegi');
       $game->nb_players = \request('nb_players');
       $game->save();
       $game->categories()->sync($select);
        //Game::find($game->id)->categories()->attach(request('select'));
    }
}
