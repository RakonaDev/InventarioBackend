<?php

namespace App\Http\Controllers;

use App\Models\Proveedor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProveedorController extends Controller
{
  // Index: Display a listing of the resource
  public function index()
  {
    $proveedores = Proveedor::all();
    return response()->json($proveedores);
  }

  // Store: Store a newly created resource in storage
  public function store(Request $request)
  {
    $validator = Validator::make($request->all(), [
      'name' => 'required|string|max:255',
      'phone' => 'required|string|max:20',
      'email' => 'required|email|unique:proveedores,email',
      'ruc' => 'required|string|max:255',
      'address' => 'required|string|max:255',
    ]);

    if ($validator->fails()) {
      return response()->json(['errors' => $validator->errors()], 422);
    }

    $proveedor = Proveedor::create($validator->validated());

    return response()->json(['message' => 'Proveedor creado con éxito', 'proveedores' => $proveedor], 201);
  }

  // Show: Display the specified resource
  public function show($id)
  {
    $proveedor = Proveedor::find($id);

    if (!$proveedor) {
      return response()->json(['message' => 'Proveedor no encontrado'], 404);
    }

    return response()->json($proveedor);
  }

  // Update: Update the specified resource in storage
  public function update(Request $request, $id)
  {
    $validator = Validator::make($request->all(), [
      'name' => 'required|string|max:255',
      'phone' => 'required|string|max:20',
      'email' => 'required|email|unique:proveedores,email,' . $id,
      'ruc' => 'required|string|max:255',
      'address' => 'required|string|max:255',
    ]);

    if ($validator->fails()) {
      return response()->json(['errors' => $validator->errors()], 422);
    }

    $proveedor = Proveedor::find($id);

    if (!$proveedor) {
      return response()->json(['message' => 'Proveedor no encontrado'], 404);
    }

    $proveedor->update($validator->validated());

    return response()->json(['message' => 'Proveedor actualizado con éxito', 'proveedores' => $proveedor]);
  }

  // Destroy: Remove the specified resource from storage
  public function destroy($id)
  {
    $proveedor = Proveedor::find($id);

    if (!$proveedor) {
      return response()->json(['message' => 'Proveedor no encontrado'], 404);
    }

    $proveedor->delete();

    return response()->json(['message' => 'Proveedor eliminado con éxito', 'proveedores' => $proveedor]);
  }
}
