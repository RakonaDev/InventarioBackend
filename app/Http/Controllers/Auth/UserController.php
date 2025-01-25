<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserController extends Controller
{
  //

  public function login (Request $request) {
    $valitatedData = Validator::make($request->all(), [
      'email' => 'required|string|email',
      'password' => 'required|string',
    ]);

    if ($valitatedData->fails()) {
      return $valitatedData->errors();
    }

    if (!$token = JWTAuth::attempt($valitatedData->getData())) {
      return response()->json(['error' => 'Credenciales inválidas'], 401);
    }
    
    $user = Auth::user();
    $cookie = cookie(
      'jwt_token',
      $token,
      60 * 24,
      '/',
      null,
      true,
      true,
      false,
      'Strict'
    );
    return response()->json([
      'user' => $user
    ])->withCookie($cookie);
  }

  public function register (Request $request) {
    $valitatedData = Validator::make($request->all(), [
      'names' => 'required|string|max:255',
      'last_names' => 'required|string|max:255',
      'email' => 'required|string|email|max:255|unique:users',
      'password' => 'required|string|min:8|max:15|confirmed',
      'id_roles' => 'required|exists:roles,id'
    ]);

    if ($valitatedData->fails()) {
      return $valitatedData->errors();
    }

    $user = User::create([
      'name' => $request->name,
      'email' => $request->email,
      'id_roles' => $request->id_roles,
      'password' => Hash::make($request->password)
    ]);

    $token = JWTAuth::fromUser($user);

    $cookie = cookie(
      'jwt_token',
      $token,
      60 * 24,
      '/',
      null,
      true,
      true,
      false,
      'Strict'
    );

    return response()->json([
      'message' => 'Usuario registrado correctamente',
      'user' => $user->load('roles')
    ])->withCookie($cookie);
  }
}
