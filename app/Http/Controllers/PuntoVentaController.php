<?php

namespace App\Http\Controllers;

use App\Models\PuntoVenta;
use Illuminate\Http\Request;
use App\Models\Municipio;

class PuntoVentaController extends Controller
{
    public function index(Request $request)
    {
        $query = PuntoVenta::with('municipio');

        // Filtrar por municipio si se proporciona
        if ($request->filled('municipio_id')) {
            $query->where('id_municipio', $request->municipio_id);
        }

        // Obtener puntos de venta paginados para la tabla
        $puntosVenta = $query->paginate(5);

        // Obtener todos los puntos de venta para el mapa
        $todosPuntosVenta = PuntoVenta::select('id', 'tipo', 'municipio', 'direccion', 'latitud', 'longitud')
            ->get();

        // Obtener municipios para el filtro
        $municipios = Municipio::orderBy('nombre')->get();

        return view('puntos_venta', compact('puntosVenta', 'todosPuntosVenta', 'municipios'));
    }

}
