<?php

namespace App\Http\Controllers;

use App\Models\Nucleo;
use Illuminate\Http\Request;

class NucleoController extends Controller
{
    public function index()
    {
        $nucleos = Nucleo::with('municipio')
            ->orderBy('nombre')
            ->get();

        return view('nucleos.index', compact('nucleos'));
    }

    public function show($id)
    {
        $nucleo = Nucleo::where('id_nucleo', $id)
            ->with(['municipio', 'zona', 'paradas'])
            ->firstOrFail();

        return view('nucleos.show', compact('nucleo'));
    }
}

