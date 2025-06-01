<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TarifaInterurbana;
use App\Models\Linea;
use App\Models\Nucleo;

class TarifaInterurbanaController extends Controller
{
    public function index()
    {
        $tarifas = TarifaInterurbana::orderBy('saltos')->get();
        return view('tarifas.index', compact('tarifas'));
    }

    public function calculadora(Request $request)
    {
        $lineas = Linea::orderBy('codigo')->get();
        $nucleos = collect();
        $resultado = null;

        // Si hay una línea seleccionada, obtener los núcleos únicos de las paradas de esa línea
        if ($request->filled('linea_id')) {
            $nucleos = \DB::table('nucleos')
                ->join('paradas', 'nucleos.id_nucleo', '=', 'paradas.id_nucleo')
                ->join('linea_parada', 'paradas.id_parada', '=', 'linea_parada.id_parada')
                ->join('zonas', 'nucleos.id_zona', '=', 'zonas.id_zona')
                ->where('linea_parada.id_linea', $request->linea_id)
                ->select('nucleos.id_nucleo', 'nucleos.nombre', 'zonas.nombre as zona_nombre')
                ->distinct()
                ->orderBy('nucleos.nombre')
                ->get();
        }

        // Si se han seleccionado ambos núcleos, calcular tarifa
        if ($request->filled(['linea_id', 'nucleo_origen', 'nucleo_destino'])) {
            $resultado = $this->calcularTarifa($request->nucleo_origen, $request->nucleo_destino);
        }

        return view('tarifas.calculadora', compact('lineas', 'nucleos', 'resultado'));
    }

    private function calcularTarifa($nucleoOrigenId, $nucleoDestinoId)
    {
        try {
            // Obtener núcleos con sus zonas usando query builder para mayor control
            $nucleoOrigen = \DB::table('nucleos')
                ->join('zonas', 'nucleos.id_zona', '=', 'zonas.id_zona')
                ->where('nucleos.id_nucleo', $nucleoOrigenId)
                ->select('nucleos.*', 'zonas.nombre as zona_nombre')
                ->first();

            $nucleoDestino = \DB::table('nucleos')
                ->join('zonas', 'nucleos.id_zona', '=', 'zonas.id_zona')
                ->where('nucleos.id_nucleo', $nucleoDestinoId)
                ->select('nucleos.*', 'zonas.nombre as zona_nombre')
                ->first();

            if (!$nucleoOrigen || !$nucleoDestino) {
                return [
                    'error' => 'Núcleos no encontrados',
                    'nucleoOrigen' => null,
                    'nucleoDestino' => null,
                    'saltos' => 0,
                    'tarifa' => null
                ];
            }

            // Si no tienen zona asignada
            if (!$nucleoOrigen->zona_nombre || !$nucleoDestino->zona_nombre) {
                return [
                    'error' => 'Uno o ambos núcleos no tienen zona tarifaria asignada',
                    'nucleoOrigen' => $nucleoOrigen,
                    'nucleoDestino' => $nucleoDestino,
                    'saltos' => 0,
                    'tarifa' => null
                ];
            }

            // Calcular saltos entre zonas usando la matriz correcta
            $saltos = $this->calcularSaltos($nucleoOrigen->zona_nombre, $nucleoDestino->zona_nombre);

            // Obtener tarifa correspondiente
            $tarifa = TarifaInterurbana::where('saltos', $saltos)->first();

            if (!$tarifa) {
                return [
                    'error' => "No se encontró tarifa para {$saltos} saltos",
                    'nucleoOrigen' => $nucleoOrigen,
                    'nucleoDestino' => $nucleoDestino,
                    'saltos' => $saltos,
                    'tarifa' => null
                ];
            }

            return [
                'error' => null,
                'nucleoOrigen' => $nucleoOrigen,
                'nucleoDestino' => $nucleoDestino,
                'saltos' => $saltos,
                'tarifa' => $tarifa,
                'ahorro' => $tarifa->bs - $tarifa->tarjeta,
                'porcentajeAhorro' => $tarifa->bs > 0 ? (($tarifa->bs - $tarifa->tarjeta) / $tarifa->bs) * 100 : 0
            ];

        } catch (\Exception $e) {
            return [
                'error' => 'Error al calcular la tarifa: ' . $e->getMessage(),
                'nucleoOrigen' => null,
                'nucleoDestino' => null,
                'saltos' => 0,
                'tarifa' => null
            ];
        }
    }

    private function calcularSaltos($zonaOrigen, $zonaDestino)
    {
        // Matriz de saltos específica para Huelva basada en el mapa real
        // Refleja las conexiones directas entre zonas según la geografía
        $matrizSaltos = [
            'ZONA A' => ['ZONA A' => 0, 'ZONA B' => 1, 'ZONA C' => 2, 'ZONA D' => 3, 'ZONA E' => 4, 'ZONA F' => 5],
            'ZONA B' => ['ZONA A' => 1, 'ZONA B' => 0, 'ZONA C' => 1, 'ZONA D' => 2, 'ZONA E' => 3, 'ZONA F' => 4],
            'ZONA C' => ['ZONA A' => 2, 'ZONA B' => 1, 'ZONA C' => 0, 'ZONA D' => 1, 'ZONA E' => 2, 'ZONA F' => 3],
            'ZONA D' => ['ZONA A' => 3, 'ZONA B' => 2, 'ZONA C' => 1, 'ZONA D' => 0, 'ZONA E' => 1, 'ZONA F' => 2],
            'ZONA E' => ['ZONA A' => 4, 'ZONA B' => 3, 'ZONA C' => 2, 'ZONA D' => 1, 'ZONA E' => 0, 'ZONA F' => 1],
            'ZONA F' => ['ZONA A' => 5, 'ZONA B' => 4, 'ZONA C' => 3, 'ZONA D' => 2, 'ZONA E' => 1, 'ZONA F' => 0]
        ];

        return $matrizSaltos[$zonaOrigen][$zonaDestino];
    }
}
