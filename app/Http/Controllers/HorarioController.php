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
        // Obtener todas las líneas para el selector
        $lineas = Linea::orderBy('codigo')->get();

        // Inicializar variables
        $lineaSeleccionada = null;
        $horarios = collect();
        $horariosIda = collect();
        $horariosVuelta = collect();
        $frecuencias = Frecuencia::all();

        // Si hay una línea seleccionada
        if ($request->filled('linea_id')) {
            $lineaSeleccionada = Linea::where('id_linea', $request->linea_id)->firstOrFail();

            // Obtener todos los horarios de la línea con núcleos no nulos
            $horarios = Horario::where('id_linea', $request->linea_id)
                ->with('frecuencia')
                ->whereNotNull('nucleos') // Solo horarios que tengan núcleos
                ->orderBy('sentido')
                ->orderBy('horas->0') // Ordenar por la primera hora
                ->get();

            // Separar por sentido
            $horariosIda = $horarios->where('sentido', 'ida');
            $horariosVuelta = $horarios->where('sentido', 'vuelta');
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
