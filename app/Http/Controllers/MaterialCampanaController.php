<?php

namespace App\Http\Controllers;

use App\Models\MaterialCampana;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MaterialCampanaController extends Controller
{
    public function index(Request $request)
    {
        $query = MaterialCampana::with('campana');

        if ($request->filled('id_campana')) {
            $query->where('id_campana', $request->id_campana);
        }

        return response()->json($query->paginate(15));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_campana' => 'required|exists:campanas,id_campana',
            'nombre_material' => 'required|string|max:100',
            'cantidad_necesaria' => 'nullable|integer|min:0',
            'cantidad_recolectada' => 'integer|min:0',
            'unidad_medida' => 'nullable|string|max:20',
            'proveedor' => 'nullable|string|max:100',
            'costo_unitario' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $material = MaterialCampana::create($validator->validated());

        return response()->json($material, 201);
    }

    public function show($id)
    {
        $material = MaterialCampana::with('campana')->findOrFail($id);
        return response()->json($material);
    }

    public function update(Request $request, $id)
    {
        $material = MaterialCampana::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'nombre_material' => 'sometimes|required|string|max:100',
            'cantidad_necesaria' => 'nullable|integer|min:0',
            'cantidad_recolectada' => 'integer|min:0',
            'unidad_medida' => 'nullable|string|max:20',
            'proveedor' => 'nullable|string|max:100',
            'costo_unitario' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $material->update($validator->validated());

        return response()->json($material);
    }

    public function destroy($id)
    {
        $material = MaterialCampana::findOrFail($id);
        $material->delete();

        return response()->json(['message' => 'Material eliminado correctamente']);
    }
}
