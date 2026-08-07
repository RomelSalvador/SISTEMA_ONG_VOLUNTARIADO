<?php

namespace App\Http\Controllers;

use App\Models\Actividad;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ActividadController extends Controller
{
    public function index(Request $request)
    {
        $query = Actividad::with('campana');

        if ($request->filled('id_campana')) {
            $query->where('id_campana', $request->id_campana);
        }
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        return response()->json($query->orderBy('fecha')->paginate(15));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_campana' => 'required|exists:campanas,id_campana',
            'nombre' => 'required|string|max:150',
            'descripcion' => 'nullable|string',
            'fecha' => 'required|date',
            'hora_inicio' => 'required',
            'hora_fin' => 'nullable',
            'capacidad_max' => 'nullable|integer|min:1',
            'estado' => 'in:programada,en_curso,completada,cancelada',
            'responsable' => 'nullable|string|max:100',
            'observaciones' => 'nullable|string',
            'duracion_estimada' => 'nullable|integer|min:0',
            'requiere_materiales' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $actividad = Actividad::create($validator->validated());

        return response()->json($actividad, 201);
    }

    public function show($id)
    {
        $actividad = Actividad::with(['campana', 'inscripciones'])->findOrFail($id);
        return response()->json($actividad);
    }

    public function update(Request $request, $id)
    {
        $actividad = Actividad::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'id_campana' => 'sometimes|required|exists:campanas,id_campana',
            'nombre' => 'sometimes|required|string|max:150',
            'descripcion' => 'nullable|string',
            'fecha' => 'sometimes|required|date',
            'hora_inicio' => 'sometimes|required',
            'hora_fin' => 'nullable',
            'capacidad_max' => 'nullable|integer|min:1',
            'estado' => 'in:programada,en_curso,completada,cancelada',
            'responsable' => 'nullable|string|max:100',
            'observaciones' => 'nullable|string',
            'duracion_estimada' => 'nullable|integer|min:0',
            'requiere_materiales' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $actividad->update($validator->validated());

        return response()->json($actividad);
    }

    public function destroy($id)
    {
        $actividad = Actividad::findOrFail($id);
        $actividad->delete();

        return response()->json(['message' => 'Actividad eliminada correctamente']);
    }
}
