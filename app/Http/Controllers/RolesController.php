<?php

namespace App\Http\Controllers;

use App\Models\Roles;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RolesController extends Controller
{
  public function index () {
    $insumos = Roles::all();
    return $insumos;
  }

  public function show ($limit, $page) {
    $insumo = Roles::paginate($limit, ['*'], 'roles', $page);
    return $insumo;
  }

  public function create (Request $request) {
    $valitatedData = Validator::make($request->all(), [
      'name' => 'required|string|max:255',
    ]);

    if ($valitatedData->fails()) {
      return $valitatedData->errors();
    }
    $result = Roles::create([
      'name' => $request->input('name')
    ]);
    return response()->json([
      'message' => 'Rol registrado correctamente',
      'roles' => $result
    ]);
  }

  public function updateData (Request $request) {
    $valitatedData = Validator::make($request->all(), [
      'name' => 'required|string|max:255',
      'id' => 'required|integer|exists:roles,id'
    ]);
    
    if ($valitatedData->fails()) {
      return response()->json(['error' => $valitatedData->errors()], 400);
    }

    $roledata = $valitatedData->validated();    
    $rol = Roles::find($roledata['id']);
    if (!$rol) {
      return response()->json([
        'error' => 'No se encontro el rol',
      ], 404);
    }

    $rol->name = $roledata['name'];
    $rol->save();

    return response()->json([
      'roles' => $rol
    ], 200);
  }

  public function delete(Request $request) {
    $valitatedData = Validator::make($request->all(), [
      'id' => 'required|integer|exists:roles,id'
    ]);
    if ($valitatedData->fails()) {
      return $valitatedData->errors();
    }
    $data = $valitatedData->validated();
    $roles = Roles::find($data['id']);
    Roles::destroy($data['id']);
    return response()->json(['message'=> 'Usuario Eliminado','roles'=> $roles], 200);
  }
}
