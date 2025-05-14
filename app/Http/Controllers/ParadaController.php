<?php

namespace App\Http\Controllers;

namespace App\Http\Controllers;

use App\Models\Parada;
use App\Models\Municipio;
use App\Models\Nucleo;
use App\Services\CtanApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ParadaController extends Controller
{
//    protected $ctanApiService;
//
//    public function __construct(CtanApiService $ctanApiService)
//    {
//        $this->ctanApiService = $ctanApiService;
//    }

    public function index()
    {
        $paradas = Parada::with(['municipio', 'nucleo'])
            ->orderBy('nombre')
            ->paginate(10);

        return view('paradas', compact('paradas'));
    }

    public function show($id)
    {
        $parada = Parada::where('id_parada', $id)
            ->with(['municipio', 'nucleo', 'zona', 'lineas'])
            ->firstOrFail();

        // Obtener servicios en tiempo real
        $servicios = $this->ctanApiService->getServiciosParada($id);

        // Verificar si la parada está en favoritos del usuario actual
        $esFavorita = false;
        if (Auth::check()) {
            $esFavorita = Auth::user()->paradasFavoritas()->where('id_parada', $id)->exists();
        }

        return view('paradas.show', compact('parada', 'servicios', 'esFavorita'));
    }

    public function porMunicipio($idMunicipio)
    {
        $municipio = Municipio::where('id_municipio', $idMunicipio)->firstOrFail();
        $paradas = Parada::where('id_municipio', $idMunicipio)
            ->with('nucleo')
            ->orderBy('nombre')
            ->get();

        return view('paradas.por-municipio', compact('paradas', 'municipio'));
    }

    public function porNucleo($idNucleo)
    {
        $nucleo = Nucleo::where('id_nucleo', $idNucleo)
            ->with('municipio')
            ->firstOrFail();

        $paradas = Parada::where('id_nucleo', $idNucleo)
            ->orderBy('nombre')
            ->get();

        return view('paradas.por-nucleo', compact('paradas', 'nucleo'));
    }

    public function servicios($idParada)
    {
        $parada = Parada::where('id_parada', $idParada)
            ->with(['municipio', 'nucleo'])
            ->firstOrFail();

        $servicios = $this->ctanApiService->getServiciosParada($idParada);

        return view('paradas.servicios', compact('parada', 'servicios'));
    }

    public function buscar(Request $request)
    {
        $query = $request->input('q');

        $paradas = Parada::where('nombre', 'like', "%{$query}%")
            ->orWhere('descripcion', 'like', "%{$query}%")
            ->with(['municipio', 'nucleo'])
            ->orderBy('nombre')
            ->paginate(20);

        return view('paradas.buscar', compact('paradas', 'query'));
    }

    public function toggleFavorito($idParada)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        $parada = Parada::where('id_parada', $idParada)->firstOrFail();

        if ($user->paradasFavoritas()->where('id_parada', $idParada)->exists()) {
            $user->paradasFavoritas()->detach($idParada);
            $mensaje = 'Parada eliminada de favoritos';
        } else {
            $user->paradasFavoritas()->attach($idParada);
            $mensaje = 'Parada añadida a favoritos';
        }

        return redirect()->back()->with('success', $mensaje);
    }
}
