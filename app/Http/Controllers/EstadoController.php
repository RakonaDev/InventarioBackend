<?php

namespace App\Http\Controllers;

use App\Models\Estado;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EstadoController extends Controller
{
  //
  public function index()
  {
    $estado = Estado::all();
    return $estado;
  }

  public function show($limit, $page)
  {
    $estado = Estado::paginate($limit, ['*'], 'estado', $page);
    return $estado;
  }

  public function create(Request $request)
  {
    $valitatedData = Validator::make($request->all(), [
      'nombre' => 'required|string|max:255',
    ]);

    if ($valitatedData->fails()) {
      return $valitatedData->errors();
    }
    $result = Estado::create([
      'name' => $request->input('nombre')
    ]);
    return $result;
  }

  public function updateData(Request $request, $id)
  {
    $valitatedData = Validator::make($request->all(), [
      'nombre' => 'required|string|max:255',
    ]);

    if ($valitatedData->fails()) {
      return response()->json(['error' => $valitatedData->errors()], 400);
    }

    $estado = Estado::find($id);
    if ($estado->isEmpty()) {
      return response()->json([
        'error' => 'No se encontro el rol',
      ], 404);
    }

    $estadodata = $request->input('nombre');
    $estado->nombre = $estadodata;
    $estado->save();
  }

  public function destroy ($id) {
    Estado::destroy($id);

    return response()->json([
      'success' => true
    ], 200);
  }
}
