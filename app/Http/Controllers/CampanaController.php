<?php

namespace App\Http\Controllers;

use App\Models\Campana;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CampanaController extends Controller
{
    public function index(Request $request)
    {
        $query = Campana::with(['organizador.usuario', 'categoria']);

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }
        if ($request->filled('id_categoria')) {
            $query->where('id_categoria', $request->id_categoria);
        }
        if ($request->filled('buscar')) {
            $q = $request->buscar;
            $query->whereFullText(['nombre', 'descripcion', 'lugar'], $q);
        }
        if ($request->filled('fecha_desde')) {
            $query->whereDate('fecha_inicio', '>=', $request->fecha_desde);
        }

        return response()->json($query->orderBy('fecha_inicio', 'desc')->paginate(15));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_organizador' => 'nullable|exists:organizadores,id_organizador',
            'nombre' => 'required|string|max:150',
            'descripcion' => 'nullable|string',
            'lugar' => 'required|string|max:255',
            'latitud' => 'nullable|numeric|between:-90,90',
            'longitud' => 'nullable|numeric|between:-180,180',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
            'hora_inicio' => 'required',
            'hora_fin' => 'nullable',
            'capacidad_max' => 'required|integer|min:1',
            'meta_voluntarios' => 'nullable|integer|min:0',
            'id_categoria' => 'nullable|exists:categorias_campanas,id_categoria',
            'requisitos' => 'nullable|string',
            'imagen_banner' => 'nullable|string',
            'cronograma' => 'nullable|string',
            'estado' => 'in:activa,completada,cancelada,en_espera,archivada',
            'puntos_ecologicos' => 'integer|min:0',
            'impacto_ambiental' => 'nullable|string',
            'impacto_social' => 'nullable|string',
            'presupuesto_estimado' => 'nullable|numeric|min:0',
            'patrocinadores' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $campana = Campana::create($validator->validated());

        return response()->json($campana, 201);
    }

    public function show($id)
    {
        $campana = Campana::with(['organizador.usuario', 'categoria', 'actividades', 'materiales', 'inscripciones'])
            ->findOrFail($id);

        return response()->json($campana);
    }

    public function update(Request $request, $id)
    {
        $campana = Campana::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'id_organizador' => 'nullable|exists:organizadores,id_organizador',
            'nombre' => 'sometimes|required|string|max:150',
            'descripcion' => 'nullable|string',
            'lugar' => 'sometimes|required|string|max:255',
            'latitud' => 'nullable|numeric|between:-90,90',
            'longitud' => 'nullable|numeric|between:-180,180',
            'fecha_inicio' => 'sometimes|required|date',
            'fecha_fin' => 'sometimes|required|date|after_or_equal:fecha_inicio',
            'hora_inicio' => 'sometimes|required',
            'hora_fin' => 'nullable',
            'capacidad_max' => 'sometimes|required|integer|min:1',
            'meta_voluntarios' => 'nullable|integer|min:0',
            'id_categoria' => 'nullable|exists:categorias_campanas,id_categoria',
            'requisitos' => 'nullable|string',
            'imagen_banner' => 'nullable|string',
            'cronograma' => 'nullable|string',
            'estado' => 'in:activa,completada,cancelada,en_espera,archivada',
            'puntos_ecologicos' => 'integer|min:0',
            'impacto_ambiental' => 'nullable|string',
            'impacto_social' => 'nullable|string',
            'presupuesto_estimado' => 'nullable|numeric|min:0',
            'patrocinadores' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $campana->update($validator->validated());

        return response()->json($campana);
    }

    public function destroy($id)
    {
        $campana = Campana::findOrFail($id);
        $campana->delete();

        return response()->json(['message' => 'Campaña eliminada correctamente']);
    }
}
