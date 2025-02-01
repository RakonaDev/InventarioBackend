<?php

use App\Http\Controllers\ComprasController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
  return view('welcome');
});

Route::get('/ver-comprobante', [ComprasController::class, 'verComprobante'])
  ->middleware('auth')
  ->name('ver.comprobante');
