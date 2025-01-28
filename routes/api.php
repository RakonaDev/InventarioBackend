<?php

use App\Http\Controllers\Auth\UserController;
use App\Http\Controllers\EstadoController;
use App\Http\Controllers\InsumoController;
use App\Http\Controllers\ProveedorController;
use App\Http\Controllers\RolesController;
use App\Http\Middleware\CheckAdmin;
use App\Models\Roles;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/login', [UserController::class, 'login']);
Route::post('/register', [UserController::class, 'register']);
Route::post('/estado', [EstadoController::class, 'create']);
Route::post('/roles', [RolesController::class, 'create']);

Route::group(['middleware' => CheckAdmin::class], function () {

  // Usuarios
  Route::get('/getUsers', [UserController::class,'index']);
  Route::patch('/user', [UserController::class,'update']);
  Route::delete('/user', [UserController::class,'delete']);

  // Ruta de Insumos
  Route::post('/insumos', action:[InsumoController::class, 'create']);
  Route::delete('/insumos/{id}', [InsumoController::class, 'destroy']);
  Route::get('/insumos/{id}', [InsumoController::class, 'showByID']);
  Route::patch('/insumos/{id}', [InsumoController::class, 'update']);
  Route::get('/insumos/{limit}/{page}', [InsumoController::class, 'show']);

  // Estado 
  // Route::post('/estado', [EstadoController::class, 'create']);
  Route::delete('/estado/{id}', [EstadoController::class, 'destroy']);
  Route::patch('/estado/{id}', [EstadoController::class, 'updateData']);
  Route::get('/estado/{limit}/{page}', [EstadoController::class, 'show']);
  Route::get('/getEstados', [EstadoController::class,'index']);

  // Roles
  Route::get('/getRoles', [RolesController::class,'index']);
  // Route::post('/roles', [RolesController::class, 'create']);
  Route::delete('/roles', [RolesController::class, 'delete']);
  Route::patch('/roles', [RolesController::class,'updateData']);

  // Proveedores
  Route::apiResource('proveedores', ProveedorController::class);
});
