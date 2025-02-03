<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserController extends Controller
{
  //

  public function login(Request $request)
  {
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
    // prueba@administrador.com
    $user = Auth::user();
    $cookie = cookie(
      'jwt_token',
      $token,
      60 * 24,
      '/',
      'http://localhost:3000',
      false,
      true,
      'None'
    );
    /*
    '/',
      "localhost",
      true,
      true,
      false,
      'None'
    
    */
    return response()->json([
      'user' => $user,
      'token' => $token
    ])->withCookie($cookie);
  }

  public function register(Request $request)
  {
    $valitatedData = Validator::make($request->all(), [
      'names' => 'required|string|max:255',
      'last_names' => 'required|string|max:255',
      'age' => 'required|integer',
      'tel' => 'required|max:10|string|unique:users',
      'dni' => 'required|string|max:255|unique:users',
      'email' => 'required|string|email|max:255|unique:users',
      'password' => 'required|string|min:8|max:15',
      'id_estado' => 'required|exists:estado,id',
      'id_roles' => 'required|exists:roles,id'
    ]);

    if ($valitatedData->fails()) {
      return response()->json([
        'errores' => $valitatedData->errors(),
      ], 404);
    }

    $user = User::create([
      'names' => $request->input('names'),
      'email' => $request->input('email'),
      'age' => $request->input('age'),
      'dni' => $request->input('dni'),
      'tel' => $request->input('tel'),
      'last_names' => $request->input('last_names'),
      'id_estado' => $request->input('id_estado'),
      'id_roles' => $request->input('id_roles'),
      'password' => Hash::make($request->input('password'))
    ]);
    $user->password = $request->input('password');

    $token = JWTAuth::fromUser($user);

    $cookie = cookie(
      'jwt_token',
      $token,
      60 * 24,
      '/',
      'http://localhost:3000',
      true,
      true,
      false,
      'None'
    );

    return response()->json([
      'message' => 'Usuario registrado correctamente',
      'user' => $user->load('roles')
    ])->cookie($cookie);
  }

  public function update(Request $request)
  {
    $valitatedData = Validator::make($request->all(), [
      'id' => 'required|integer|exists:users,id',
      'names' => 'nullable|string|max:255',
      'last_names' => 'nullable|string|max:255',
      'age' => 'nullable|integer',
      'tel' => 'nullable|max:10|string|unique:users,tel,' . $request->id,
      'dni' => 'nullable|string|max:255|unique:users,dni,' . $request->id,
      'email' => 'nullable|string|email|max:255|unique:users,email,' . $request->id,
      'password' => 'nullable|string|min:8|max:15',
      'id_estado' => 'nullable|exists:estado,id',
      'id_roles' => 'nullable|exists:roles,id'
    ]);

    if ($valitatedData->fails()) {
      return $valitatedData->errors();
    }

    $data = $valitatedData->validated();
    $user = User::find($data['id']);
    if (!$user) {
      return response()->json(['message' => 'Usuario no encontrado'], 404);
    }
    $user->names = $data['names'] ?? $user->names;
    $user->last_names = $data['last_names'] ?? $user->last_names;
    $user->age = $data['age'] ?? $user->age;
    $user->tel = $data['tel'] ?? $user->tel;
    $user->dni = $data['dni'] ?? $user->dni;
    $user->email = $data['email'] ?? $user->email;
    if (!empty($data['password'])) {
      $user->password = Hash::make($data['password']);
    }

    $user->id_estado = $data['id_estado'] ?? $user->id_estado;
    $user->id_roles = $data['id_roles'] ?? $user->id_roles;
    $user->save();

    return response()->json(['message' => 'Usuario actualizado exitosamente', 'user' => $user], 200);
  }

  public function index()
  {
    $users = User::all()->load('roles')->load('estado');
    return $users;
  }

  public function delete(Request $request)
  {
    $valitatedData = Validator::make($request->all(), [
      'id' => 'required|integer|exists:users,id'
    ]);
    if ($valitatedData->fails()) {
      return $valitatedData->errors();
    }
    $data = $valitatedData->validated();
    $user = User::find($data['id']);
    User::destroy($data['id']);
    return response()->json(['message' => 'Usuario Eliminado', 'user' => $user], 200);
  }

  public function me()
  {
    $usuario = JWTAuth::parseToken()->authenticate();

    if (!$usuario) {
      return response()->json(['message' => 'No autenticado'], 401);
    }

    $usuario->load('roles.ListPaginas'); // Cargar el rol y sus páginas permitidas

    return response()->json($usuario);
  }
}
