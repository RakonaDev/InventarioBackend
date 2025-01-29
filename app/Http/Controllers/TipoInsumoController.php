<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TipoInsumo;
use Illuminate\Support\Facades\Validator;

class TipoInsumoController extends Controller
{
  // Index: Display a listing of the resource
  public function index()
  {
    $tiposInsumo = TipoInsumo::all();
    return response()->json($tiposInsumo);
  }

  // Store: Store a newly created resource in storage
  public function store(Request $request)
  {
    $validator = Validator::make($request->all(), [
      'nombre' => 'required|string|max:255',
    ]);

    if ($validator->fails()) {
      return response()->json(['errors' => $validator->errors()], 422);
    }

    $tipoInsumo = TipoInsumo::create($validator->validated());

    return response()->json(['message' => 'Tipo de insumo creado con éxito', 'data' => $tipoInsumo], 201);
  }

  // Show: Display the specified resource
  public function show($id)
  {
    $tipoInsumo = TipoInsumo::find($id);

    if (!$tipoInsumo) {
      return response()->json(['message' => 'Tipo de insumo no encontrado'], 404);
    }

    return response()->json($tipoInsumo);
  }

  // Update: Update the specified resource in storage
  public function update(Request $request, $id)
  {
    $validator = Validator::make($request->all(), [
      'nombre' => 'required|string|max:255',
    ]);

    if ($validator->fails()) {
      return response()->json(['errors' => $validator->errors()], 422);
    }

    $tipoInsumo = TipoInsumo::find($id);

    if (!$tipoInsumo) {
      return response()->json(['message' => 'Tipo de insumo no encontrado'], 404);
    }

    $tipoInsumo->update($validator->validated());

    return response()->json(['message' => 'Tipo de insumo actualizado con éxito', 'data' => $tipoInsumo]);
  }

  // Destroy: Remove the specified resource from storage
  public function destroy($id)
  {
    $tipoInsumo = TipoInsumo::find($id);

    if (!$tipoInsumo) {
      return response()->json(['message' => 'Tipo de insumo no encontrado'], 404);
    }

    $tipoInsumo->delete();

    return response()->json(['message' => 'Tipo de insumo eliminado con éxito']);
  }
}
