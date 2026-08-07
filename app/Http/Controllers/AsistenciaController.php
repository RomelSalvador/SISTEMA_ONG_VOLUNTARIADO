<?php

namespace App\Http\Controllers;

use App\Models\Asistencia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AsistenciaController extends Controller
{
    public function index(Request $request)
    {
        $query = Asistencia::with(['inscripcion.voluntario.usuario', 'registradoPor']);

        if ($request->filled('id_inscripcion')) {
            $query->where('id_inscripcion', $request->id_inscripcion);
        }
        if ($request->filled('fecha_asistencia')) {
            $query->whereDate('fecha_asistencia', $request->fecha_asistencia);
        }

        return response()->json($query->orderBy('fecha_asistencia', 'desc')->paginate(15));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_inscripcion' => [
                'required',
                'exists:inscripciones,id_inscripcion',
                \Illuminate\Validation\Rule::unique('asistencia')->where(
                    fn ($q) => $q->where('fecha_asistencia', $request->fecha_asistencia)
                ),
            ],
            'hora_ingreso' => 'required',
            'hora_salida' => 'nullable',
            'fecha_asistencia' => 'required|date',
            'metodo_verificacion' => 'in:qr,manual,admin,biometrico,face',
            'latitud_checkin' => 'nullable|numeric|between:-90,90',
            'longitud_checkin' => 'nullable|numeric|between:-180,180',
            'latitud_checkout' => 'nullable|numeric|between:-90,90',
            'longitud_checkout' => 'nullable|numeric|between:-180,180',
            'observacion' => 'nullable|string',
            'registrado_por' => 'nullable|exists:usuarios,id_usuario',
            'estado_asistencia' => 'in:presente,tarde,ausente,justificado',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();
        $data['fecha_registro'] = now();

        // Calcula horas si hay hora de salida
        if (!empty($data['hora_salida'])) {
            $ingreso = \Carbon\Carbon::parse($data['hora_ingreso']);
            $salida = \Carbon\Carbon::parse($data['hora_salida']);
            $data['horas_calculadas'] = round($salida->diffInMinutes($ingreso) / 60, 2);
        }

        $asistencia = Asistencia::create($data);

        return response()->json($asistencia, 201);
    }

    public function show($id)
    {
        $asistencia = Asistencia::with(['inscripcion.voluntario.usuario', 'registradoPor'])->findOrFail($id);
        return response()->json($asistencia);
    }

    public function update(Request $request, $id)
    {
        $asistencia = Asistencia::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'hora_ingreso' => 'sometimes|required',
            'hora_salida' => 'nullable',
            'metodo_verificacion' => 'in:qr,manual,admin,biometrico,face',
            'latitud_checkout' => 'nullable|numeric|between:-90,90',
            'longitud_checkout' => 'nullable|numeric|between:-180,180',
            'observacion' => 'nullable|string',
            'estado_asistencia' => 'in:presente,tarde,ausente,justificado',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();

        $horaIngreso = $data['hora_ingreso'] ?? $asistencia->hora_ingreso;
        $horaSalida = $data['hora_salida'] ?? null;

        if ($horaSalida) {
            $ingreso = \Carbon\Carbon::parse($horaIngreso);
            $salida = \Carbon\Carbon::parse($horaSalida);
            $data['horas_calculadas'] = round($salida->diffInMinutes($ingreso) / 60, 2);
        }

        $asistencia->update($data);

        return response()->json($asistencia);
    }

    public function destroy($id)
    {
        $asistencia = Asistencia::findOrFail($id);
        $asistencia->delete();

        return response()->json(['message' => 'Asistencia eliminada correctamente']);
    }
}
