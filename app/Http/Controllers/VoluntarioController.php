<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use App\Models\Voluntario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class VoluntarioController extends Controller
{
    public function index(Request $request)
    {
        $query = Voluntario::with('usuario');

        if ($request->filled('facultad')) {
            $query->where('facultad', $request->facultad);
        }
        if ($request->filled('disponibilidad')) {
            $query->where('disponibilidad', $request->disponibilidad);
        }

        return response()->json($query->paginate(15));
    }

    // Crea el usuario base + el registro de voluntario en una sola transacción
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|max:100|unique:usuarios,email',
            'password' => 'required|string|min:8',
            'nombres' => 'required|string|max:60',
            'apellidos' => 'required|string|max:60',
            'dni' => 'required|string|size:8|unique:usuarios,dni',
            'telefono' => 'nullable|string|max:15',
            'matricula_universitaria' => 'required|string|max:20|unique:voluntarios,matricula_universitaria',
            'facultad' => 'required|string|max:100',
            'carrera' => 'required|string|max:100',
            'ciclo' => 'nullable|string|max:10',
            'habilidades' => 'nullable|string',
            'fecha_nacimiento' => 'nullable|date',
            'direccion' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();

        $voluntario = DB::transaction(function () use ($data) {
            $usuario = Usuario::create([
                'email' => $data['email'],
                'password_hash' => Hash::make($data['password']),
                'nombres' => $data['nombres'],
                'apellidos' => $data['apellidos'],
                'dni' => $data['dni'],
                'telefono' => $data['telefono'] ?? null,
                'rol' => 'voluntario',
                'activo' => true,
                'fecha_registro' => now(),
            ]);

            return Voluntario::create([
                'id_voluntario' => $usuario->id_usuario,
                'matricula_universitaria' => $data['matricula_universitaria'],
                'facultad' => $data['facultad'],
                'carrera' => $data['carrera'],
                'ciclo' => $data['ciclo'] ?? null,
                'habilidades' => $data['habilidades'] ?? null,
                'fecha_nacimiento' => $data['fecha_nacimiento'] ?? null,
                'direccion' => $data['direccion'] ?? null,
            ]);
        });

        return response()->json($voluntario->load('usuario'), 201);
    }

    public function show($id)
    {
        $voluntario = Voluntario::with(['usuario', 'inscripciones', 'certificados', 'puntoEcologico'])
            ->findOrFail($id);

        return response()->json($voluntario);
    }

    public function update(Request $request, $id)
    {
        $voluntario = Voluntario::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'matricula_universitaria' => 'sometimes|required|string|max:20|unique:voluntarios,matricula_universitaria,' . $id . ',id_voluntario',
            'facultad' => 'sometimes|required|string|max:100',
            'carrera' => 'sometimes|required|string|max:100',
            'ciclo' => 'nullable|string|max:10',
            'horas_acumuladas' => 'nullable|numeric|min:0',
            'fecha_graduacion' => 'nullable|date',
            'disponibilidad' => 'in:disponible,ocupado,no_disponible',
            'habilidades' => 'nullable|string',
            'fecha_nacimiento' => 'nullable|date',
            'direccion' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $voluntario->update($validator->validated());

        return response()->json($voluntario);
    }

    public function destroy($id)
    {
        $voluntario = Voluntario::findOrFail($id);
        $voluntario->delete();

        return response()->json(['message' => 'Voluntario eliminado correctamente']);
    }
}
