<?php

use App\Http\Controllers\ActaController;
use App\Http\Controllers\AsistenciaController;
use App\Http\Controllers\CompromisoController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\LogoutController;
use App\Http\Controllers\ReunionController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\TranscripcionController;
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

    // Transcripciones
    Route::post('reuniones/{reunion}/transcripciones', [TranscripcionController::class,'store'])->name('reuniones.transcripciones.store');
    Route::get('reuniones/{reunion}/transcripciones/last', [TranscripcionController::class,'showLast'])->name('reuniones.transcripciones.last');

    // Actas
    Route::get('reuniones/{reunion}/acta/create', [ActaController::class,'create'])->name('actas.create');
    Route::post('reuniones/{reunion}/acta', [ActaController::class,'store'])->name('actas.store');
    Route::post('actas/{acta}/finalizar', [ActaController::class,'finalizar'])->name('actas.finalizar');
    Route::get('actas/{acta}/pdf', [ActaController::class,'exportPdf'])->name('actas.exportPdf');
    Route::get('actas/{acta}', [ActaController::class,'show'])->name('actas.show');
    Route::get('actas/{acta}/pdf', [ActaController::class, 'descargarPdf'])
    ->name('actas.pdf');


    // Asistencia
    Route::post('reuniones/{reunion}/asistencia/entrada', [AsistenciaController::class,'entrada'])->name('reuniones.asistencia.entrada');
    Route::post('reuniones/{reunion}/asistencia/salida', [AsistenciaController::class,'salida'])->name('reuniones.asistencia.salida');


    // Guardar un compromiso en una reunión
    Route::post('/reuniones/{reunion}/compromisos', [CompromisoController::class, 'store'])
        ->name('compromisos.store');

    // Eliminar un compromiso
    Route::delete('/compromisos/{compromiso}', [CompromisoController::class, 'destroy'])
        ->name('compromisos.destroy');

    // Editar un compromiso
    Route::put('/compromisos/{compromiso}', [CompromisoController::class, 'update'])
        ->name('compromisos.update');


    // Actividades desde el Acta
    Route::post('/actas/{acta}/actividades', [ActaController::class, 'storeActividad'])->name('actas.actividades.store');
    Route::delete('/actas/actividades/{actividad}', [ActaController::class, 'destroyActividad'])->name('actas.actividades.destroy');
    Route::put('/actas/actividades/{actividad}', [ActaController::class, 'updateActividad'])->name('actas.actividades.update');
});
