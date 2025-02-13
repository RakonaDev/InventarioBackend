<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CategoriaController extends Controller
{
  public function index()
  {
    $categorias = Categoria::all();
    return response()->json($categorias);
  }

  // Store: Store a newly created resource in storage
  public function store(Request $request)
  {
    $validator = Validator::make($request->all(), [
      'nombre' => 'required|string|max:255',
      'descripcion' => 'required|string|max:255'
    ]);

    if ($validator->fails()) {
      return response()->json(['errors' => $validator->errors(), 'message' => 'Faltan Datos'], 422);
    }

    $categoria = Categoria::create($validator->validated());

    return response()->json(['message' => 'Categoria creado con éxito', 'categorias' => $categoria], 201);
  }

  // Show: Display the specified resource
  public function show($id)
  {
    $categoria = Categoria::find($id);

    if (!$categoria) {
      return response()->json(['message' => 'Categoria no encontrado'], 404);
    }

    return response()->json($categoria);
  }

  // Update: Update the specified resource in storage
  public function update(Request $request, $id)
  {
    $validator = Validator::make($request->all(), [
      'nombre' => 'required|string|max:255',
      'descripcion' => 'required|string|max:255'
    ]);

    if ($validator->fails()) {
      return response()->json(['errors' => $validator->errors()], 422);
    }

    $categoria = Categoria::find($id);

    if (!$categoria) {
      return response()->json(['message' => 'Categoria no encontrado'], 404);
    }

    $categoria->update($validator->validated());

    return response()->json(['message' => 'Categoria actualizado con éxito', 'categorias' => $categoria], 200);
  }

  // Destroy: Remove the specified resource from storage
  public function destroy($id)
  {
    $categoria = Categoria::find($id);

    if (!$categoria) {
      return response()->json(['message' => 'Categoria no encontrado'], 404);
    }

    $categoria->delete();

    return response()->json(['message' => 'Categoria eliminado con éxito', 'categorias' => $categoria], 200);
  }

  public function paginateCategorias ($limit = 10, $page = 1) {
    $categorias = Categoria::paginate($limit, ['*'], 'page', $page);
    $response = [
      'categorias' => $categorias->items(),
      'currentPage' => $categorias->currentPage(),
      'totalPages' => $categorias->lastPage()
    ];
    return response()->json($response, 200);
  }
}
