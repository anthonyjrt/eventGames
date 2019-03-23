<?php

namespace App\Http\Controllers;

use App\Fight;
use App\Game;
use App\Player;
use App\Ranking;
use Illuminate\Http\Request;
use Pusher\Pusher;

class FightController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $fights = Fight::with('rankings')->where('statut', 'En cours')->get();
        return response()->json(compact('fights'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function control()
    {
        $fights = Fight::with('rankings')->get();
        //->where('statut','<>', 'Terminé')
        return response()->json(compact('fights'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $rankings = Ranking::all('game_id');
        $r = $rankings->map(function ($g) {
            return $g->game_id;
        })->values();
        $r = array_unique($r->toArray());
        $r = array_values($r);   // Reindex the array starting from 0.
        array_unshift($r, '');     // Prepend a dummy element to the start of the array.
        unset($r[0]);
        $sR = sizeof($r); // récupération de la longeur du tableau
        for ($i = 1; $i <= $sR; $i++) {
            echo("<script>console.log('Jeu ' + $i + ' en cours de création');</script>");
            $matchCree = false;
            while (!$matchCree) {
                echo("<script>console.log('Génération d\'un match pour le jeu : ' + $i);</script>");
                $partyToRun = Ranking::where('game_id', intval($r[$i]))->where('life', 1)->get()->random(2);
                $fight = new Fight();
                $fight->statut = "Attente";
                $fight->save();

                // Si Aucun des 2 joueurs n'est en jeu
                if ($partyToRun[0]->player->inGame == 0 && $partyToRun[1]->player->inGame == 0) {
                    echo("<script>console.log('Jeu : ' + $i + ', Joueurs 1 et Joueurs 2 disponible.');</script>");
                    $joueursDifferents = true;
                    while ($joueursDifferents) {
                        // Si l'id du joueur 1 est egale à celle du joueur 2
                        if ($partyToRun[0]->player->id == $partyToRun[1]->player->id) {
                            echo("<script>console.log('Jeu : ' + $i + ', Match avec 2 joueurs identique');</script>");
                            $fight->statut = "Terminé";
                            $fight->save();
                            $fight->rankings()->attach($partyToRun[0]->id, ['color' => 'red']);
                            $fight->rankings()->attach($partyToRun[1]->id, ['color' => 'blue']);
                            Ranking::findOrFail($partyToRun[0]->id)->increment('score');
                            Ranking::findOrFail($partyToRun[1]->id)->decrement('life');

                            $joueursDifferents = true;

                            echo("<script>console.log('Jeu : ' + $i + ', Recréation d\'un match');</script>");
                            $partyToRun = Ranking::where('game_id', intval($r[$i]))->where('life', 1)->get()->random(2);
                            $fight = new Fight();
                            $fight->statut = "Attente";
                            $fight->save();
                        } else {
                            echo("<script>console.log('Jeu : ' + $i + ', Match avec 2 joueurs différents');</script>");
                            $joueursDifferents = false;
                        }
                    }

                    $p1 = Player::findOrFail($partyToRun[0]->player->id);
                    $fight->rankings()->attach($partyToRun[1]->id, ['color' => 'blue']);
                    echo "blue_" . $p1->firstname . "<br>";
                    $p1->inGame = 1;
                    $p1->save();

                    $p2 = Player::findOrFail($partyToRun[0]->player->id);
                    $fight->rankings()->attach($partyToRun[0]->id, ['color' => 'red']);
                    echo "red_" . $p2->firstname;
                    $p2->inGame = 1;
                    $p2->save();

                    $matchCree = true;
                    echo("<script>console.log('Jeu : ' + $i + ', Match crée avec succès');</script>");

                } else {
                    echo("<script>console.log('Un des joueurs et en jeu, recréation d\'un nouveau match');</script>");
                }
            }
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

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

        $fight = Fight::findOrFail($id);
        if ($fight->statut == "Attente") {
            $fight->statut = "En cours";
            $fight->save();

            // Retour Pusher
            $fight = Fight::with('rankings')->FindOrFail($fight->id);
            $pusher->trigger('fights', 'fight.updated', $fight);
        }
        if ($request->get('win')) {
            $ranking = $fight->rankings[0]->game_id;
            $fight->statut = "Terminé";
            // Tu remets les joueurs en disponible à la fin du match
            foreach ($fight->rankings as $value) {
                $p = Player::findOrFail($value->player->id);
                $p->inGame = 0;
                $p->save();
            }
            $fight->save();
            Ranking::findOrFail($request->get('win'))->increment('score');
            Ranking::findOrFail($request->get('loose'))->decrement('life');


            $matchCree = false;
            while (!$matchCree) {
                $partyToRun = Ranking::where('game_id', $ranking)->where('life', 1)->get()->random(2);
                $fight = new Fight();
                $fight->statut = "Attente";
                $fight->save();

                // Si Aucun des 2 joueurs n'est en jeu
                if ($partyToRun[0]->player->inGame == 0 && $partyToRun[1]->player->inGame == 0) {
                    $joueursDifferents = true;
                    while ($joueursDifferents) {
                        // Si l'id du joueur 1 est egale à celle du joueur 2
                        if ($partyToRun[0]->player->id == $partyToRun[1]->player->id) {
                            $fight->statut = "Terminé";
                            $fight->save();
                            $fight->rankings()->attach($partyToRun[0]->id, ['color' => 'red']);
                            $fight->rankings()->attach($partyToRun[1]->id, ['color' => 'blue']);
                            Ranking::findOrFail($partyToRun[0]->id)->increment('score');
                            Ranking::findOrFail($partyToRun[1]->id)->decrement('life');

                            $joueursDifferents = true;

                            $partyToRun = Ranking::where('game_id', $ranking)->where('life', 1)->get()->random(2);
                            $fight = new Fight();
                            $fight->statut = "Attente";
                            $fight->save();
                        } else {
                            $joueursDifferents = false;
                        }
                    }

                    $p1 = Player::findOrFail($partyToRun[0]->player->id);
                    $fight->rankings()->attach($partyToRun[1]->id, ['color' => 'blue']);
                    echo "blue_" . $p1->firstname . "<br>";
                    $p1->inGame = 1;
                    $p1->save();

                    $p2 = Player::findOrFail($partyToRun[0]->player->id);
                    $fight->rankings()->attach($partyToRun[0]->id, ['color' => 'red']);
                    echo "red_" . $p2->firstname;
                    $p2->inGame = 1;
                    $p2->save();

                    $matchCree = true;
                }
            }

            /*
            // Tu sélectionnes 2 joueurs avec la vie à 1 pour le même jeu
            $partyToRun = Ranking::where('game_id', $ranking)->where('life', 1)->get()->random(2);
            // Tu créés une partie que tu mets en attente
            $fight = new Fight();
            $fight->statut = "Attente";
            $fight->save();
            // Le problème vient certainement de la boucle

            // Tant que le joueur 1 ou le joueur 2 est en jeu
            while ($partyToRun[0]->player->inGame != 0 || $partyToRun[1]->player->inGame != 0) {
                // Tu génères à nouveau une rencontre
                $partyToRun = Ranking::where('game_id', $ranking)->where('life', 1)->get()->random(2);
                // Si Aucun des 2 joueurs n'est en jeu
                if ($partyToRun[0]->player->inGame == 0 && $partyToRun[1]->player->inGame == 0) {
                    // Si l'id du joueur 1 est egale à celle du joueur 2
                    if ($partyToRun[0]->player->id == $partyToRun[1]->player->id) {
                        echo "Match terminé entre : " . $partyToRun[0]->player->name . " et " . $partyToRun[1]->player->name;
                        // Tu termines le match
                        $fight->statut = "Terminé";

                        $fight->save();
                        // Tu ajoutes les deux joueurs à la partie en leur attribuant une couleur
                        $fight->rankings()->attach($partyToRun[0]->id, ['color' => 'red']);
                        $fight->rankings()->attach($partyToRun[1]->id, ['color' => 'blue']);
                        // Tu fais gagner le joueur 1 en incrémentant son score
                        Ranking::findOrFail($partyToRun[0]->id)->increment('score');
                        // Tu fais perdre le joueur 2 en décrémentant sa vie
                        Ranking::findOrFail($partyToRun[1]->id)->decrement('life');
                        // Tu sélectionnes 2 joueurs pour une nouvelle rencontre
                        $partyToRun = Ranking::where('game_id', $ranking)->where('life', 1)->get()->random(2);
                        // Tu génère une nouvelle partie avec le statut "Attente"
                        $fight = new Fight();
                        $fight->statut = "Attente";
                        $fight->save();
                    }
                }


            }


            foreach ($partyToRun as $value) {

                if ($partyToRun[0]->id == $value->id) {
                    $fight->rankings()->attach($partyToRun[0]->id, ['color' => 'red']);
                    echo "red_" . $value->player->firstname;
                } elseif ($partyToRun[1]->id == $value->id) {
                    $fight->rankings()->attach($partyToRun[1]->id, ['color' => 'blue']);
                    echo "blue_" . $value->player->firstname . "<br>";
                }

                $p = Player::findOrFail($value->player->id);
                $p->inGame = 1;
                $p->save();

            }
            */


            // Retour Pusher
            $fight = Fight::with('rankings')->FindOrFail($fight->id);
            $pusher->trigger('fights', 'fight.finished', $fight);
        }
        return response()->json(['message' => 'Fight updated successfully!'], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public
    function destroy($id)
    {
        //
    }
}
