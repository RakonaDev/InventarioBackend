<?php

namespace App\Http\Controllers;

use App\Models\Permisos;
use App\Models\Roles;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RolesController extends Controller
{
  public function index () {
    $insumos = Roles::all()->load('ListPaginas');
    return $insumos;
  }

  public function show ($limit, $page) {
    $insumo = Roles::paginate($limit, ['*'], 'roles', $page);
    return $insumo;
  }

  public function create (Request $request) {
    $valitatedData = Validator::make($request->all(), [
      'name' => 'required|string|max:255|unique:roles',
      'paginas' => 'required|array',
      'paginas.*' => 'exists:paginas,id'
    ]);

    if ($valitatedData->fails()) {
      return response()->json($valitatedData->errors(), 400);
    }
    $result = Roles::create([
      'name' => $request->input('name')
    ]);

    if ($request->has('paginas')) {
      $paginas = $request->input('paginas');
      foreach ($paginas as $pagina_id) {
        Permisos::create([
          'id_rol' => $result->id,
          'id_pagina' => $pagina_id
        ]);
    }
    }
    return response()->json([
      'message' => 'Rol registrado correctamente',
      'roles' => $result->load('ListPaginas')
    ]);
  }

  public function updateData (Request $request) {
    $valitatedData = Validator::make($request->all(), [
      'name' => 'required|string|max:255',
      'id' => 'required|integer|exists:roles,id',
      'paginas' => 'required|array',
      'paginas.*' => 'exists:paginas,id'
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
    $rol->ListPaginas()->sync($request->input('paginas'));
    $rol->name = $roledata['name'];
    $rol->save();

    return response()->json([
      'roles' => $rol->load('ListPaginas')
    ], 200);
  }

  public function delete(Request $request, $id) {
    /*
    $valitatedData = Validator::make($request->all(), [
      'id' => 'required|integer|exists:roles,id'
    ]);
    
    if ($valitatedData->fails()) {
      return $valitatedData->errors();
    }
    
    $data = $valitatedData->validated();
    */
    $roles = Roles::find($id);
    if ($roles) {
      $roles->ListPaginas()->detach();
    }
    else {
      return response()->json(['message'=> 'Usuario No Existe'], 404);
    }
    Roles::destroy($id);
    return response()->json(['message'=> 'Usuario Eliminado','roles'=> $roles], 200);
  }
}
