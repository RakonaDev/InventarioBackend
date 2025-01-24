<?php

use App\Http\Controllers\Auth\UserController;
use App\Http\Controllers\RolesController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/login', [UserController::class, 'login']);

Route::post('/register', [UserController::class, 'register']);

Route::post('/roles', [RolesController::class, 'create']);