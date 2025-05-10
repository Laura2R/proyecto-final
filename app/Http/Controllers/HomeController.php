<?php

namespace App\Http\Controllers;

use App\Models\Linea;
use App\Models\Municipio;
use App\Models\Parada;
use App\Models\PuntoVenta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index()
    {
        $totalLineas = Linea::count();
        $totalParadas = Parada::count();
        $totalMunicipios = Municipio::count();
        $totalPuntosVenta = PuntoVenta::count();

        // Si el usuario está autenticado, obtener sus favoritos
        $paradasFavoritas = collect();
        $lineasFavoritas = collect();

        if (Auth::check()) {
            $user = Auth::user();
            $paradasFavoritas = $user->paradasFavoritas()->with(['municipio', 'nucleo'])->take(5)->get();
            $lineasFavoritas = $user->lineasFavoritas()->take(5)->get();
        }

        return view('home', compact(
            'totalLineas',
            'totalParadas',
            'totalMunicipios',
            'totalPuntosVenta',
            'paradasFavoritas',
            'lineasFavoritas'
        ));
    }

    public function buscar(Request $request)
    {
        $query = $request->input('q');

        if (empty($query)) {
            return redirect()->route('home');
        }

        $lineas = Linea::where('nombre', 'like', "%{$query}%")
            ->orWhere('codigo', 'like', "%{$query}%")
            ->take(10)
            ->get();

        $paradas = Parada::where('nombre', 'like', "%{$query}%")
            ->orWhere('descripcion', 'like', "%{$query}%")
            ->with(['municipio', 'nucleo'])
            ->take(10)
            ->get();

        $municipios = Municipio::where('nombre', 'like', "%{$query}%")
            ->take(10)
            ->get();

        $puntosVenta = PuntoVenta::where('nombre', 'like', "%{$query}%")
            ->orWhere('direccion', 'like', "%{$query}%")
            ->with('municipio')
            ->take(10)
            ->get();

        return view('buscar', compact('query', 'lineas', 'paradas', 'municipios', 'puntosVenta'));
    }
}
