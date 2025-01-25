<?php

use App\Http\Controllers\Auth\UserController;
use App\Http\Controllers\EstadoController;
use App\Http\Controllers\InsumoController;
use App\Http\Controllers\RolesController;
use App\Http\Middleware\CheckAdmin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/login', [UserController::class, 'login']);

Route::group(['middleware' => CheckAdmin::class], function () {
  Route::post('/register', [UserController::class, 'register']);

  Route::post('/roles', [RolesController::class, 'create']);

  // Ruta de Insumos
  Route::post('/insumos', action:[InsumoController::class, 'create']);
  Route::delete('/insumos/{id}', [InsumoController::class, 'destroy']);
  Route::get('/insumos/{id}', [InsumoController::class, 'showByID']);
  Route::patch('/insumos/{id}', [InsumoController::class, 'update']);
  Route::get('/insumos/{limit}/{page}', [InsumoController::class, 'show']);

  Route::post('/estado', [EstadoController::class, 'create']);
  Route::delete('/estado/{id}', [EstadoController::class, 'destroy']);
  Route::patch('/estado/{id}', [EstadoController::class, 'updateData']);
  Route::get('/estado/{limit}/{page}', [EstadoController::class, 'show']);
});
