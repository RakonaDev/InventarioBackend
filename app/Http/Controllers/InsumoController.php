<?php

namespace App\Http\Controllers;

use App\Models\Compra;
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
    return response()->json(Insumo::all()->load('categorias')->load('proveedor'), 200);
  }

  public function store(Request $request)
  {
    $request->merge([
      'fecha_creacion' => $request->filled('fecha_creacion') ? $request->fecha_creacion : null,
      'fecha_vencimiento' => $request->filled('fecha_vencimiento') ? $request->fecha_vencimiento : null,
    ]);
    $validator = Validator::make($request->all(), [
      'nombre' => 'required|string|max:255',
      'descripcion' => 'required|string',
      'precio' => 'required|numeric|min:1',
      'cantidad' => 'required|integer|min:1',
      'id_categoria' => 'required|exists:categorias,id',
      'id_proveedor' => 'required|exists:proveedores,id',
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

    $data = $request->all();

    if ($request->hasFile('comprobante')) {
      $path = $request->file('comprobante')->store('compras', 'public');
      $data['comprobante'] = asset("storage/{$path}");
    }

    if (isset($data['fecha_creacion']) && isset($data['fecha_vencimiento'])) {
      $data['vida_util_dias'] = now()->parse($data['fecha_creacion'])->diffInDays($data['fecha_vencimiento']);
    }

    $insumo = Insumo::create($data);

    $compra = Compra::create([
      'cantidad' => $insumo->cantidad,
      'comprobante' => $data['comprobante'] ?? null,
      'total' => $insumo->precio * $insumo->cantidad,
      'id_producto' => $insumo->id,
      'fecha_ingreso' => $data['fecha_creacion'] ?? null,
      'fecha_vencimiento' => $data['fecha_vencimiento'] ?? null,
      'vida_utiles_dias' => $data['vida_util_dias'] ?? null,
    ]);

    return response()->json([
      'message' => 'Insumo y compra registrados correctamente',
      'insumos' => $insumo->load('categorias')->load('proveedor'),
      'compras' => $compra->load('producto')
    ], 201);
  }

  public function show($id)
  {
    $insumo = Insumo::find($id);
    if (!$insumo) {
      return response()->json(['message' => 'Insumo no encontrado'], 404);
    }
    return response()->json($insumo, 200);
  }

  public function update(Request $request, $id)
  {
    $insumo = Insumo::find($id);
    if (!$insumo) {
      return response()->json(['message' => 'Insumo no encontrado'], 404);
    }

    $validator = Validator::make($request->all(), [
      'nombre' => 'sometimes|string|max:255',
      'descripcion' => 'sometimes|string',
      'precio' => 'sometimes|numeric|min:1',
      'cantidad' => 'sometimes|integer|min:1',
      'id_categoria' => 'sometimes|exists:categorias,id',
      'id_proveedor' => 'sometimes|exists:proveedores,id',
    ]);

    if ($validator->fails()) {
      return response()->json($validator->errors(), 400);
    }

    $insumo->update($validator->validated());

    return response()->json([
      'message' => 'Insumo actualizado correctamente',
      'insumos' => $insumo->load('categorias')->load('proveedor'),
    ], 200);
  }

  public function destroy($id)
  {
    $insumo = Insumo::find($id);
    if (!$insumo) {
      return response()->json(['message' => 'Insumo no encontrado'], 404);
    }
    $compra = Compra::where('id_producto', $insumo->id)->first();

    // Si existe una compra y tiene un comprobante, eliminar el archivo
    if ($compra && !empty($compra->comprobante)) {
      // Eliminar el archivo solo si existe y tiene una ruta válida
      if (Storage::exists($compra->comprobante)) {
        Storage::delete($compra->comprobante);
      }
    }
    $insumo->delete();

    return response()->json(['message' => 'Insumo eliminado correctamente', 'insumos' => $insumo, 'compras' => $compra], 200);
  }

  public function create(Request $request)
  {
    $valitateData = Validator::make($request->all(), [
      'nombre' => 'required|string|max:255',
      'descripcion' => 'required|string',
      'precio' => 'required|numeric|min:0',
      'vida_util_dias' => 'required|integer|min:0',
      'id_categoria' => 'required|integer|exists:categorias,id',
      'id_proveedor' => 'required|integer|exists:proveedores,id',
      'id_tipo_insumo' => 'required|integer|exists:tipo_insumo,id',
      'imagen' => 'file|mimes:jpg,jpeg,png|max:5048',
    ]);

    if ($valitateData->fails()) {
      return response()->json($valitateData->errors(), 404);
    }

    $result = Insumo::create([
      'nombre' => $request->input('nombre'),
      'descripcion' => $request->input('descripcion'),
      'precio' => $request->input('precio'),
      'vida_util_dias' => $request->input('vida_util_dias'),
      'id_tipo_consumo' => $request->input('id_tipo_insumo'),
      'id_proveedor' => $request->input('id_proveedor'),
      'id_categoria' => $request->input('id_categoria'),
    ]);

    /*
    if ($request->has('imagen')) {
      foreach ($request->file('imagen') as $imagen) {
        $path = $imagen->store('productos', 'public'); // Guardar en storage/app/public/productos
        ImagenInsumos::create([
          'producto_id' => $result->id,
          'url' => Storage::url($path), // Guardar la URL pública
        ]);
      }
    }
    */
    if ($request->hasFile('imagen')) {
      $imagen = $request->file('imagen'); // Obtener el archivo de imagen

      // Asegurarse de que la imagen es válida
      if ($imagen->isValid()) {
        // Crear un nombre único para la imagen y guardarla
        $filename = uniqid() . '.' . $imagen->getClientOriginalExtension(); // Nombre único con extensión original
        $path = $imagen->storeAs('productos', $filename, 'public'); // Guardar en el directorio 'productos' con almacenamiento público
        $url = url("storage/" . $path);
        // Crear el registro de la imagen en la base de datos
        ImagenInsumos::create([
          'id_insumo' => $result->id,
          'url' => Storage::url($path), // Guardar la URL pública de la imagen
        ]);
      } else {
        return response()->json(['message' => 'La imagen no es válida.'], 400);
      }
    }
    return response()->json([
      'message' => 'Producto creado exitosamente.',
      'producto' => $result->load('imagenes'), // Retornar el producto con sus imágenes
    ], 201);
  }
  public function paginateInsumos($limit = 10, $page = 1)
  {
    $productos = Insumo::with('categorias')->with('proveedor')->paginate($limit, ['*'], 'page', $page);
    $response = [
      'insumos' => $productos->items(),
      'currentPage' => $productos->currentPage(),
      'totalPages' => $productos->lastPage()
    ];
    return response()->json($response, 200);
  }

  public function buscarInsumosPorNombrePaginado(Request $request)
  {
    $query = $request->input('query');
    $limit = $request->input('limit', 10); // Obtén el límite de la petición o usa 10 por defecto
    $page = $request->input('page', 1);   // Obtén la página de la petición o usa 1 por defecto

    $productos = Insumo::with('categorias')
      ->with('proveedor')
      ->where('nombre', 'like', '%' . $query . '%') // Asumo que la columna del nombre es 'nombre'
      ->paginate($limit, ['*'], 'page', $page);

    $response = [
      'insumos' => $productos->items(),
      'currentPage' => $productos->currentPage(),
      'totalPages' => $productos->lastPage(),
      'total' => $productos->total(), // Agrega el total de elementos si lo necesitas
    ];

    return response()->json($response, 200);
  }
}
