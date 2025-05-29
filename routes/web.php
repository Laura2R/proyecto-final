<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TarifaInterurbanaController;
use App\Http\Controllers\MunicipioController;
use App\Http\Controllers\NucleoController;
use App\Http\Controllers\LineaController;
use App\Http\Controllers\ParadaController;
use App\Http\Controllers\PuntoVentaController;
use App\Http\Controllers\HorarioController;

// Página de inicio
Route::get('/', [HomeController::class, 'index'])->name('home');

// Contacto
Route::get('/contacto', [HomeController::class, 'contacto'])->name('contact');
Route::post('/contacto', [HomeController::class, 'store'])->name('contact.store');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Rutas para listar cada tabla
Route::get('/municipios', [MunicipioController::class, 'index']);
Route::get('/nucleos', [NucleoController::class, 'index'])->name('nucleos.index');
Route::get('/lineas', [LineaController::class, 'index']);
Route::get('/puntos-venta', [PuntoVentaController::class, 'index']);
Route::get('/horarios', [HorarioController::class, 'index']);
Route::get('/paradas/filtro', [ParadaController::class, 'filtro'])->name('paradas.filtro');
Route::get('/paradas/filtro-linea', [ParadaController::class, 'filtroPorLinea'])->name('filtro-linea');
Route::get('/paradas/{parada}', [ParadaController::class, 'show'])->name('paradas.show');
Route::get('/tarifas', [TarifaInterurbanaController::class, 'index'])->name('tarifas.index');

require __DIR__.'/auth.php';
