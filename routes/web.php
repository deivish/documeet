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
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AnalyticsController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('register', [RegisterController::class, 'index'])->name('register');
Route::post('register', [RegisterController::class, 'store']);

Route::get('/muro', [PostController::class, 'index'])->middleware('auth')->name('post.index');

Route::get('/login', [LoginController::class, 'index'])->name('login');
Route::post('/login', [LoginController::class, 'store']);
Route::post('/logout', [LogoutController::class, 'store'])->name('logout');

// Recuperar contraseña (fuera del grupo auth)
Route::get('/forgot-password', [PasswordResetController::class, 'showForm'])->name('password.request');
Route::post('/forgot-password', [PasswordResetController::class, 'sendLink'])->name('password.email');
Route::get('/reset-password', [PasswordResetController::class, 'showReset'])->name('password.reset');
Route::post('/reset-password', [PasswordResetController::class, 'reset'])->name('password.update');

Route::middleware(['auth'])->group(function () {

    // ── Dashboard / Muro ──
    Route::get('/reuniones', [ReunionController::class, 'index'])->name('reuniones.index');
    Route::get('/reuniones/create', [ReunionController::class, 'create'])->name('reuniones.create');
    Route::post('/reuniones', [ReunionController::class, 'store'])->name('reuniones.store');
    Route::get('/reuniones/{reunion}/dashboard', [DashboardController::class, 'reunion'])->name('reuniones.dashboard');
    Route::get('/reuniones/{reunion}/auditoria', [DashboardController::class, 'auditoria'])->name('reuniones.auditoria');
    Route::get('/reuniones/{reunion}/auditoria/exportar', [DashboardController::class, 'exportarAuditoria'])->name('reuniones.auditoria.exportar');
    Route::get('/analytics', [AnalyticsController::class, 'index'])->name('analytics.index');
    
    // ── Historial ──
    Route::get('/reuniones/historial', [ReunionController::class, 'historial'])->name('reuniones.historial');
    Route::get('/reuniones/historial/{reunion}', [ReunionController::class, 'detalleHistorial'])->name('reuniones.detalle_historial');

    // ── Reuniones CRUD ──
    Route::get('/reuniones/{id}', [ReunionController::class, 'show'])->name('reuniones.show');
    Route::get('reuniones/{reunion}/edit', [ReunionController::class, 'edit'])->name('reuniones.edit');
    Route::put('reuniones/{reunion}', [ReunionController::class, 'update'])->name('reuniones.update');
    Route::delete('reuniones/{reunion}', [ReunionController::class, 'destroy'])->name('reuniones.destroy');

    // ── Invitados ──
    Route::get('/reuniones/{reunion}/invitados', [ReunionController::class, 'invitados'])->name('reuniones.invitados');
    Route::post('/reuniones/{reunion}/invitados', [ReunionController::class, 'agregarInvitado'])->name('reuniones.agregarInvitado');
    Route::get('/reuniones/invitaciones', [ReunionController::class, 'invitaciones'])->name('reuniones.invitaciones');

    // ── Videollamada ──
    Route::get('/reuniones/{reunion}/videollamada', [VideoCallController::class, 'join'])->name('reuniones.videollamada');

    // ── Asistencia ──
    Route::post('reuniones/{reunion}/asistencia/entrada', [AsistenciaController::class, 'entrada'])->name('reuniones.asistencia.entrada');
    Route::post('reuniones/{reunion}/asistencia/salida', [AsistenciaController::class, 'salida'])->name('reuniones.asistencia.salida');

    // ── Actividades desde Reunión ──
    Route::post('/reuniones/{reunion}/actividades', [ReunionController::class, 'storeActividad'])->name('actividades.store');

    // ── Transcripciones ──
    Route::post('reuniones/{reunion}/transcripciones', [TranscripcionController::class, 'store'])->name('reuniones.transcripciones.store');
    Route::get('reuniones/{reunion}/transcripciones/last', [TranscripcionController::class, 'showLast'])->name('reuniones.transcripciones.last');
    Route::get('reuniones/{reunion}/transcripciones/all', [TranscripcionController::class, 'getAll'])->name('reuniones.transcripciones.all');
    Route::get('reuniones/{reunion}/transcripciones', [TranscripcionController::class, 'index'])->name('reuniones.transcripciones.index');
    Route::post('reuniones/{reunion}/transcripciones/procesar-audio', [TranscripcionController::class, 'procesarAudio'])->name('reuniones.transcripciones.procesar-audio');

    // ── Compromisos ──
    Route::post('/reuniones/{reunion}/compromisos', [CompromisoController::class, 'store'])->name('compromisos.store');
    Route::put('/compromisos/{compromiso}', [CompromisoController::class, 'update'])->name('compromisos.update');
    Route::delete('/compromisos/{compromiso}', [CompromisoController::class, 'destroy'])->name('compromisos.destroy');
    Route::put('/compromisos/{compromiso}/estado', [CompromisoController::class, 'updateEstado'])->name('compromisos.estado');

    // ── Actas ──
    Route::post('reuniones/{reunion}/acta', [ActaController::class, 'store'])->name('actas.store');
    Route::get('actas/{acta}', [ActaController::class, 'show'])->name('actas.show');
    Route::post('actas/{acta}/finalizar', [ActaController::class, 'finalizar'])->name('actas.finalizar');
    Route::get('actas/{acta}/pdf', [ActaController::class, 'descargarPdf'])->name('actas.pdf');
    Route::get('actas/{acta}/docx', [ActaController::class, 'descargarDocx'])->name('actas.docx');

    // ── IA para Actas ──
    Route::post('/actas/{acta}/extraer-compromisos', [ActaController::class, 'extraerCompromisos'])->name('actas.extraer-compromisos');
    Route::post('/actas/{acta}/generar-resumen', [ActaController::class, 'generarResumen'])->name('actas.generar-resumen');

    // ── Actividades desde el Acta ──
    Route::post('/actas/{acta}/actividades', [ActaController::class, 'storeActividad'])->name('actas.actividades.store');
    Route::put('/actas/actividades/{actividad}', [ActaController::class, 'updateActividad'])->name('actas.actividades.update');
    Route::put('/actas/actividades/{actividad}/estado', [ActaController::class, 'updateEstadoActividad'])->name('actas.actividades.estado');
    Route::delete('/actas/actividades/{actividad}', [ActaController::class, 'destroyActividad'])->name('actas.actividades.destroy');

});