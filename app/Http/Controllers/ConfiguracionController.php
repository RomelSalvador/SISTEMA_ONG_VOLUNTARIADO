<?php

namespace App\Http\Controllers;

use App\Models\Configuracion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ConfiguracionController extends Controller
{
    public function index(Request $request)
    {
        $query = Configuracion::query();

        if ($request->filled('categoria')) {
            $query->where('categoria', $request->categoria);
        }

        return response()->json($query->orderBy('clave')->get());
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'clave' => 'required|string|max:50|unique:configuracion,clave',
            'valor' => 'nullable|string',
            'descripcion' => 'nullable|string',
            'tipo' => 'in:string,int,boolean,json',
            'modificado_por' => 'nullable|exists:usuarios,id_usuario',
            'categoria' => 'string|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();
        $data['fecha_modificacion'] = now();

        $config = Configuracion::create($data);

        return response()->json($config, 201);
    }

    public function show($id)
    {
        $config = Configuracion::findOrFail($id);
        return response()->json($config);
    }

    // Obtiene una configuración por su clave (más práctico que por id)
    public function porClave($clave)
    {
        $config = Configuracion::where('clave', $clave)->firstOrFail();
        return response()->json($config);
    }

    public function update(Request $request, $id)
    {
        $config = Configuracion::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'valor' => 'nullable|string',
            'descripcion' => 'nullable|string',
            'tipo' => 'in:string,int,boolean,json',
            'modificado_por' => 'nullable|exists:usuarios,id_usuario',
            'categoria' => 'string|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();
        $data['fecha_modificacion'] = now();

        $config->update($data);

        return response()->json($config);
    }

    public function destroy($id)
    {
        $config = Configuracion::findOrFail($id);
        $config->delete();

        return response()->json(['message' => 'Configuración eliminada correctamente']);
    }
}
