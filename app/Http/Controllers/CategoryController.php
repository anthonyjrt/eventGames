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
    public function destroy($id)
    {
        $category = Category::findOrFail($id);
        $category->delete();

        return response()->json(['data' => 'Catégorie supprimé avec succès!']);
    }
}
