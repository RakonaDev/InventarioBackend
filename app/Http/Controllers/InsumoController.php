<?php

namespace App\Http\Controllers;

use App\Models\Insumo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class InsumoController extends Controller
{
  //
  public function index () {
    $insumos = Insumo::all();
    return $insumos;
  }

  public function show ($limit, $page) {
    $insumo = Insumo::paginate($limit, ['*'], 'insumos', $page);
    return $insumo;
  }
  /**
   * Falta el ingreso de imagenes
   */
  public function create (Request $request) {
    $valitateData = Validator::make($request->all(), [
      'nombre' => 'required|string|max:255',
    ]);

    if ($valitateData->fails()) {
      return redirect()->back()->withErrors($valitateData->errors());
    }

    $result = Insumo::create($valitateData->getData());
    return $result;
  }
  
  
}
