<?php

namespace App\Http\Controllers;

use App\Models\Permisos;
use App\Models\Roles;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class RolesController extends Controller
{
  public function index()
  {
    $insumos = Roles::all()->load('ListPaginas');
    return $insumos;
  }

  public function show($limit, $page)
  {
    $insumo = Roles::paginate($limit, ['*'], 'roles', $page);
    return $insumo;
  }

  public function create(Request $request)
  { 
    
    $valitatedData = Validator::make($request->all(), [
      'nombres' => 'required|string|max:255|unique:roles',
      'paginas' => 'required|array',
      'paginas.*' => 'integer|exists:paginas,id'
    ]);
    
    if ($valitatedData->fails()) {
      return response()->json($valitatedData->errors(), 400);
    }

    $result = Roles::create([
      'name' => $request->input('nombres')
    ]);

    if ($request->has('paginas')) {
      $paginas = $request->input('paginas');
      foreach ($paginas as $pagina_id) {
        Log::info($pagina_id);
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

  public function updateData(Request $request, $id)
  {
    $valitatedData = Validator::make($request->all(), [
      'nombres' => 'required|string|max:255',
      'paginas' => 'required|array',
      'paginas.*' => 'exists:paginas,id'
    ]);

    if ($valitatedData->fails()) {
      return response()->json($valitatedData->errors(), 400);
    }

    $roledata = $valitatedData->validated();
    $rol = Roles::find($id);
    if (!$rol) {
      return response()->json([
        'error' => 'No se encontro el rol',
      ], 404);
    }
    $rol->ListPaginas()->sync($request->input('paginas'));
    $rol->name = $roledata['nombres'];
    $rol->save();

    return response()->json([
      'roles' => $rol->load('ListPaginas')
    ], 200);
  }

  public function delete(Request $request, $id)
  {
    /*
    $valitatedData = Validator::make($request->all(), [
      'id' => 'required|integer|exists:roles,id'
    ]);
    
    if ($valitatedData->fails()) {
      return $valitatedData->errors();
    }
    
    $data = $valitatedData->validated();
    */
    if ($id == 1 || $id == "1") {
      return response()->json([ 'message' => 'El rol Administrador no se borra' ], 500);
    }

    $roles = Roles::find($id);
    if ($roles) {
      $roles->ListPaginas()->detach();
    } else {
      return response()->json(['message' => 'Rol No Existe'], 404);
    }
    Roles::destroy($id);
    return response()->json(['message' => 'Usuario Eliminado', 'roles' => $roles], 200);
  }

  public function paginateRoles($limit, $page)
  {
    $roles = Roles::with('ListPaginas')->paginate($limit, ['*'], 'page', $page);
    $response = [
      'roles' => $roles->items(),
      'currentPage' => $roles->currentPage(),
      'totalPages' => $roles->lastPage()
    ];
    return response()->json($response, 200);
  }
}
