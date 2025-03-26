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

  public function store(Request $request)
  {
    $request->merge([
      'fecha_creacion' => $request->filled('fecha_creacion') ? $request->fecha_creacion : null,
      'fecha_vencimiento' => $request->filled('fecha_vencimiento') ? $request->fecha_vencimiento : null,
    ]);
    $validator = Validator::make($request->all(), [
      'cantidad' => 'required|integer|min:1',
      'id_producto' => 'required|exists:insumos,id',
      'fecha_creacion' => [
        'nullable',
        'date',
        function ($attribute, $value, $fail) {
          if (!empty($value) && !strtotime($value)) {
            $fail("El campo $attribute debe ser una fecha valida.");
          }
        }
      ],
      'fecha_vencimiento' => [
        'nullable',
        'date',
        'after_or_equal:fecha_creacion',
        function ($attribute, $value, $fail) {
          if (!empty($value) && !strtotime($value)) {
            $fail("El campo $attribute debe ser una fecha valida.");
          }
        }
      ],
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
  
  public function paginateCompras($limit = 10, $page = 1)
  {
    $compras = Compra::with('producto')->paginate($limit, ['*'], 'page', $page);
    $response = [
      'compras' => $compras->items(),
      'currentPage' => $compras->currentPage(),
      'totalPages' => $compras->lastPage()
    ];
    return response()->json($response, 200);
  }
}
