<?php

namespace App\Http\Controllers;

use App\Models\Linea;
use App\Models\Municipio;
use App\Models\Parada;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index()
    {
        // Estadísticas para la página de inicio
        $stats = [
            'lineas' => Linea::count(),
            'paradas' => Parada::count(),
            'municipios' => Municipio::count(),
        ];

        return view('home', compact('stats'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'email' => 'required|email',
            'asunto' => 'required|string|max:255',
            'mensaje' => 'required|string|min:10',
        ]);

        return back()->with('success', '¡Mensaje enviado correctamente! Te contactaremos pronto.');
    }


    public function contacto()
    {
        return view('contact');
    }
}
