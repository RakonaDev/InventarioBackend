<?php

use App\Http\Controllers\Auth\UserController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\ComprasController;
use App\Http\Controllers\EstadoController;
use App\Http\Controllers\InsumoController;
use App\Http\Controllers\PaginasController;
use App\Http\Controllers\ProveedorController;
use App\Http\Controllers\RolesController;
use App\Http\Controllers\SalidasController;
use App\Http\Controllers\TipoInsumoController;
use App\Http\Middleware\CheckAdmin;
use Illuminate\Support\Facades\Route;

Route::post('/login', [UserController::class, 'login']);

Route::group(['middleware' => CheckAdmin::class], function () {
  Route::get('/getUsers', [UserController::class, 'index']);
  Route::post('/register', [UserController::class, 'register']);
  Route::get('/me', [UserController::class, 'me']);
  // Route::patch('/user', [UserController::class, 'update']);
  Route::post('/user', [UserController::class, 'update']);
  // Route::delete('/user/{id}', [UserController::class,'delete']);
  Route::post('/deleteUser/{id}', [UserController::class, 'delete']);
  Route::post('/logout', [UserController::class,'logout']);
  // Ruta de Insumos
  Route::get('/getInsumos', [InsumoController::class, 'index']); 
  Route::post('/insumos', action:[InsumoController::class, 'store']);
  // Route::delete('/insumos/{id}', [InsumoController::class, 'destroy']);
  // Route::get('/insumos/{id}', [InsumoController::class, 'showByID']);
  // Route::patch('/insumos/{id}', [InsumoController::class, 'update']);
  Route::post('/editInsumos/{id}', [InsumoController::class, 'update']);
  Route::post('/deleteInsumos', [InsumoController::class, 'destroy']);
  Route::get('/insumos/{limit}/{page}', [InsumoController::class, 'show']);

  // Estado 
  Route::post('/estado', [EstadoController::class, 'create']);
  Route::delete('/estado/{id}', [EstadoController::class, 'destroy']);
  Route::patch('/estado/{id}', [EstadoController::class, 'updateData']);
  Route::get('/estado/{limit}/{page}', [EstadoController::class, 'show']);
  Route::get('/getEstados', [EstadoController::class,'index']);

  // Roles
  Route::get('/getRoles', [RolesController::class,'index']);
  Route::post('/roles', [RolesController::class, 'create']);
  //Route::delete('/roles/{id}', [RolesController::class, 'delete']);
  Route::post('/deleteRoles/{id}', [RolesController::class, 'delete']);
  // Route::patch('/roles', [RolesController::class,'updateData']);
  Route::post('/roles', [RolesController::class, 'updateData']);

  // Proveedores
  // Route::apiResource('proveedores', ProveedorController::class);
  Route::get('/proveedores', [ProveedorController::class, 'index']);
  Route::post('/proveedores', [ProveedorController::class, 'store']);
  Route::post('/proveedores/{id}', [ProveedorController::class, 'update']);
  Route::post('/deleteProveedores/{id}', [ProveedorController::class, 'destroy']);

  // Tipo Insumo
  Route::apiResource('tipo-insumo', TipoInsumoController::class);

  //Route::apiResource('categorias', CategoriaController::class);
  Route::get('/categorias', [CategoriaController::class, 'index']);
  Route::post('/categorias', [CategoriaController::class, 'store']);
  Route::post('/categorias/{id}', [CategoriaController::class, 'update']);
  Route::post('/deleteCategorias/{id}', [CategoriaController::class, 'destroy']);
  /*
  Route::get('/categorias', [CategoriaController::class, 'index']);
  Route::post('/categorias', [CategoriaController::class, 'store']);
  */
  
  Route::get('/paginas', [PaginasController::class, 'index']);
  
  Route::apiResource('compras', ComprasController::class);
  // Route::apiResource('insumos', InsumoController::class);
  Route::get('/insumos', [InsumoController::class, 'index']);
  Route::post('/insumos', [InsumoController::class, 'store']);
  Route::post('/insumos/{id}', [InsumoController::class, 'update']);
  Route::post('/deleteInsumos/{id}', [InsumoController::class, 'destroy']);
  
  Route::apiResource('salidas', SalidasController::class);
});
