<?php

namespace App\Http\Controllers;

use App\Console;
use Illuminate\Http\Request;

class ConsoleController extends Controller
{
    public function index(){
        $consoles = Console::orderBy('libelle', 'asc')

            ->get();
        return response()->json($consoles);
    }
    public function store(Request $request){
        Console::create($request);
    }
}
