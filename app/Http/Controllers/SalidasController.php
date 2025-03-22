<?php

namespace App\Http\Controllers;

use App\Models\Insumo;
use App\Models\Salida;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SalidasController extends Controller
{
  public function index()
  {
    return Salida::all()->load('producto');
  }
  public function store(Request $request)
  {
    $validator = Validator::make($request->all(), [
      'cantidad' => 'required|integer|min:1',
      'id_producto' => 'required|exists:insumos,id',
    ]);

    if ($validator->fails()) {
      return response()->json($validator->errors(), 400);
    }
    $data = $validator->validated();
    $producto = Insumo::find($data['id_producto']);

    if ($producto->cantidad < $data['cantidad']) {
      return response()->json(['message' => 'No hay suficiente stock disponible'], 400);
    }
    $salida = Salida::create([
      'cantidad' => $data['cantidad'],
      'id_producto' => $data['id_producto']
    ]);
    $producto->decrement('cantidad', $data['cantidad']);

    return response()->json([
      'message' => 'Salida registrada exitosamente',
      'salidas' => $salida->load('producto'),
      'insumos' => $producto
    ]);
  }

  public function paginateSalidas ($limit = 10, $page = 1) {
    $salidas = Salida::with('producto')->paginate($limit, ['*'], 'page', $page);
    $response = [
      'salidas' => $salidas->items(),
      'currentPage' => $salidas->currentPage(),
      'totalPages' => $salidas->lastPage()
    ];
    return response()->json($response, 200);         
  }
}
