<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FavoritoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = Auth::user();
        $paradasFavoritas = $user->paradasFavoritas()->with(['municipio', 'nucleo'])->get();
        $lineasFavoritas = $user->lineasFavoritas()->get();

        return view('favoritos.index', compact('paradasFavoritas', 'lineasFavoritas'));
    }
}
