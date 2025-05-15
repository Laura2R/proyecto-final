<?php

namespace App\Http\Controllers;

use App\Models\Horario;
use Illuminate\Http\Request;

class HorarioController extends Controller
{
    /**
     * Muestra el listado paginado de horarios
     */
    public function index()
    {
        $horarios = Horario::with(['linea' => function($query) {
            $query->select('id', 'id_linea', 'nombre'); // Optimiza la consulta
        }])
            ->orderBy('id_linea')
            ->orderBy('sentido')
            ->paginate(20);

        return view('horarios', compact('horarios'));
    }

    /**
     * Muestra los detalles de un horario específico
     */
    public function show($id)
    {
        $horario = Horario::with(['linea', 'frecuencia'])
            ->findOrFail($id);

        return view('horarios.show', compact('horario'));
    }

    /**
     * Sincroniza horarios desde la API (usando tu servicio existente)
     */
    public function sync()
    {
        // Este método podría llamar a tu servicio de sincronización
        $result = app('App\Services\ApiService')->syncHorarios();

        return redirect()->route('horarios.index')
            ->with('success', "Sincronizados {$result} horarios correctamente");
    }
}
