<?php

namespace App\Http\Controllers;

use App\Models\LogAuditoria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LogAuditoriaController extends Controller
{
    // Los logs son de solo lectura desde la API; se generan internamente vía LogAuditoria::create()
    // en otros controladores/servicios cuando ocurre una acción relevante.

    public function index(Request $request)
    {
        $query = LogAuditoria::with('usuario');

        if ($request->filled('id_usuario')) {
            $query->where('id_usuario', $request->id_usuario);
        }
        if ($request->filled('tabla_afectada')) {
            $query->where('tabla_afectada', $request->tabla_afectada);
        }
        if ($request->filled('fecha_desde')) {
            $query->whereDate('fecha', '>=', $request->fecha_desde);
        }

        return response()->json($query->orderBy('fecha', 'desc')->paginate(25));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_usuario' => 'nullable|exists:usuarios,id_usuario',
            'accion' => 'required|string|max:255',
            'tabla_afectada' => 'nullable|string|max:50',
            'registro_id' => 'nullable|integer',
            'datos_anteriores' => 'nullable|array',
            'datos_nuevos' => 'nullable|array',
            'ip_origen' => 'nullable|string|max:45',
            'user_agent' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();
        $data['fecha'] = now();
        $data['ip_origen'] = $data['ip_origen'] ?? $request->ip();
        $data['user_agent'] = $data['user_agent'] ?? $request->userAgent();

        $log = LogAuditoria::create($data);

        return response()->json($log, 201);
    }

    public function show($id)
    {
        $log = LogAuditoria::with('usuario')->findOrFail($id);
        return response()->json($log);
    }

    public function destroy($id)
    {
        $log = LogAuditoria::findOrFail($id);
        $log->delete();

        return response()->json(['message' => 'Log eliminado correctamente']);
    }
}
