<?php

namespace App\Http\Controllers;

use App\Models\EvaluacionCampana;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EvaluacionCampanaController extends Controller
{
    public function index(Request $request)
    {
        $query = EvaluacionCampana::with('inscripcion.campana');

        if ($request->filled('id_inscripcion')) {
            $query->where('id_inscripcion', $request->id_inscripcion);
        }

        return response()->json($query->orderBy('fecha_evaluacion', 'desc')->paginate(15));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_inscripcion' => 'required|exists:inscripciones,id_inscripcion|unique:evaluaciones_campana,id_inscripcion',
            'puntuacion' => 'nullable|integer|min:1|max:5',
            'comentario' => 'nullable|string',
            'recomendaciones' => 'nullable|string',
            'aspectos_positivos' => 'nullable|string',
            'aspectos_mejorar' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();
        $data['fecha_evaluacion'] = now();

        $evaluacion = EvaluacionCampana::create($data);

        return response()->json($evaluacion, 201);
    }

    public function show($id)
    {
        $evaluacion = EvaluacionCampana::with('inscripcion.campana')->findOrFail($id);
        return response()->json($evaluacion);
    }

    public function update(Request $request, $id)
    {
        $evaluacion = EvaluacionCampana::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'puntuacion' => 'nullable|integer|min:1|max:5',
            'comentario' => 'nullable|string',
            'recomendaciones' => 'nullable|string',
            'aspectos_positivos' => 'nullable|string',
            'aspectos_mejorar' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $evaluacion->update($validator->validated());

        return response()->json($evaluacion);
    }

    public function destroy($id)
    {
        $evaluacion = EvaluacionCampana::findOrFail($id);
        $evaluacion->delete();

        return response()->json(['message' => 'Evaluación eliminada correctamente']);
    }
}
