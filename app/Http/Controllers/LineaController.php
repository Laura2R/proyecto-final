<?php

namespace App\Http\Controllers;

use App\Models\Linea;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LineaController extends Controller
{


    public function index()
    {
        $lineas = Linea::orderBy('codigo')->paginate(20);
        return view('lineas', compact('lineas'));
    }


    public function show($id)
    {
        $linea = Linea::where('id_linea', $id)
            ->with(['paradasIda', 'paradasVuelta'])
            ->firstOrFail();

        // Verificar si la línea está en favoritos del usuario actual
        $esFavorita = false;
        if (Auth::check()) {
            $esFavorita = Auth::user()->lineasFavoritas()->where('id_linea', $id)->exists();
        }

        return view('lineas.show', compact('linea', 'esFavorita'));
    }


    public function horarios($idLinea)
    {
        $linea = Linea::where('id_linea', $idLinea)->firstOrFail();
        $horarios = $linea->horarios;

        // Agrupar horarios por tipo de día y sentido
        $horariosAgrupados = [
            'ida' => [
                'laborables' => $horarios->where('sentido', 'ida')->where('tipo_dia', 'laborables')->first(),
                'sabados' => $horarios->where('sentido', 'ida')->where('tipo_dia', 'sabados')->first(),
                'festivos' => $horarios->where('sentido', 'ida')->where('tipo_dia', 'festivos')->first(),
            ],
            'vuelta' => [
                'laborables' => $horarios->where('sentido', 'vuelta')->where('tipo_dia', 'laborables')->first(),
                'sabados' => $horarios->where('sentido', 'vuelta')->where('tipo_dia', 'sabados')->first(),
                'festivos' => $horarios->where('sentido', 'vuelta')->where('tipo_dia', 'festivos')->first(),
            ]
        ];

        return view('lineas.horarios', compact('linea', 'horariosAgrupados'));
    }

}
