<?php

namespace App\Http\Controllers;


use App\Models\Linea;
use App\Models\Parada;
use App\Models\Municipio;
use App\Models\Nucleo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ParadaController extends Controller
{

    public function index()
    {
        $paradas = Parada::with(['nucleo.municipio', 'zona'])
            ->orderBy('nombre')
            ->paginate(15);

        return view('paradas', compact('paradas'));
    }

   /* public function filtro(Request $request)
    {
        $municipios = Municipio::orderBy('nombre')->get();

        // Si hay municipio seleccionado, carga los núcleos de ese municipio
        $nucleos = collect();
        if ($request->filled('municipio_id')) {
            $nucleos = Nucleo::where('id_municipio', $request->municipio_id)->orderBy('nombre')->get();
        }

        // Filtra las paradas según los filtros seleccionados
        $query = Parada::query()->with(['municipio', 'nucleo']);
        if ($request->filled('municipio_id')) {
            $query->where('id_municipio', $request->municipio_id);
        }
        if ($request->filled('nucleo_id')) {
            $query->where('id_nucleo', $request->nucleo_id);
        }
        $paradas = $query->paginate(15);

        return view('paradas.filtro', compact('municipios', 'nucleos', 'paradas'));
    }*/

    public function filtroPorLinea(Request $request)
    {
        // Obtener todas las líneas
        $lineas = Linea::select('id_linea', 'codigo', 'nombre')->orderBy('codigo')->get();

        $lineaSeleccionada = null;
        $municipios = collect();
        $nucleos = collect();
        $paradasAgrupadas = collect();

        if ($request->filled('linea_id')) {
            // Obtener línea seleccionada
            $lineaSeleccionada = Linea::where('id_linea', $request->linea_id)->firstOrFail();

            // Construir consulta base
            $query = Parada::with(['nucleo.municipio'])
                ->join('linea_parada', 'paradas.id_parada', '=', 'linea_parada.id_parada')
                ->where('linea_parada.id_linea', $request->linea_id)
                ->select(
                    'paradas.*',
                    'linea_parada.sentido as pivot_sentido',
                    'linea_parada.orden as pivot_orden'
                );

            // Aplicar filtros adicionales
            if ($request->filled('municipio_id')) {
                $query->where('paradas.id_municipio', $request->municipio_id);
            }

            if ($request->filled('nucleo_id')) {
                $query->where('paradas.id_nucleo', $request->nucleo_id);
            }

            // Ejecutar consulta y ordenar
            $paradas = $query->orderBy('pivot_sentido')
                ->orderBy('pivot_orden')
                ->get();

            // Agrupar por sentido
            $paradasAgrupadas = $paradas->groupBy('pivot_sentido');

            // Obtener municipios y núcleos únicos (de toda la línea, sin filtros)
            $municipios = Municipio::whereIn('id_municipio',
                Parada::join('linea_parada', 'paradas.id_parada', '=', 'linea_parada.id_parada')
                    ->where('linea_parada.id_linea', $request->linea_id)
                    ->pluck('paradas.id_municipio')
                    ->unique()
            )->get();

            $nucleos = Nucleo::whereIn('id_nucleo',
                Parada::join('linea_parada', 'paradas.id_parada', '=', 'linea_parada.id_parada')
                    ->where('linea_parada.id_linea', $request->linea_id)
                    ->pluck('paradas.id_nucleo')
                    ->unique()
            )->get();
        }

        return view('filtro-linea', compact(
            'lineas',
            'lineaSeleccionada',
            'municipios',
            'nucleos',
            'paradasAgrupadas'
        ));
    }

    public function show(Parada $parada)
    {
        // Carga relaciones si quieres
        $parada->load(['nucleo.municipio', 'zona']);
        return view('paradas.show', compact('parada'));
    }

}
