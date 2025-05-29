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

    public function show($saltos)
    {
        $tarifa = TarifaInterurbana::getPorSaltos($saltos);

        if (!$tarifa) {
            abort(404, 'Tarifa no encontrada');
        }

        return view('tarifas.show', compact('tarifa'));
    }

    /**
     * API endpoint para obtener tarifa por saltos
     */
    public function api($saltos)
    {
        $tarifa = TarifaInterurbana::getPorSaltos($saltos);

        if (!$tarifa) {
            return response()->json(['error' => 'Tarifa no encontrada'], 404);
        }

        return response()->json($tarifa);
    }

    /**
     * API endpoint para obtener todas las tarifas
     */
    public function apiIndex()
    {
        $tarifas = TarifaInterurbana::getOrdenadas();
        return response()->json(['tarifas' => $tarifas]);
    }
}
