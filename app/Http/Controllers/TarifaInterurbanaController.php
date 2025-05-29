<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TarifaInterurbana;

class TarifaInterurbanaController extends Controller
{
    public function index()
    {
        $tarifas = TarifaInterurbana::orderBy('saltos')->get();
        return view('tarifas.index', compact('tarifas'));
    }

}
