<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\LogoutController;
use App\Http\Controllers\ReunionController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\VideoCallController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('register', [RegisterController::class, 'index'])-> name('register');
Route::post('register', [RegisterController::class, 'store']);

Route::get('/muro', [PostController::class, 'index'])->middleware('auth')->name('post.index');


Route::get('/login', [LoginController::class, 'index'])->name('login');
Route::post('/login', [LoginController::class, 'store']);
Route::post('/logout', [LogoutController::class, 'store'])->name('logout');

Route::middleware(['auth'])->group(function () {
    Route::get('/reuniones', [ReunionController::class, 'index'])->name('reuniones.index');
    Route::get('/reuniones/create', [ReunionController::class, 'create'])->name('reuniones.create');
    Route::post('/reuniones', [ReunionController::class, 'store'])->name('reuniones.store');

    Route::get('/reuniones/historial', [ReunionController::class, 'historial'])->name('reuniones.historial');
    Route::get('/reuniones/historial/{reunion}', [ReunionController::class, 'detalleHistorial'])->name('reuniones.detalle_historial');
    Route::post('/reuniones/{reunion}/actividades', [ReunionController::class, 'storeActividad'])->name('actividades.store');


    Route::get('/reuniones/{id}', [ReunionController::class, 'show'])->name('reuniones.show');
    Route::get('/reuniones/{reunion}/invitados', [ReunionController::class, 'invitados'])->name('reuniones.invitados');
    Route::post('/reuniones/{reunion}/invitados', [ReunionController::class, 'agregarInvitado'])->name('reuniones.agregarInvitado');
    Route::get('/reuniones/invitaciones', [ReunionController::class, 'invitaciones'])->name('reuniones.invitaciones');
    Route::get('reuniones/{reunion}/edit', [ReunionController::class, 'edit'])->name('reuniones.edit');
    Route::put('reuniones/{reunion}', [ReunionController::class, 'update'])->name('reuniones.update');
    Route::delete('reuniones/{reunion}', [ReunionController::class, 'destroy'])->name('reuniones.destroy');

    Route::get('/reuniones/{reunion}/videollamada', [VideoCallController::class, 'join'])->name('reuniones.videollamada');

});
