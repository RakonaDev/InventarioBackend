<?php

namespace App\Http\Controllers;

use App\Models\ImagenInsumos;
use App\Models\Insumo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class InsumoController extends Controller
{
  //
  public function index()
  {
    $insumos = Insumo::with('imagenes')->get();
    return $insumos;
  }

  public function show($limit, $page)
  {
    $insumo = Insumo::with('imagenes')->paginate($limit, ['*'], 'insumos', $page);
    return $insumo;
  }

  public function showByID ($id) {
    $insumo = Insumo::with('imagenes')->findOrFail($id);
    return response()->json($insumo);
  }
  
  public function create(Request $request)
  {
    $valitateData = Validator::make($request->all(), [
      'nombre' => 'required|string|max:255',
      'descripcion' => 'required|string',
      'precio' => 'required|numeric|min:0',
      'vida_util_dias' => 'required|integer|min:0',
      'imagenes' => 'array|max:4',
      'imagenes.*' => 'file|mimes:jpg,jpeg,png|max:5048',
    ]);

    if ($valitateData->fails()) {
      return $valitateData->errors();
    }

    $result = Insumo::create([
      'nombre' => $request->input('nombre'),
      'descripcion' => $request->input('descripcion'),
      'precio' => $request->input('precio'),
      'vida_util_dias' => $request->input('vida_util_dias')
    ]);

    if ($request->has('imagenes')) {
      foreach ($request->file('imagenes') as $imagen) {
        $path = $imagen->store('productos', 'public'); // Guardar en storage/app/public/productos
        ImagenInsumos::create([
          'producto_id' => $result->id,
          'url' => Storage::url($path), // Guardar la URL pública
        ]);
      }
    }

    return response()->json([
      'message' => 'Producto creado exitosamente.',
      'producto' => $result->load('imagenes'), // Retornar el producto con sus imágenes
    ], 201);
  }

  public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'nombre' => 'string|max:255',
            'descripcion' => 'string',
            'precio' => 'numeric|min:0',
            'vida_util_dias' => 'integer|min:0',
            'imagenes' => 'array|max:4',
            'imagenes.*' => 'file|mimes:jpg,jpeg,png|max:2048',
        ]);

        $producto = Insumo::findOrFail($id);
        $producto->update($validatedData);

        if ($request->has('imagenes')) {
            // Eliminar imágenes antiguas
            foreach ($producto->imagenes as $imagen) {
                Storage::delete('public/' . basename($imagen->url));
                $imagen->delete();
            }

            // Subir imágenes nuevas
            foreach ($request->file('imagenes') as $imagen) {
                $path = $imagen->store('productos', 'public');
                ImagenInsumos::create([
                    'producto_id' => $producto->id,
                    'url' => Storage::url($path),
                ]);
            }
        }

        return response()->json($producto->load('imagenes'));
    }

  public function destroy($id)
    {
        $producto = Insumo::findOrFail($id);

        foreach ($producto->imagenes as $imagen) {
            Storage::delete('public/' . basename($imagen->url));
            $imagen->delete();
        }

        $producto->delete();

        return response()->json(['message' => 'Insumo eliminado exitosamente']);
    }
}
