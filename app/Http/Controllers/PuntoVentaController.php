<?php

namespace App\Http\Controllers;

use App\Models\PuntoVenta;
use App\Models\Municipio;
use Illuminate\Http\Request;

class PuntoVentaController extends Controller
{
    public function index()
    {
        $puntosVenta = PuntoVenta::with('municipio')
            ->orderBy('nombre')
            ->paginate(20);

        return view('puntos-venta.index', compact('puntosVenta'));
    }

    public function show($id)
    {
        $puntoVenta = PuntoVenta::where('id_punto', $id)
            ->with('municipio')
            ->firstOrFail();

        return view('puntos-venta.show', compact('puntoVenta'));
    }

    public function porMunicipio($idMunicipio)
    {
        $municipio = Municipio::where('id_municipio', $idMunicipio)->firstOrFail();
        $puntosVenta = PuntoVenta::where('id_municipio', $idMunicipio)
            ->orderBy('nombre')
            ->get();

        return view('puntos-venta.por-municipio', compact('puntosVenta', 'municipio'));
    }

    public function buscar(Request $request)
    {
        $query = $request->input('q');

        $puntosVenta = PuntoVenta::where('nombre', 'like', "%{$query}%")
            ->orWhere('direccion', 'like', "%{$query}%")
            ->with('municipio')
            ->orderBy('nombre')
            ->paginate(20);

        return view('puntos-venta.buscar', compact('puntosVenta', 'query'));
    }
}

