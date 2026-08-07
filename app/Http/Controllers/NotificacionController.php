<?php

namespace App\Http\Controllers;

use App\Models\Notificacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class NotificacionController extends Controller
{
    public function index(Request $request)
    {
        $query = Notificacion::query();

        if ($request->filled('id_usuario')) {
            $query->where('id_usuario', $request->id_usuario);
        }
        if ($request->filled('leida')) {
            $query->where('leida', $request->boolean('leida'));
        }

        return response()->json($query->orderBy('fecha_envio', 'desc')->paginate(20));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_usuario' => 'required|exists:usuarios,id_usuario',
            'titulo' => 'required|string|max:100',
            'mensaje' => 'required|string',
            'tipo' => 'in:info,exito,advertencia,error,recordatorio',
            'enlace_accion' => 'nullable|string',
            'prioridad' => 'in:baja,media,alta',
            'categoria_notificacion' => 'in:sistema,campana,actividad,asistencia,logro',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();
        $data['fecha_envio'] = now();

        $notificacion = Notificacion::create($data);

        return response()->json($notificacion, 201);
    }

    public function show($id)
    {
        $notificacion = Notificacion::with('usuario')->findOrFail($id);
        return response()->json($notificacion);
    }

    // Marca la notificación como leída
    public function update(Request $request, $id)
    {
        $notificacion = Notificacion::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'leida' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();
        if (!empty($data['leida'])) {
            $data['fecha_lectura'] = now();
        }

        $notificacion->update($data);

        return response()->json($notificacion);
    }

    public function destroy($id)
    {
        $notificacion = Notificacion::findOrFail($id);
        $notificacion->delete();

        return response()->json(['message' => 'Notificación eliminada correctamente']);
    }
}
