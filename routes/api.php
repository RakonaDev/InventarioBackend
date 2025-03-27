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
use App\Http\Middleware\CategoriaPermisoMiddleware;
use App\Http\Middleware\CheckAdmin;
use App\Http\Middleware\ComprasPermisoMiddleware;
use App\Http\Middleware\MovimientosPermisoMiddleware;
use App\Http\Middleware\ProductosPermisoMiddleware;
use App\Http\Middleware\ProveedoresPermisoMiddleware;
use App\Http\Middleware\RolesPermisoMiddleware;
use App\Http\Middleware\SalidasPermisoMiddleware;
use App\Http\Middleware\UsuariosPermisoMiddleware;
use Illuminate\Support\Facades\Route;

Route::post('/login', [UserController::class, 'login']);
Route::post('/register', [UserController::class, 'register']);

Route::group(['middleware' => CheckAdmin::class], function () {

  Route::group(['middleware' => UsuariosPermisoMiddleware::class], function () {
    Route::get('/user/{limit}/{page}', [UserController::class, 'paginateUsers']);
    Route::post('/register', [UserController::class, 'register']);
    Route::post('/user', [UserController::class, 'update']);
    Route::post('/deleteUser/{id}', [UserController::class, 'delete']);
    Route::get('/getUsers', [UserController::class, 'index']);
  });

  Route::group(['middleware' => CategoriaPermisoMiddleware::class], function () {
    Route::get('/categorias/{limit}/{pages}', [CategoriaController::class, 'paginateCategorias']);
    Route::post('/categorias', [CategoriaController::class, 'store']);
    Route::post('/categorias/{id}', [CategoriaController::class, 'update']);
    Route::post('/deleteCategorias/{id}', [CategoriaController::class, 'destroy']);
  });

  Route::group(['middleware' => ProductosPermisoMiddleware::class], function () {
    Route::post('/insumos', [InsumoController::class, 'store']);
    Route::post('/insumos/{id}', [InsumoController::class, 'update']);
    Route::get('/insumos/{limit}/{page}', [InsumoController::class, 'paginateInsumos']);
    Route::post('/deleteInsumos/{id}', [InsumoController::class, 'destroy']);
    Route::get('/insumos/{limit}/{page}/{nombre}', [InsumoController::class, 'buscarInsumosPorNombrePaginado']);
  });

  Route::group(['middleware' => ComprasPermisoMiddleware::class], function () {
    Route::get('/compras/{limit}/{page}', [ComprasController::class, 'paginateCompras']);
    Route::post('/compras', [ComprasController::class, 'store']);
  });

  Route::group(['middleware' => RolesPermisoMiddleware::class], function () {
    Route::post('/roles', [RolesController::class, 'create']);
    //Route::delete('/roles/{id}', [RolesController::class, 'delete']);
    Route::post('/deleteRoles/{id}', [RolesController::class, 'delete']);
    // Route::patch('/roles', [RolesController::class,'updateData']);
    Route::post('/roles/{id}', [RolesController::class, 'updateData']);
    Route::get('/roles/{limit}/{page}', [RolesController::class, 'paginateRoles']);
  });

  Route::group(['middleware' => ProveedoresPermisoMiddleware::class], function () {
    Route::post('/proveedores', [ProveedorController::class, 'store']);
    Route::post('/proveedores/{id}', [ProveedorController::class, 'update']);
    Route::post('/deleteProveedores/{id}', [ProveedorController::class, 'destroy']);
    Route::get('/proveedores/{limit}/{page}', [ProveedorController::class, 'paginateProveedores']);
  });

  Route::group(['middleware' => SalidasPermisoMiddleware::class], function () {
    Route::get('/salidas/{limit}/{page}', [SalidasController::class, 'paginateSalidas']);
    Route::post('/salidas', [SalidasController::class, 'store']);
  });

  Route::group(['middleware' => MovimientosPermisoMiddleware::class], function () {
    Route::get('/salidasMov/{limit}/{page}', [SalidasController::class, 'paginateSalidas']);
    Route::post('/salidasMov', [SalidasController::class, 'store']);

    Route::get('/comprasMov/{limit}/{page}', [ComprasController::class, 'paginateCompras']);
    Route::post('/comprasMov', [ComprasController::class, 'store']);
  });

  Route::get('/me', [UserController::class, 'me']);
  // Route::patch('/user', [UserController::class, 'update']);
  // Route::delete('/user/{id}', [UserController::class,'delete']);
  Route::post('/logout', [UserController::class, 'logout']);
  // Ruta de Insumos
  Route::get('/getInsumos', [InsumoController::class, 'index']);
  Route::post('/insumos', action: [InsumoController::class, 'store']);
  // Route::delete('/insumos/{id}', [InsumoController::class, 'destroy']);
  // Route::get('/insumos/{id}', [InsumoController::class, 'showByID']);
  // Route::patch('/insumos/{id}', [InsumoController::class, 'update']);
  // Route::get('/insumos/{limit}/{page}', [InsumoController::class, 'paginateInsumos']);

  // Estado 
  Route::post('/estado', [EstadoController::class, 'create']);
  Route::delete('/estado/{id}', [EstadoController::class, 'destroy']);
  Route::patch('/estado/{id}', [EstadoController::class, 'updateData']);
  Route::get('/estado/{limit}/{page}', [EstadoController::class, 'show']);
  Route::get('/getEstados', [EstadoController::class, 'index']);

  // Roles
  Route::get('/getRoles', [RolesController::class, 'index']);

  // Proveedores
  // Route::apiResource('proveedores', ProveedorController::class);
  Route::get('/proveedores', [ProveedorController::class, 'index']);
  // Tipo Insumo
  // Route::apiResource('tipo-insumo', TipoInsumoController::class);

  //Route::apiResource('categorias', CategoriaController::class);
  Route::get('/categorias', [CategoriaController::class, 'index']);
  /*
  Route::get('/categorias', [CategoriaController::class, 'index']);
  Route::post('/categorias', [CategoriaController::class, 'store']);
  */

  Route::get('/paginas', [PaginasController::class, 'index']);

  // Route::apiResource('compras', ComprasController::class);

  // Route::apiResource('insumos', InsumoController::class);
  Route::get('/insumos', [InsumoController::class, 'index']);
  Route::get('/insumos/buscar/{nombre}/{limit}/{page}', [InsumoController::class, 'buscarInsumosPorNombrePaginado']);

  // Route::apiResource('salidas', SalidasController::class);
  Route::get('/salidas/{limit}/{page}', [SalidasController::class, 'paginateSalidas']);
  Route::post('/salidas', [SalidasController::class, 'store']);
});
