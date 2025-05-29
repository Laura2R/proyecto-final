<?php

namespace App\Http\Controllers;

use App\Models\Municipio;
use App\Models\Nucleo;
use App\Models\Zona;
use Illuminate\Http\Request;

class MunicipioController extends Controller
{
    public function index(Request $request)
    {
        // Obtener todos los municipios, núcleos y zonas para los filtros
        $todosMunicipios = Municipio::orderBy('nombre')->get();
        $todosNucleos = Nucleo::orderBy('nombre')->get();
        $todasZonas = Zona::orderBy('nombre')->get();

        // Query base para núcleos con sus relaciones
        $query = Nucleo::with(['municipio', 'zona']);

        // Aplicar filtros
        if ($request->filled('municipio_id')) {
            $query->where('id_municipio', $request->municipio_id);
        }

        if ($request->filled('nucleo_search')) {
            $query->where('nombre', 'like', '%' . $request->nucleo_search . '%');
        }

        if ($request->filled('zona_id')) {
            $query->where('id_zona', $request->zona_id);
        }

        // Obtener resultados paginados
        $nucleos = $query->orderBy('nombre')->paginate(20);

        return view('municipios', compact('nucleos', 'todosMunicipios', 'todosNucleos', 'todasZonas'));
    }

}
