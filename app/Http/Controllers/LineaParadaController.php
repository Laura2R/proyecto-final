<?php

namespace App\Http\Controllers;

use App\Models\LineaParada;
//use Illuminate\Http\Request;

class LineaParadaController extends Controller
{
    public function index()
    {
        $lineaParada = LineaParada::with(['linea', 'parada'])
            ->orderBy('id_linea')
            ->orderBy('orden')
            ->paginate(20);

        return view('linea_parada', compact('lineaParada'));
    }
}
