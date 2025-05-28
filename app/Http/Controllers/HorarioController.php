<?php
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

        if ($request->filled('linea_id')) {
            $lineaSeleccionada = Linea::where('id_linea', $request->linea_id)->firstOrFail();

            $horarios = Horario::where('id_linea', $request->linea_id)
                ->with('frecuencia')
                ->whereNotNull('nucleos')
                ->get();

            // Aplicar ordenamiento mejorado
            $sortBy = $request->get('sort', 'horas');
            $sortDirection = $request->get('direction', 'asc');

            if ($sortBy === 'horas') {
                $horarios = $horarios->sortBy(function($horario) {
                    // Buscar la primera hora válida en el array
                    $primeraHoraValida = null;

                    if ($horario->horas && is_array($horario->horas)) {
                        foreach ($horario->horas as $hora) {
                            if ($hora && $hora !== '--' && $hora !== null && $hora !== '') {
                                // Validar que tenga formato HH:MM
                                if (preg_match('/^\d{1,2}:\d{2}$/', $hora)) {
                                    $primeraHoraValida = $hora;
                                    break;
                                }
                            }
                        }
                    }

                    // Si no encontramos hora válida, poner al final
                    if (!$primeraHoraValida) {
                        return 99999;
                    }

                    // Convertir hora a minutos para ordenamiento correcto
                    $partes = explode(':', $primeraHoraValida);
                    if (count($partes) !== 2) {
                        return 99999;
                    }

                    $hora = (int)$partes[0];
                    $minuto = (int)$partes[1];

                    // Validar rangos válidos
                    if ($hora < 0 || $hora > 23 || $minuto < 0 || $minuto > 59) {
                        return 99999;
                    }

                    return $hora * 60 + $minuto;
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

            $horariosIda = $horarios->where('sentido', 'ida')->values();
            $horariosVuelta = $horarios->where('sentido', 'vuelta')->values();
        }

        return view('horarios', compact(
            'lineas',
            'lineaSeleccionada',
            'horarios',
            'horariosIda',
            'horariosVuelta',
            'frecuencias'
        ));
    }


}
