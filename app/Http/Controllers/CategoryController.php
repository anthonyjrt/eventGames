<?php

namespace App\Http\Controllers;

use App\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(){
        $consoles = Category::orderBy('libelle', 'asc')

            ->get();
        return response()->json($consoles);
    }
    public function store(Request $request){
        Category::create($request->all());
    }
}
