<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class sqlite extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sqlite:createdb';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        if (DB::getDriverName() === 'sqlite') {
            $path = DB::getConfig('database');
            if (!file_exists($path) && is_dir(dirname($path))) {
                touch($path);
                $this->info("La base de données database.sqlite a bien été créée");
            } else {
                $this->info("La base de données database.sqlite existe déjà");
            }
        }

    }
}
