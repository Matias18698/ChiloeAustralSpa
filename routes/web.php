<?php

use App\Http\Controllers\EmbarcacionController; // Importar el controlador
use App\Http\Controllers\AsistenciaController;
use App\Http\Controllers\TrabajadorController;
use App\Http\Controllers\BitacoraController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\GastoController;
use App\Http\Controllers\UserController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    /*
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);*/
    return redirect('/Inicio');
});
//ruta para el cron de asistencias
Route::get('/Asistencias/cron', [AsistenciaController::class, 'AsistenciaCron'])->name('asistencia.cron'); 

Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard
    Route::get('/Inicio', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');
    
    // Perfil de usuario
    Route::prefix('profile')->group(function () {
        Route::get('/', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/', [ProfileController::class, 'destroy'])->name('profile.destroy');
    });
        
    // Gestión de usuarios (admin)
    Route::prefix('/Usuarios')->name('usuarios.')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::get('create', [UserController::class, 'create'])->name('create');
        Route::post('/', [UserController::class, 'store'])->name('store');
        Route::get('{user}/edit', [UserController::class, 'edit'])->name('edit');
        Route::post('{user}', [UserController::class, 'update'])->name('update');
        Route::delete('{user}', [UserController::class, 'destroy'])
            ->whereNumber('user')
            ->name('destroy');

        Route::post('{user}/role', [UserController::class, 'updateRole'])->name('updateRole');
    });


    // Trabajadores
    Route::prefix('/Trabajadores')->name('trabajador.')->group(function () {
        Route::get('/', [TrabajadorController::class, 'index'])->name('index');
        Route::get('create', [TrabajadorController::class, 'create'])->name('create');
        Route::post('/', [TrabajadorController::class, 'store'])->name('store');
        Route::get('{trabajador}', [TrabajadorController::class, 'show'])->name('show');
        Route::get('{trabajador}/edit', [TrabajadorController::class, 'edit'])->name('edit');
        Route::post('{trabajador}', [TrabajadorController::class, 'update'])->name('update');
        Route::delete('{trabajador}', [TrabajadorController::class, 'destroy'])->name('destroy');
        Route::get('{trabajador}/avatar', [TrabajadorController::class, 'avatar'])->name('avatar');
        Route::post('check-email', [TrabajadorController::class, 'checkEmail'])->name('checkEmail');
        Route::post('check-rut', [TrabajadorController::class, 'checkRut'])->name('checkRut');
    });
        //Asistencias
    Route::prefix('/Asistencias')->name('asistencia.')->group(function () {
        Route::get('/', [AsistenciaController::class, 'index'])->name('index');
        Route::get('create', [AsistenciaController::class, 'create'])->name('create');
        Route::get('manual', [AsistenciaController::class, 'manual'])->name('manual'); // NUEVA
        Route::get('bajada', [AsistenciaController::class, 'bajada'])->name('bajada'); // NUEVA

        Route::post('/', [AsistenciaController::class, 'store'])->name('store');
        Route::get('{asistencia}', [AsistenciaController::class, 'show'])->name('show');
        Route::get('{asistencia}/edit', [AsistenciaController::class, 'edit'])->name('edit');
        Route::post('{asistencia}', [AsistenciaController::class, 'update'])->name('update');
        Route::delete('{asistencia}', [AsistenciaController::class, 'destroy'])->name('destroy');
        Route::post('registrar-rut', [AsistenciaController::class, 'registrarPorRut'])->name('registrar.rut');
        Route::post('verificar-rut', [AsistenciaController::class, 'verificarRut'])->name('verificar.rut');

        

    });
    
    // Embarcaciones
    Route::prefix('/Embarcaciones')->name('embarcacion.')->group(function () {
        Route::get('/', [EmbarcacionController::class, 'index'])->name('index');  // Ver lista de embarcaciones
        Route::get('create', [EmbarcacionController::class, 'create'])->name('create');  // Crear nueva embarcación
        
        Route::get('manual', [EmbarcacionController::class, 'manual'])->name('manual');  // Crear nueva embarcación manual
        Route::post('/', [EmbarcacionController::class, 'store'])->name('store');  // Guardar embarcación
        Route::get('{embarcacion}', [EmbarcacionController::class, 'show'])->name('show');  // Ver embarcación específica
        Route::get('{embarcacion}/edit', [EmbarcacionController::class, 'edit'])->name('edit');  // Editar embarcación
        Route::post('{embarcacion}', [EmbarcacionController::class, 'update'])->name('update');  // Actualizar embarcación
        Route::delete('{embarcacion}', [EmbarcacionController::class, 'destroy'])->name('destroy');  // Eliminar embarcación
        Route::post('{embarcacion}/ubicacion', [EmbarcacionController::class, 'updateLocation'])->name('updateLocation');

    });
    // Bitácoras
    Route::prefix('/Bitacoras')->name('bitacora.')->group(function () {
        Route::get('/', [BitacoraController::class, 'index'])->name('index');  // Ver lista de bitácoras
        Route::get('create', [BitacoraController::class, 'create'])->name('create');  // Crear nueva bitácora
        Route::post('/', [BitacoraController::class, 'store'])->name('store');  // Guardar bitácora
        Route::get('{bitacora}', [BitacoraController::class, 'show'])->name('show');  // Ver bitácora específica
        Route::get('{bitacora}/edit', [BitacoraController::class, 'edit'])->name('edit');  // Editar bitácora
        Route::post('{bitacora}', [BitacoraController::class, 'update'])->name('update');  // Actualizar bitácora
        Route::delete('{bitacora}', [BitacoraController::class, 'destroy'])->name('destroy');  // Eliminar bitácora
        Route::post('crear-diaria/{embarcacionId}', [BitacoraController::class, 'crearBitacoraDiaria'])->name('crear-diaria');  // Crear bitácora diaria
        Route::get('embarcacion/{embarcacionId}', [BitacoraController::class, 'bitacorasPorEmbarcacion'])->name('embarcacion');  // Ver bitácoras por embarcación
        Route::get('documento/{id}', [BitacoraController::class, 'documento'])->name('documento'); // Ver documento de bitácora
        
    });
    // Rutas de gastos
Route::prefix('/Gastos')->name('gastos.')->group(function () {
    Route::get('/', [GastoController::class, 'index'])->name('index');             // Lista
    Route::get('create', [GastoController::class, 'create'])->name('create');      // Crear
    Route::post('/', [GastoController::class, 'store'])->name('store');            // Guardar
    Route::get('{gasto}', [GastoController::class, 'show'])->name('show');         // Ver uno
    Route::get('{gasto}/edit', [GastoController::class, 'edit'])->name('edit');    // Editar
    Route::post('{gasto}', [GastoController::class, 'update'])->name('update');    // Actualizar
    Route::delete('{gasto}', [GastoController::class, 'destroy'])->name('destroy'); // Eliminar
    route::get('obtener-gastos', [GastoController::class, 'obtenerGastos'])->name('obtener'); // Obtener gastos por rango de fechas
    // Ruta API para obtener gastos filtrados en JSON (para consumo frontend)
    Route::get('api', [GastoController::class, 'apiIndex'])->name('api.index');
});

});

require __DIR__.'/auth.php';
