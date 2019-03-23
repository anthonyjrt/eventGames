<?php

use Illuminate\Database\Seeder;

class GameTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $games = [
            [
                'libelle' => 'Just Danse',
                'pegi' => 3,
                'nb_players' => 2,
                'categories' =>[6,7],
                'console_id' => 8
            ],
            [
                'libelle' => 'Fifa 19',
                'pegi' => 3,
                'nb_players' => 2,
                'categories' => [4],
                'console_id' => 2
            ],
            [
                'libelle' => 'Fifa 98',
                'pegi' => 3,
                'nb_players' => 2,
                'categories' => [4],
                'console_id' => 6
            ],
            [
                'libelle' => 'Rocket League',
                'pegi' => 3,
                'nb_players' => 2,
                'categories' => [3],
                'console_id' => 3
            ],
            [
                'libelle' => 'Sonic 2',
                'pegi' => 3,
                'nb_players' => 2,
                'categories' => [6,5],
                'console_id' => 4
            ],
            [
                'libelle' => 'StreetFighter II',
                'pegi' => 12,
                'nb_players' => 2,
                'categories' => [2],
                'console_id' => 7
            ],
            [
                'libelle' => 'Super Mario Kart',
                'pegi' => 3,
                'nb_players' => 2,
                'categories' => [3],
                'console_id' => 5
            ],
            [
                'libelle' => 'Super Smash Bros Ultimate',
                'pegi' => 12,
                'nb_players' => 2,
                'categories' => [2],
                'console_id' => 1
            ]
        ];

        for( $i= 0 ; $i <= count($games)-1 ; $i++ )
        {
            $c = new \App\Game();

                $c->libelle = $games[$i]['libelle'];
                $c->pegi = $games[$i]['pegi'];
                $c->nb_players = $games[$i]['nb_players'];
                $c->console_id = $games[$i]['console_id'];
                $c->save();
                $c->categories()->sync($games[$i]['categories']);

        }
    }
}
