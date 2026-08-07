<?php

namespace App\Http\Controllers;

use App\Models\Inscripcion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class InscripcionController extends Controller
{
    public function index(Request $request)
    {
        $query = Inscripcion::with(['voluntario.usuario', 'campana', 'actividad']);

        if ($request->filled('id_voluntario')) {
            $query->where('id_voluntario', $request->id_voluntario);
        }
        if ($request->filled('id_campana')) {
            $query->where('id_campana', $request->id_campana);
        }
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        return response()->json($query->orderBy('fecha_inscripcion', 'desc')->paginate(15));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_voluntario' => [
                'required',
                'exists:voluntarios,id_voluntario',
                \Illuminate\Validation\Rule::unique('inscripciones')->where(
                    fn ($q) => $q->where('id_campana', $request->id_campana)
                ),
            ],
            'id_campana' => 'required|exists:campanas,id_campana',
            'id_actividad' => 'nullable|exists:actividades,id_actividad',
            'estado' => 'in:pendiente,aprobada,rechazada,cancelada,finalizada',
            'comentarios' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();
        $data['fecha_inscripcion'] = now();

        $inscripcion = Inscripcion::create($data);

        return response()->json($inscripcion->load(['voluntario', 'campana']), 201);
    }

    public function show($id)
    {
        $inscripcion = Inscripcion::with(['voluntario.usuario', 'campana', 'actividad', 'asistencias', 'horasVoluntariado', 'evaluacion'])
            ->findOrFail($id);

        return response()->json($inscripcion);
    }

    public function update(Request $request, $id)
    {
        $inscripcion = Inscripcion::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'id_actividad' => 'nullable|exists:actividades,id_actividad',
            'estado' => 'in:pendiente,aprobada,rechazada,cancelada,finalizada',
            'asistencia_confirmada' => 'boolean',
            'horas_acreditadas' => 'nullable|numeric|min:0',
            'comentarios' => 'nullable|string',
            'motivo_cancelacion' => 'nullable|string',
            'calificacion_voluntario' => 'nullable|integer|min:1|max:5',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();

        // Marca automáticamente las fechas de aprobación/cancelación según el nuevo estado
        if (isset($data['estado'])) {
            if ($data['estado'] === 'aprobada') {
                $data['fecha_aprobacion'] = now();
            } elseif ($data['estado'] === 'cancelada') {
                $data['fecha_cancelacion'] = now();
            }
        }

        $inscripcion->update($data);

        return response()->json($inscripcion);
    }

    public function destroy($id)
    {
        $inscripcion = Inscripcion::findOrFail($id);
        $inscripcion->delete();

        return response()->json(['message' => 'Inscripción eliminada correctamente']);
    }
}
