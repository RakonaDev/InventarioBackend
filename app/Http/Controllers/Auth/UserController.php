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
    return response()->json([
      'token' => $token,
      'user' => $user
    ]);
  }

  public function register (Request $request) {
    $valitatedData = Validator::make($request->all(), [
      'name' => 'required|string|max:255',
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

    return response()->json([
      'message' => 'Usuario registrado correctamente',
      'token' => $token,
      'user' => $user->load('roles')
    ]);
  }
}
