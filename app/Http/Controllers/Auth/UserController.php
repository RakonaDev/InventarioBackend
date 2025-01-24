<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
}
