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
      'nombre' => 'required|string|max:255',
    ]);

    if ($valitatedData->fails()) {
      return redirect()->back()->withErrors($valitatedData->errors());
    }

    $result = Roles::create($valitatedData->getData());
    return $result;
  }

  public function updateData (Request $request, $id) {
    $valitatedData = Validator::make($request->all(), [
      'nombre' => 'required|string|max:255',
    ]);
    
    if ($valitatedData->fails()) {
      return response()->json(['error' => $valitatedData->errors()], 400);
    }
    
    $rol = Roles::find($id);
    if ($rol->isEmpty()) {
      return response()->json([
        'error' => 'No se encontro el rol',
      ], 404);
    }

    $roledata = $valitatedData->getData();
    $rol->nombre = $roledata['nombre'];
    $rol->save();
  }

}
