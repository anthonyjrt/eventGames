<?php

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\User::create([
            'name' => 'admin',
            'email' => 'anthonyganz@hotmail.fr',
            'password' => bcrypt('secret'),
            'remember_token' => str_random(10),
        ]);
    }
}
