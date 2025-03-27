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

  public function login(Request $request)
  {
    $valitatedData = Validator::make($request->all(), [
      'email' => 'required|string|email',
      'password' => 'required|string',
    ]);

    if ($valitatedData->fails()) {
      return response()->json(['message' => $valitatedData->errors()]);
    }

    if (!$token = JWTAuth::attempt($valitatedData->getData())) {
      return response()->json(['message' => 'Credenciales inválidas'], 401);
    }
    // prueba@administrador.com
    $user = Auth::user()->load(['roles', 'roles.ListPaginas']); // Cargar el rol y sus páginas permitidas
    $cookie = cookie(
      'jwt_token',
      $token,
      60 * 24,
      '/',
      ENV("DOMAIN_COOKIE"),
      true,
      true,
      false,
      'None'
    );
    return response()->json([
      'user' => $user->load('roles.ListPaginas'),
      'token' => $token,
      'message' => 'Iniciado Correctamente'
    ])->withCookie($cookie);
  }

  public function register(Request $request)
  {
    $valitatedData = Validator::make($request->all(), [
      'nombres' => 'required|string|max:255',
      'apellidos' => 'required|string|max:255',
      'edad' => 'required|integer|min:10|max:100',
      'celular' => 'required|max:10|string|unique:users',
      'dni' => 'required|string|min:9|max:10|unique:users',
      'email' => 'required|string|email|max:255|unique:users',
      'contraseña' => 'required|string|min:8|max:15',
      'id_estado' => 'required|exists:estado,id',
      'id_roles' => 'required|exists:roles,id'
    ]);

    if ($valitatedData->fails()) {
      return response()->json($valitatedData->errors(), 404);
    }

    $user = User::create([
      'names' => $request->input('nombres'),
      'email' => $request->input('email'),
      'age' => $request->input('edad'),
      'dni' => $request->input('dni'),
      'tel' => $request->input('celular'),
      'last_names' => $request->input('apellidos'),
      'id_estado' => $request->input('id_estado'),
      'id_roles' => $request->input('id_roles'),
      'password' => Hash::make($request->input('contraseña'))
    ]);
    $user->password = $request->input('password');
    
    return response()->json([
      'message' => 'Usuario registrado correctamente',
      'user' => $user->load('roles')
    ], 200);
  }

  public function update(Request $request)
  { 
    
    $valitatedData = Validator::make($request->all(), [
      'id' => 'required|integer|exists:users,id',
      'nombres' => 'required|string|max:255',
      'apellidos' => 'required|string|max:255',
      'edad' => 'required|integer|min:10|max:100',
      'celular' => 'required|max:10|string|unique:users,tel,' . $request->id,
      'dni' => 'required|string|min:9|max:10|unique:users,dni,' . $request->id,
      'email' => 'required|string|email|max:255|unique:users,email,' . $request->id,
      'contraseña' => 'nullable|string|min:8|max:15',
      'id_estado' => 'required|exists:estado,id',
      'id_roles' => 'required|exists:roles,id'
    ]);

    if ($request->input('id') == 1 || $request->input('id') == "1") {
      return response()->json([ 'message' => 'El usuario Administrador no se borra' ], 500);
    }

    if ($valitatedData->fails()) {
      return response()->json($valitatedData->errors(), 404);
    }

    $data = $valitatedData->validated();

    $user = User::find($data['id']);
    if (!$user) {
      return response()->json(['message' => 'Usuario no encontrado'], 404);
    }
    $user->names = $data['nombres'] ?? $user->names;
    $user->last_names = $data['apellidos'] ?? $user->last_names;
    $user->age = $data['edad'] ?? $user->age;
    $user->tel = $data['celular'] ?? $user->tel;
    $user->dni = $data['dni'] ?? $user->dni;
    $user->email = $data['email'] ?? $user->email;
    if (!empty($data['contraseña'])) {
      $user->password = Hash::make($data['contraseña']);
    }

    $user->id_estado = $data['id_estado'] ?? $user->id_estado;
    $user->id_roles = $data['id_roles'] ?? $user->id_roles;
    $user->save();

    return response()->json(['message' => 'Usuario actualizado exitosamente', 'user' => $user->load('roles')->load('estado')], 200);
  }

  public function index()
  {
    $users = User::all()->load('roles')->load('estado');
    return $users;
  }

  public function delete(Request $request, $id)
  {
    /*
    $valitatedData = Validator::make($request->all(), [
      'id' => 'required|integer|exists:users,id'
    ]);
    if ($valitatedData->fails()) {
      return $valitatedData->errors();
    }
    $data = $valitatedData->validated();
    */
    if ($id == 1 || $id == "1") {
      return response()->json([ 'message' => 'El usuario Administrador no se borra' ], 500);
    }
    $user = User::find($id);
    
    User::destroy($id);
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
  public function logout()
  {
    $cookie = cookie(
      'jwt_token',
      '',
      0,
      '/',
      ENV("DOMAIN_COOKIE"),
      true,
      true,
      false,
      'None'
    );

    try {
      JWTAuth::invalidate(JWTAuth::getToken());

      return response()->json([
        'message' => 'Sesión cerrada correctamente'
      ], 200)->withCookie($cookie);
    } catch (\Exception $e) {
      return response()->json([
        'error' => 'No se pudo cerrar la sesión',
        'message' => $e->getMessage()
      ], 500);
    }
  }

  public function paginateUsers ($limit = 10, $page = 1) {
    $users = User::with('roles')->with('estado')->paginate($limit, ['*'], 'page', $page);
    $response = [
      'users' => $users->items(),
      'currentPage' => $users->currentPage(),
      'totalPages' => $users->lastPage()
    ];
    return response()->json($response, 200);
  }
}
