<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');



// Referencia de rutas para routes/api.php
// Copia y pega esto (ajustando el prefijo/middleware que ya uses) en tu routes/api.php

use App\Http\Controllers\ActividadController;
use App\Http\Controllers\AsistenciaController;
use App\Http\Controllers\CampanaController;
use App\Http\Controllers\CategoriaCampanaController;
use App\Http\Controllers\CertificadoController;
use App\Http\Controllers\ComunicadoController;
use App\Http\Controllers\ConfiguracionController;
use App\Http\Controllers\EvaluacionCampanaController;
use App\Http\Controllers\HoraVoluntariadoController;
use App\Http\Controllers\InscripcionController;
use App\Http\Controllers\LogAuditoriaController;
use App\Http\Controllers\MaterialCampanaController;
use App\Http\Controllers\NotificacionController;
use App\Http\Controllers\OrganizadorController;
use App\Http\Controllers\PuntoEcologicoController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\VoluntarioController;


Route::apiResource('usuarios', UsuarioController::class);
Route::apiResource('voluntarios', VoluntarioController::class);
Route::apiResource('organizadores', OrganizadorController::class);
Route::apiResource('categorias-campanas', CategoriaCampanaController::class);
Route::apiResource('campanas', CampanaController::class);
Route::apiResource('actividades', ActividadController::class);
Route::apiResource('inscripciones', InscripcionController::class);
Route::apiResource('asistencias', AsistenciaController::class);
Route::apiResource('horas-voluntariado', HoraVoluntariadoController::class);
Route::apiResource('notificaciones', NotificacionController::class);
Route::apiResource('certificados', CertificadoController::class);
Route::get('certificados/verificar/{codigo}', [CertificadoController::class, 'verificar']);
Route::apiResource('puntos-ecologicos', PuntoEcologicoController::class);
Route::apiResource('materiales-campana', MaterialCampanaController::class);
Route::apiResource('evaluaciones-campana', EvaluacionCampanaController::class);
Route::apiResource('logs-auditoria', LogAuditoriaController::class)->except(['update']);
Route::apiResource('comunicados', ComunicadoController::class);
Route::apiResource('configuracion', ConfiguracionController::class);
Route::get('configuracion/clave/{clave}', [ConfiguracionController::class, 'porClave']);
