<?php

namespace App\Http\Controllers;

use App\Models\Insumo;
use App\Models\Salida;
use Carbon\Carbon;
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
  public function paginateSalidas($limit = 10, $page = 1, $nombre, $orden, $fecha)
  {
    $nombreProducto = $nombre;
    $ordenM = strtolower($orden === 'desc' || $orden === 'asc' ? $orden : 'desc');

    $query = Salida::with('producto');

    if ($nombreProducto !== 'todos') {
      $query->whereHas('producto', function ($q) use ($nombreProducto) {
        $q->where('nombre', 'like', '%' . $nombreProducto . '%');
      })->orderBy('created_at', '' . $ordenM);
    }

    if ($fecha !== 'todos') {
      try {
        $date = Carbon::parse($fecha)->toDateString();
        $query->whereDate('created_at', $date);
      } catch (\Exception $e) {
        // Log or handle the error if the date format is invalid
        // For now, we'll just ignore invalid dates
        return response()->json([
          'message' => 'Fecha inválida'
        ]);
      }
    }
    $salidas = $query->paginate($limit, ['*'], 'page', $page);

    $response = [
      'salidas' => $salidas->items(),
      'currentPage' => $salidas->currentPage(),
      'totalPages' => $salidas->lastPage()
    ];
    return response()->json($response, 200);
    /*
    $salidas = Salida::with('producto')
      ->orderBy('created_at', 'desc')
      ->paginate($limit, ['*'], 'page', $page);
    $response = [
      'salidas' => $salidas->items(),
      'currentPage' => $salidas->currentPage(),
      'totalPages' => $salidas->lastPage()
    ];
    return response()->json($response, 200);
    */
  }
}
