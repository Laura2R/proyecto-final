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
        // Query base para excluir datos no válidos
        $queryBase = PuntoVenta::with(['municipio', 'nucleo'])
            ->where('tipo', '!=', 'Pruebas')
            ->where('direccion', '!=', 'eliminar');

        // Query para la tabla
        $queryTabla = clone $queryBase;

        // Query para el mapa
        $queryMapa = clone $queryBase;

        // Aplicar filtros a ambas queries
        if ($request->filled('municipio_id')) {
            $queryTabla->where('id_municipio', $request->municipio_id);
            $queryMapa->where('id_municipio', $request->municipio_id);
        }

        // Solo aplicar filtro de núcleo si pertenece al municipio
        if ($request->filled('nucleo_id') && $request->filled('municipio_id')) {
            // Verificar que el núcleo pertenece al municipio seleccionado
            $nucleoValido = Nucleo::where('id_nucleo', $request->nucleo_id)
                ->where('id_municipio', $request->municipio_id)
                ->exists();

            if ($nucleoValido) {
                $queryTabla->where('id_nucleo', $request->nucleo_id);
                $queryMapa->where('id_nucleo', $request->nucleo_id);
            }
        } elseif ($request->filled('nucleo_id') && !$request->filled('municipio_id')) {
            // Si solo se selecciona núcleo sin municipio, aplicar el filtro normalmente
            $queryTabla->where('id_nucleo', $request->nucleo_id);
            $queryMapa->where('id_nucleo', $request->nucleo_id);
        }

        // Obtener puntos de venta paginados para la tabla
        $puntosVenta = $queryTabla->paginate(5);

        // Obtener los puntos para el mapa
        $puntosVentaMapa = $queryMapa->get()->map(function($punto) {
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

        return view('puntos_venta', compact('puntosVenta', 'puntosVentaMapa', 'municipios', 'nucleos'));
    }
}
