<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Linea;
use App\Models\Parada;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class FavoritoController extends Controller
{


    public function toggleLinea(Request $request)
    {
        try {
            $user = Auth::user();
            $lineaId = $request->linea_id;

            $exists = $user->lineasFavoritas()->where('favoritos_lineas.id_linea', $lineaId)->exists();

            if ($exists) {
                $user->lineasFavoritas()->detach($lineaId);
                $isFavorite = false;
                $message = 'Línea eliminada de favoritos';
            } else {
                $user->lineasFavoritas()->attach($lineaId);
                $isFavorite = true;
                $message = 'Línea añadida a favoritos';
            }

            return response()->json([
                'success' => true,
                'is_favorite' => $isFavorite,
                'message' => $message
            ]);
        } catch (\Exception $e) {
            Log::error('Error en toggleLinea: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function toggleParada(Request $request)
    {
        try {
            $user = Auth::user();
            $paradaId = $request->parada_id;

            $exists = $user->paradasFavoritas()->where('favoritos_paradas.id_parada', $paradaId)->exists();

            if ($exists) {
                $user->paradasFavoritas()->detach($paradaId);
                $isFavorite = false;
                $message = 'Parada eliminada de favoritos';
            } else {
                $user->paradasFavoritas()->attach($paradaId);
                $isFavorite = true;
                $message = 'Parada añadida a favoritos';
            }

            return response()->json([
                'success' => true,
                'is_favorite' => $isFavorite,
                'message' => $message
            ]);
        } catch (\Exception $e) {
            Log::error('Error en toggleParada: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function lineasFavoritas()
    {
        $lineasFavoritas = Auth::user()->lineasFavoritas()->paginate(10);
        return view('favoritos.lineas', compact('lineasFavoritas'));
    }

    public function paradasFavoritas()
    {
        $paradasFavoritas = Auth::user()->paradasFavoritas()->with(['municipio', 'nucleo'])->paginate(10);
        return view('favoritos.paradas', compact('paradasFavoritas'));
    }
}
