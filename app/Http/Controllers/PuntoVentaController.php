<?php

namespace App\Http\Controllers;

use App\Models\PuntoVenta;
use Illuminate\Http\Request;
use App\Models\Municipio;
use App\Models\Nucleo;

class PuntoVentaController extends Controller
{
    public function index(Request $request)
    {
        $query = PuntoVenta::with(['municipio', 'nucleo'])
            ->where('tipo', '!=', 'Pruebas')
            ->where('direccion', '!=', 'eliminar');//Excluimos tipo Pruebas y dirección eliminar

        // Filtrar por municipio si se proporciona
        if ($request->filled('municipio_id')) {
            $query->where('id_municipio', $request->municipio_id);
        }

        // Filtrar por núcleo si se proporciona
        if ($request->filled('nucleo_id')) {
            $query->where('id_nucleo', $request->nucleo_id);
        }

        // Obtener puntos de venta paginados para la tabla
        $puntosVenta = $query->paginate(5);

        // Obtener todos los puntos de venta para el mapa con relaciones
        $todosPuntosVenta = PuntoVenta::with(['municipio', 'nucleo'])
            ->where('tipo', '!=', 'Pruebas')
            ->where('direccion', '!=', 'eliminar')
            ->get()
            ->map(function($punto) {
                return [
                    'id_punto' => $punto->id_punto,
                    'tipo' => $punto->tipo,
                    'direccion' => $punto->direccion,
                    'latitud' => $punto->latitud,
                    'longitud' => $punto->longitud,
                    'municipio' => [
                        'nombre' => $punto->municipio ? $punto->municipio->nombre : 'Sin datos'
                    ],
                    'nucleo' => [
                        'nombre' => $punto->nucleo ? $punto->nucleo->nombre : 'Sin datos'
                    ]
                ];
            });

        // Obtener municipios para el filtro
        $municipios = Municipio::orderBy('nombre')->get();

        // Obtener núcleos para el filtro
        $nucleos = collect();
        if ($request->filled('municipio_id')) {
            $nucleos = Nucleo::where('id_municipio', $request->municipio_id)
                ->orderBy('nombre')
                ->get();
        }

        return view('puntos_venta', compact('puntosVenta', 'todosPuntosVenta', 'municipios', 'nucleos'));
    }

}
