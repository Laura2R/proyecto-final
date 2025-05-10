<?php

namespace App\Http\Controllers;

use App\Models\Linea;
use App\Models\Municipio;
use App\Models\Parada;
use App\Services\CtanApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LineaController extends Controller
{
    protected $ctanApiService;

    public function __construct(CtanApiService $ctanApiService)
    {
        $this->ctanApiService = $ctanApiService;
    }

    public function index()
    {
        $lineas = Linea::orderBy('codigo')->get();
        return view('lineas.index', compact('lineas'));
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

    public function porMunicipio($idMunicipio)
    {
        $municipio = Municipio::where('id_municipio', $idMunicipio)->firstOrFail();

        // Obtener paradas del municipio
        $paradas = Parada::where('id_municipio', $idMunicipio)->get();

        // Obtener líneas que pasan por esas paradas
        $lineasIds = [];
        foreach ($paradas as $parada) {
            $lineasParada = $parada->lineas()->pluck('id_linea')->toArray();
            $lineasIds = array_merge($lineasIds, $lineasParada);
        }

        $lineas = Linea::whereIn('id_linea', array_unique($lineasIds))
            ->orderBy('codigo')
            ->get();

        return view('lineas.por-municipio', compact('lineas', 'municipio'));
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

    public function toggleFavorito($idLinea)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        $linea = Linea::where('id_linea', $idLinea)->firstOrFail();

        if ($user->lineasFavoritas()->where('id_linea', $idLinea)->exists()) {
            $user->lineasFavoritas()->detach($idLinea);
            $mensaje = 'Línea eliminada de favoritos';
        } else {
            $user->lineasFavoritas()->attach($idLinea);
            $mensaje = 'Línea añadida a favoritos';
        }

        return redirect()->back()->with('success', $mensaje);
    }
}
