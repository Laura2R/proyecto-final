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
use App\Http\Controllers\Admin\AdminController;

// Página de inicio
Route::get('/', [HomeController::class, 'index'])->name('home');

// Contacto
Route::get('/contacto', [HomeController::class, 'contacto'])->name('contact');
Route::post('/contacto', [HomeController::class, 'store'])->name('contact.store');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'prevent-back-history'])->group(function () {

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

// Rutas de administración
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('dashboard');
    Route::get('/users', [AdminController::class, 'users'])->name('users');
    Route::get('/users/create', [AdminController::class, 'createUser'])->name('users.create');
    Route::post('/users', [AdminController::class, 'storeUser'])->name('users.store');
    Route::get('/users/{user}/edit', [AdminController::class, 'editUser'])->name('users.edit');
    Route::put('/users/{user}', [AdminController::class, 'updateUser'])->name('users.update');
    Route::delete('/users/{user}', [AdminController::class, 'destroyUser'])->name('users.destroy');
    Route::get('/users/{user}/cards', [AdminController::class, 'userCards'])->name('users.cards');
    Route::get('/users/{user}/cards/{card}/edit', [AdminController::class, 'editUserCard'])->name('users.cards.edit');
    Route::put('/users/{user}/cards/{card}', [AdminController::class, 'updateUserCard'])->name('users.cards.update');
    Route::delete('/users/{user}/cards/{card}', [AdminController::class, 'destroyUserCard'])->name('users.cards.destroy');
});

//COMPROBACION DE ERRORES
/*

// Error 401 - No Autorizado
Route::get('/401', function () {
    abort(401, 'No autorizado - Debes iniciar sesión');
})->name('test.401');

// Error 402 - Pago Requerido
Route::get('/402', function () {
    abort(402, 'Pago requerido para acceder a este contenido');
})->name('test.402');

// Error 403 - Acceso Prohibido
Route::get('/403', function () {
    abort(403, 'Acceso prohibido - No tienes permisos');
})->name('test.403');

// Error 404 - No Encontrado
Route::get('/404', function () {
    abort(404, 'Página no encontrada');
})->name('test.404');

// Error 419 - Token CSRF Expirado
Route::get('/419', function () {
    abort(419, 'Token CSRF expirado');
})->name('test.419');

// Error 429 - Demasiadas Solicitudes
Route::get('/429', function () {
    abort(429, 'Demasiadas solicitudes - Límite excedido');
})->name('test.429');

// Error 500 - Error Interno del Servidor
Route::get('/500', function () {
    abort(500, 'Error interno del servidor');
})->name('test.500');

// Error 503 - Servicio No Disponible
Route::get('/503', function () {
    abort(503, 'Servicio temporalmente no disponible');
})->name('test.503');

*/
require __DIR__ . '/auth.php';
