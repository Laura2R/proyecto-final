<?php
// app/Http/Controllers/HorarioController.php
namespace App\Http\Controllers;

use App\Models\Horario;
use App\Models\Frecuencia;
use App\Models\Linea;
use Illuminate\Http\Request;

class HorarioController extends Controller
{
    public function index(Request $request)
    {
        $lineas = Linea::orderBy('codigo')->get();

        $lineaSeleccionada = null;
        $horarios = collect();
        $horariosIda = collect();
        $horariosVuelta = collect();
        $frecuencias = Frecuencia::all();
        $planificadores = collect();

        if ($request->filled('linea_id')) {
            $lineaSeleccionada = Linea::where('id_linea', $request->linea_id)->firstOrFail();

            // Obtener todos los planificadores de la línea
            $planificadores = Horario::where('id_linea', $request->linea_id)
                ->select('id_planificador', 'fecha_inicio', 'fecha_fin')
                ->distinct()
                ->orderBy('fecha_inicio')
                ->get();

            // Obtener horarios del planificador seleccionado o el más reciente
            $planificadorSeleccionado = $request->get('planificador_id', $planificadores->first()?->id_planificador);

            $horarios = Horario::where('id_linea', $request->linea_id)
                ->where('id_planificador', $planificadorSeleccionado)
                ->with('frecuencia')
                ->whereNotNull('nucleos')
                ->get();

            // Aplicar ordenamiento en la colección
            $sortBy = $request->get('sort', 'horas'); // Por defecto ordenar por horas
            $sortDirection = $request->get('direction', 'asc');

            if ($sortBy === 'horas') {
                $horarios = $horarios->sortBy(function($horario) {
                    // Obtener la primera hora y convertirla a minutos para ordenar correctamente
                    $primeraHora = $horario->horas[0] ?? '00:00';
                    list($hora, $minuto) = explode(':', $primeraHora);
                    return (int)$hora * 60 + (int)$minuto;
                });

                if ($sortDirection === 'desc') {
                    $horarios = $horarios->reverse();
                }
            } elseif ($sortBy === 'frecuencia') {
                $horarios = $horarios->sortBy('frecuencia_acronimo');

                if ($sortDirection === 'desc') {
                    $horarios = $horarios->reverse();
                }
            }

            // Separar por sentido manteniendo el orden
            $horariosIda = $horarios->where('sentido', 'ida')->values();
            $horariosVuelta = $horarios->where('sentido', 'vuelta')->values();
        }

        return view('horarios', compact(
            'lineas',
            'lineaSeleccionada',
            'horarios',
            'horariosIda',
            'horariosVuelta',
            'frecuencias',
            'planificadores'
        ));
    }

}
