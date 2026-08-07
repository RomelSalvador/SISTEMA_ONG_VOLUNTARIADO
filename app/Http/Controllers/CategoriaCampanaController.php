<?php

namespace App\Http\Controllers;

use App\Models\CategoriaCampana;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CategoriaCampanaController extends Controller
{
    public function index()
    {
        return response()->json(CategoriaCampana::orderBy('nombre')->get());
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:50|unique:categorias_campanas,nombre',
            'icono' => 'nullable|string|max:50',
            'color_hex' => 'nullable|string|max:7',
            'descripcion' => 'nullable|string',
            'activo' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $categoria = CategoriaCampana::create($validator->validated());

        return response()->json($categoria, 201);
    }

    public function show($id)
    {
        $categoria = CategoriaCampana::with('campanas')->findOrFail($id);
        return response()->json($categoria);
    }

    public function update(Request $request, $id)
    {
        $categoria = CategoriaCampana::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'nombre' => 'sometimes|required|string|max:50|unique:categorias_campanas,nombre,' . $id . ',id_categoria',
            'icono' => 'nullable|string|max:50',
            'color_hex' => 'nullable|string|max:7',
            'descripcion' => 'nullable|string',
            'activo' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $categoria->update($validator->validated());

        return response()->json($categoria);
    }

    public function destroy($id)
    {
        $categoria = CategoriaCampana::findOrFail($id);
        $categoria->delete();

        return response()->json(['message' => 'Categoría eliminada correctamente']);
    }
}
