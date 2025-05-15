<?php

namespace App\Http\Controllers;

use App\Models\PuntoVenta;
use Illuminate\Http\Request;

class PuntoVentaController extends Controller
{
    public function index()
    {
        // Puedes añadir relaciones si las tienes, por ejemplo municipio o nucleo
        $puntosVenta = PuntoVenta::orderBy('id_punto')->paginate(15);
        return view('puntos_venta', compact('puntosVenta'));
    }
}
