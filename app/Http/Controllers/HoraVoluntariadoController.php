<?php

namespace App\Http\Controllers;

use App\Models\HoraVoluntariado;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class HoraVoluntariadoController extends Controller
{
    public function index(Request $request)
    {
        $query = HoraVoluntariado::with(['inscripcion.voluntario.usuario', 'aprobadoPor']);

        if ($request->filled('id_inscripcion')) {
            $query->where('id_inscripcion', $request->id_inscripcion);
        }
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        return response()->json($query->orderBy('fecha_actividad', 'desc')->paginate(15));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_inscripcion' => 'required|exists:inscripciones,id_inscripcion',
            'horas_calculadas' => 'required|numeric|min:0',
            'fecha_actividad' => 'required|date',
            'hora_inicio' => 'nullable',
            'hora_fin' => 'nullable',
            'descripcion_actividad' => 'nullable|string',
            'aprobado_por' => 'nullable|exists:organizadores,id_organizador',
            'estado' => 'in:pendiente,aprobado,rechazado',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $hora = HoraVoluntariado::create($validator->validated());

        return response()->json($hora, 201);
    }

    public function show($id)
    {
        $hora = HoraVoluntariado::with(['inscripcion.voluntario.usuario', 'aprobadoPor'])->findOrFail($id);
        return response()->json($hora);
    }

    public function update(Request $request, $id)
    {
        $hora = HoraVoluntariado::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'horas_calculadas' => 'sometimes|required|numeric|min:0',
            'descripcion_actividad' => 'nullable|string',
            'aprobado_por' => 'nullable|exists:organizadores,id_organizador',
            'estado' => 'in:pendiente,aprobado,rechazado',
            'comentario_aprobacion' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();

        if (isset($data['estado']) && in_array($data['estado'], ['aprobado', 'rechazado'])) {
            $data['fecha_aprobacion'] = now();
        }

        $hora->update($data);

        return response()->json($hora);
    }

    public function destroy($id)
    {
        $hora = HoraVoluntariado::findOrFail($id);
        $hora->delete();

        return response()->json(['message' => 'Registro de horas eliminado correctamente']);
    }
}
