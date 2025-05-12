<?php

namespace App\Http\Controllers;

use App\Models\Municipio;
use Illuminate\Http\Request;

class MunicipioController extends Controller
{
    public function index()
    {
        $municipios = Municipio::orderBy('nombre')->get();
        return view('municipios.index', compact('municipios'));
    }

    public function show($id)
    {
        $municipio = Municipio::where('id_municipio', $id)
            ->with(['nucleos', 'paradas', 'puntosVenta'])
            ->firstOrFail();

        return view('municipios.show', compact('municipio'));
    }
}
