<?php

use Illuminate\Database\Seeder;

class ConsoleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $consoles = [
            "Switch",
            "PS4",
            "Xbox One",
            "Megadrive mini",
            "SNES",
            "PSOne",
            "Pandora Box",
            "Wii"
        ];
        foreach ($consoles as $i){
            $c = new \App\Console();
            $c->libelle = $i;
            $c->save();

    }


    }
}
