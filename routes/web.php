<?php

use App\Http\Controllers\FavoritoController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RecargaController;
use App\Http\Controllers\CardController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TarifaInterurbanaController;
use App\Http\Controllers\MunicipioController;
use App\Http\Controllers\NucleoController;
use App\Http\Controllers\LineaController;
use App\Http\Controllers\ParadaController;
use App\Http\Controllers\PuntoVentaController;
use App\Http\Controllers\HorarioController;
use App\Http\Controllers\BilleteController;

// Página de inicio
Route::get('/', [HomeController::class, 'index'])->name('home');

// Contacto
Route::get('/contacto', [HomeController::class, 'contacto'])->name('contact');
Route::post('/contacto', [HomeController::class, 'store'])->name('contact.store');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    // Perfil
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Gestión de tarjetas
    Route::resource('cards', CardController::class)->except(['edit', 'update']);

    // Recargas
    Route::get('/recarga', [RecargaController::class, 'selectCard'])->name('recarga.select');
    Route::get('/recarga/{card}', [RecargaController::class, 'showForm'])->name('recarga.form');
    Route::post('/recarga/{card}', [RecargaController::class, 'procesar'])->name('recarga.procesar');
    Route::get('/recarga/{card}/success', [RecargaController::class, 'success'])->name('recarga.success');
    Route::get('/recarga/{card}/pending', [RecargaController::class, 'pending'])->name('recarga.pending');
    Route::get('/recarga/{card}/cancel', [RecargaController::class, 'cancel'])->name('recarga.cancel');

    // Billetes
    Route::post('/procesar-pago', [BilleteController::class, 'procesar'])->name('procesar.pago');
    Route::get('/mis-billetes', [BilleteController::class, 'misBilletes'])->name('billetes.mis-billetes');
    Route::get('/billete/{transaccion}', [BilleteController::class, 'mostrarBillete'])->name('billete.mostrar');
    Route::get('/billete/{transaccion}/descargar', [BilleteController::class, 'descargarPDF'])->name('billete.descargar');
    Route::delete('/billete/{transaccion}', [BilleteController::class, 'destroy'])->name('billete.destroy');

    // Favoritos
    Route::post('/favoritos/linea', [FavoritoController::class, 'toggleLinea'])->name('favoritos.toggle.linea');
    Route::post('/favoritos/parada', [FavoritoController::class, 'toggleParada'])->name('favoritos.toggle.parada');
    Route::get('/mis-lineas-favoritas', [FavoritoController::class, 'lineasFavoritas'])->name('lineas.favoritas');
    Route::get('/mis-paradas-favoritas', [FavoritoController::class, 'paradasFavoritas'])->name('paradas.favoritas');


});

// Rutas para listar cada tabla (públicas)
Route::get('/municipios', [MunicipioController::class, 'index'])->name('municipios.index');
Route::get('/nucleos', [NucleoController::class, 'index'])->name('nucleos.index');
Route::get('/lineas', [LineaController::class, 'index'])->name('lineas.index');
Route::get('/puntos-venta', [PuntoVentaController::class, 'index'])->name('puntos-ventas');
Route::get('/horarios', [HorarioController::class, 'index'])->name('horarios.index');
Route::get('/paradas/filtro', [ParadaController::class, 'filtro'])->name('paradas.filtro');
Route::get('/paradas/filtro-linea', [ParadaController::class, 'filtroPorLinea'])->name('filtro-linea');
Route::get('/paradas/{parada}', [ParadaController::class, 'show'])->name('paradas.show');
Route::get('/tarifas', [TarifaInterurbanaController::class, 'index'])->name('tarifas.index');
Route::get('/tarifas/calculadora', [TarifaInterurbanaController::class, 'calculadora'])->name('tarifas.calculadora');


require __DIR__ . '/auth.php';
