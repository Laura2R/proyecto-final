<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Linea;
use App\Models\Parada;
use Illuminate\Support\Facades\Auth;

class FavoritoController extends Controller
{

    // Líneas favoritas
    public function toggleLinea(Request $request)
    {
        $user = Auth::user();
        $lineaId = $request->linea_id;

        $exists = $user->lineasFavoritas()->where('id_linea', $lineaId)->exists();

        if ($exists) {
            $user->lineasFavoritas()->detach($lineaId);
            $isFavorite = false;
        } else {
            $user->lineasFavoritas()->attach($lineaId);
            $isFavorite = true;
        }

        return response()->json([
            'success' => true,
            'is_favorite' => $isFavorite,
            'message' => $isFavorite ? 'Línea añadida a favoritos' : 'Línea eliminada de favoritos'
        ]);
    }

    // Paradas favoritas
    public function toggleParada(Request $request)
    {
        $user = Auth::user();
        $paradaId = $request->parada_id;

        $exists = $user->paradasFavoritas()->where('id_parada', $paradaId)->exists();

        if ($exists) {
            $user->paradasFavoritas()->detach($paradaId);
            $isFavorite = false;
        } else {
            $user->paradasFavoritas()->attach($paradaId);
            $isFavorite = true;
        }

        return response()->json([
            'success' => true,
            'is_favorite' => $isFavorite,
            'message' => $isFavorite ? 'Parada añadida a favoritos' : 'Parada eliminada de favoritos'
        ]);
    }

    // Vista de líneas favoritas
    public function lineasFavoritas()
    {
        $lineasFavoritas = Auth::user()->lineasFavoritas()->paginate(10);
        return view('favoritos.lineas', compact('lineasFavoritas'));
    }

    // Vista de paradas favoritas
    public function paradasFavoritas()
    {
        $paradasFavoritas = Auth::user()->paradasFavoritas()->with(['municipio', 'nucleo'])->paginate(10);
        return view('favoritos.paradas', compact('paradasFavoritas'));
    }

}
