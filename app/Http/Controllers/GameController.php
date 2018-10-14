<?php

namespace App\Http\Controllers;

use App\Category;
use App\Console;
use App\Game;
use Illuminate\Http\Request;

class GameController extends Controller
{
    public function index(){
        $modelRead = Game::orderBy('libelle', 'asc')
            ->with('categories')
            ->with('console')

            ->get();

        $categories = Category::all();
        $modelRead2 = $categories->map(
            function($category) {
                return [
                    "category_id" => $category->id,
                    "label" => $category->libelle
                ];
            }
        );
        $modelRead3 = Console::all()->map(
            function($console) {
                return [
                    "id" => $console->id,
                    "libelle" => $console->libelle
                ];
            }
        );
        return response()->json(compact('modelRead','modelRead2', 'modelRead3'));
    }
    public function store(Request $request){

        $select = $request->get('select');
       $game = new Game();
       $game->libelle = \request('libelle');
       $game->pegi = \request('pegi');
       $game->nb_players = \request('nb_players');
       $game->console_id = \request('console_id');
       $game->save();
       $game->categories()->sync($select);
        return response()->json(['data' => 'Le Jeu est bien créé' ],200);
        //Game::find($game->id)->categories()->attach(request('select'));
    }
    public function destroy($id)
    {
        $game = Game::findOrFail($id);
        $game->delete();

        return response()->json(['data' => 'Jeu supprimé avec succès!']);
    }
}
