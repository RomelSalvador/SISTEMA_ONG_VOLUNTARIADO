<?php

namespace App\Http\Controllers;

use App\Models\PuntoEcologico;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PuntoEcologicoController extends Controller
{
    public function index(Request $request)
    {
        $query = PuntoEcologico::with('voluntario.usuario');

        if ($request->filled('nivel')) {
            $query->where('nivel', $request->nivel);
        }

        // Ranking por puntos (leaderboard)
        return response()->json($query->orderBy('puntos', 'desc')->paginate(15));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_voluntario' => 'required|exists:voluntarios,id_voluntario|unique:puntos_ecologicos,id_voluntario',
            'puntos' => 'integer|min:0',
            'nivel' => 'in:Bronce,Plata,Oro,Platino,Diamante',
            'puntos_acumulados_mes' => 'integer|min:0',
            'ultimo_logro' => 'nullable|string|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();
        $data['fecha_actualizacion'] = now();

        $punto = PuntoEcologico::create($data);

        return response()->json($punto, 201);
    }

    public function show($id)
    {
        $punto = PuntoEcologico::with('voluntario.usuario')->findOrFail($id);
        return response()->json($punto);
    }

    public function update(Request $request, $id)
    {
        $punto = PuntoEcologico::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'puntos' => 'integer|min:0',
            'nivel' => 'in:Bronce,Plata,Oro,Platino,Diamante',
            'puntos_acumulados_mes' => 'integer|min:0',
            'ultimo_logro' => 'nullable|string|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();
        $data['fecha_actualizacion'] = now();

        $punto->update($data);

        return response()->json($punto);
    }

    public function destroy($id)
    {
        $punto = PuntoEcologico::findOrFail($id);
        $punto->delete();

        return response()->json(['message' => 'Registro de puntos eliminado correctamente']);
    }
}
