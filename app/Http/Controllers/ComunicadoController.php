<?php

namespace App\Http\Controllers;

use App\Models\Comunicado;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ComunicadoController extends Controller
{
    public function index(Request $request)
    {
        $query = Comunicado::query();

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }
        if ($request->filled('categoria')) {
            $query->where('categoria', $request->categoria);
        }
        if ($request->boolean('solo_publicos')) {
            $query->where('publico', true)->where('estado', 'publicado');
        }

        return response()->json($query->orderBy('fecha_publicacion', 'desc')->paginate(15));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'titulo' => 'required|string|max:150',
            'contenido' => 'required|string',
            'imagen' => 'nullable|string',
            'fecha_expiracion' => 'nullable|date',
            'estado' => 'in:publicado,borrador,archivado',
            'publico' => 'boolean',
            'categoria' => 'string|max:50',
            'autor' => 'nullable|string|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();
        $data['fecha_publicacion'] = now();

        $comunicado = Comunicado::create($data);

        return response()->json($comunicado, 201);
    }

    public function show($id)
    {
        $comunicado = Comunicado::findOrFail($id);
        $comunicado->increment('visitas');

        return response()->json($comunicado);
    }

    public function update(Request $request, $id)
    {
        $comunicado = Comunicado::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'titulo' => 'sometimes|required|string|max:150',
            'contenido' => 'sometimes|required|string',
            'imagen' => 'nullable|string',
            'fecha_expiracion' => 'nullable|date',
            'estado' => 'in:publicado,borrador,archivado',
            'publico' => 'boolean',
            'categoria' => 'string|max:50',
            'autor' => 'nullable|string|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $comunicado->update($validator->validated());

        return response()->json($comunicado);
    }

    public function destroy($id)
    {
        $comunicado = Comunicado::findOrFail($id);
        $comunicado->delete();

        return response()->json(['message' => 'Comunicado eliminado correctamente']);
    }
}
