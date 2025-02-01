<?php

namespace App\Http\Controllers;

use App\Models\Paginas;
use Illuminate\Http\Request;

class PaginasController extends Controller
{
  public function index () {
    $paginas = Paginas::all();
    return response()->json($paginas);
  }
}
