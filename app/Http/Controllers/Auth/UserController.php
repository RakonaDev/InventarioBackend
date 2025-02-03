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
      false,
      true,
      false,
      null
    );
    return response()->json([
      'user' => $user,
      'token' => $token
    ])->cookie($cookie);
  }

  public function register (Request $request) {
    $valitatedData = Validator::make($request->all(), [
      'names' => 'required|string|max:255',
      'last_names' => 'required|string|max:255',
      'age' => 'required|integer',
      'dni' => 'required|string|max:255|unique:users',
      'email' => 'required|string|email|max:255|unique:users',
      'password' => 'required|string|min:8|max:15|confirmed',
      'id_estado' => 'required|exists:estado,id',
      'id_roles' => 'required|exists:roles,id'
    ]);

    if ($valitatedData->fails()) {
      return $valitatedData->errors();
    }

    $user = User::create([
      'names' => $request->input('names'),
      'email' => $request->input('email'),
      'age' => $request->input('age'),
      'dni' => $request->input('dni'),
      'last_names' => $request->input('last_names'),
      'id_estado' => $request->input('id_estado'),
      'id_roles' => $request->input('id_roles'),
      'password' => Hash::make($request->input('password'))
    ]);

    $token = JWTAuth::fromUser($user);

    $cookie = cookie(
      'jwt_token',
      $token,
      60 * 24,
      '/',
      'http://localhost:3000',
      false,
      true,
      false,
      null
    );

    return response()->json([
      'message' => 'Usuario registrado correctamente',
      'user' => $user->load('roles')
    ])->withCookie($cookie);
  }
}
