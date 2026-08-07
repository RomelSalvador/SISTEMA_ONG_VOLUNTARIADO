<?php

namespace App\Http\Controllers;

use App\Models\Certificado;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Str;
use Illuminate\Support\Facades\Validator;

class CertificadoController extends Controller
{
    public function index(Request $request)
    {
        $query = Certificado::with(['voluntario.usuario', 'campana']);

        if ($request->filled('id_voluntario')) {
            $query->where('id_voluntario', $request->id_voluntario);
        }
        if ($request->filled('tipo')) {
            $query->where('tipo', $request->tipo);
        }

        return response()->json($query->orderBy('fecha_emision', 'desc')->paginate(15));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_voluntario' => 'required|exists:voluntarios,id_voluntario',
            'id_campana' => 'nullable|exists:campanas,id_campana',
            'horas_certificadas' => 'required|numeric|min:0',
            'pdf_url' => 'nullable|string',
            'firmado_por' => 'nullable|string|max:100',
            'fecha_expiracion' => 'nullable|date',
            'tipo' => 'in:participacion,horas,logro',
            'descripcion_logro' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();
        $data['fecha_emision'] = now();
        $data['codigo_verificacion'] = strtoupper(Str::random(4)) . '-' . now()->format('Ymd') . '-' . strtoupper(Str::random(4));

        $certificado = Certificado::create($data);

        return response()->json($certificado, 201);
    }

    public function show($id)
    {
        $certificado = Certificado::with(['voluntario.usuario', 'campana'])->findOrFail($id);
        return response()->json($certificado);
    }

    // Verificación pública por código (útil para validar autenticidad del certificado)
    public function verificar($codigo)
    {
        $certificado = Certificado::with(['voluntario.usuario', 'campana'])
            ->where('codigo_verificacion', $codigo)
            ->firstOrFail();

        return response()->json($certificado);
    }

    public function update(Request $request, $id)
    {
        $certificado = Certificado::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'horas_certificadas' => 'sometimes|required|numeric|min:0',
            'pdf_url' => 'nullable|string',
            'firmado_por' => 'nullable|string|max:100',
            'fecha_expiracion' => 'nullable|date',
            'tipo' => 'in:participacion,horas,logro',
            'descripcion_logro' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $certificado->update($validator->validated());

        return response()->json($certificado);
    }

    public function destroy($id)
    {
        $certificado = Certificado::findOrFail($id);
        $certificado->delete();

        return response()->json(['message' => 'Certificado eliminado correctamente']);
    }
}
