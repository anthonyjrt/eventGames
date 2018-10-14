<?php

use Illuminate\Database\Seeder;

class CategoryTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $categories = [
            "Beat them all",
            "Combat",
            "Course",
            "Football",
            "Plates-formes",
            "Fils Rouge"
        ];
        foreach ($categories as $i){
            $c = new \App\Category();
            $c->libelle = $i;
            $c->save();

        }

    }
}
