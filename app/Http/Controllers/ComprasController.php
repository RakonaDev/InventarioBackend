<?php

namespace App\Http\Controllers;

use App\Models\Compra;
use App\Models\Insumo;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Validator;

class ComprasController extends Controller
{
  public function index()
  {
    return Compra::all()->load('producto');
  }

  public function store(Request $request) {
    $validator = Validator::make($request->all(), [
      'cantidad' => 'required|integer|min:1',
      'id_producto' => 'required|exists:insumos,id',
      'fecha_creacion' => 'nullable|date',
      'fecha_vencimiento' => 'nullable|date|after_or_equal:fecha_creacion',
      'comprobante' => 'nullable|file|mimes:pdf|max:5048'
    ]);

    if ($validator->fails()) {
      return response()->json($validator->errors(), 400);
    }

    $data = $validator->validated();

    if ($request->hasFile('comprobante')) {
      $path = $request->file('comprobante')->store('compras', 'public');
      $data['comprobante'] = asset("storage/{$path}");
    }

    if (isset($data['fecha_creacion']) && isset($data['fecha_vencimiento'])) {
      $data['vida_util_dias'] = now()->parse($data['fecha_creacion'])->diffInDays($data['fecha_vencimiento']);
    }

    $producto = Insumo::find($request->id_producto);
    $compra = Compra::create([
      'cantidad' => $data['cantidad'],
      'comprobante' => $data['comprobante'] ?? null,
      'total' => $producto->precio * $data['cantidad'],
      'id_producto' => $data['id_producto'],
      'fecha_ingreso' => $data['fecha_creacion'] ?? null,
      'fecha_vencimiento' => $data['fecha_vencimiento'] ?? null,
      'vida_utiles_dias' => $data['vida_util_dias'] ?? null,
    ]);
  
    $producto->increment('cantidad', $data['cantidad']);
    
    return response()->json([
      'message' => 'Compra creada exitosamente',
      'compras' => $compra->load('producto'),
      'insumos' => $producto
    ]);
  }
  /*
  public function verComprobante(Request $request)
  {
    // Verificar si el usuario está autenticado
    if (!auth()->check()) {
      return response()->json(['error' => 'No autorizado'], 403);
    }

    // Desencriptar la ruta del archivo
    try {
      $path = Crypt::decrypt($request->query('path'));
    } catch (\Exception $e) {
      return response()->json(['error' => 'URL inválida'], 400);
    }

    if (!Storage::exists($path)) {
      return response()->json(['error' => 'Archivo no encontrado'], 404);
    }

    // Mostrar el PDF en el navegador sin descargarlo
    return response()->file(storage_path("app/{$path}"), [
      'Content-Type' => 'application/pdf',
      'Cache-Control' => 'no-store, no-cache, must-revalidate',
      'Pragma' => 'no-cache',
    ]);
  }
    */
}
