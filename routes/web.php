<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ZonaController;
use App\Http\Controllers\MunicipioController;
use App\Http\Controllers\NucleoController;
use App\Http\Controllers\LineaController;
use App\Http\Controllers\ParadaController;
use App\Http\Controllers\PuntoVentaController;
use App\Http\Controllers\HorarioController;
//use App\Http\Controllers\TarifaController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Rutas para listar cada tabla
Route::get('/zonas', [ZonaController::class, 'index'])->name('zonas.index');
Route::get('/municipios', [MunicipioController::class, 'index']);
Route::get('/nucleos', [NucleoController::class, 'index'])->name('nucleos.index');
Route::get('/lineas', [LineaController::class, 'index'])->name('lineas.index');
Route::get('/paradas', [ParadaController::class, 'index'])->name('paradas.index');
Route::get('/puntos-venta', [PuntoVentaController::class, 'index'])->name('puntosventa.index');
Route::get('/horarios', [HorarioController::class, 'index'])->name('horarios.index');
Route::get('/tarifas', [TarifaController::class, 'index'])->name('tarifas.index');

require __DIR__.'/auth.php';
