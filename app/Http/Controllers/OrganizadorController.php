<?php

namespace App\Http\Controllers;

use App\Models\Organizador;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class OrganizadorController extends Controller
{
    public function index(Request $request)
    {
        $query = Organizador::with('usuario');

        if ($request->filled('departamento')) {
            $query->where('departamento', $request->departamento);
        }

        return response()->json($query->paginate(15));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|max:100|unique:usuarios,email',
            'password' => 'required|string|min:8',
            'nombres' => 'required|string|max:60',
            'apellidos' => 'required|string|max:60',
            'dni' => 'required|string|size:8|unique:usuarios,dni',
            'telefono' => 'nullable|string|max:15',
            'ong_nombre' => 'nullable|string|max:100',
            'telefono_emergencia' => 'nullable|string|max:15',
            'puesto' => 'nullable|string|max:50',
            'fecha_contratacion' => 'nullable|date',
            'departamento' => 'nullable|string|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();

        $organizador = DB::transaction(function () use ($data) {
            $usuario = Usuario::create([
                'email' => $data['email'],
                'password_hash' => Hash::make($data['password']),
                'nombres' => $data['nombres'],
                'apellidos' => $data['apellidos'],
                'dni' => $data['dni'],
                'telefono' => $data['telefono'] ?? null,
                'rol' => 'organizador',
                'activo' => true,
                'fecha_registro' => now(),
            ]);

            return Organizador::create([
                'id_organizador' => $usuario->id_usuario,
                'ong_nombre' => $data['ong_nombre'] ?? null,
                'telefono_emergencia' => $data['telefono_emergencia'] ?? null,
                'puesto' => $data['puesto'] ?? null,
                'fecha_contratacion' => $data['fecha_contratacion'] ?? null,
                'departamento' => $data['departamento'] ?? null,
            ]);
        });

        return response()->json($organizador->load('usuario'), 201);
    }

    public function show($id)
    {
        $organizador = Organizador::with(['usuario', 'campanas'])->findOrFail($id);
        return response()->json($organizador);
    }

    public function update(Request $request, $id)
    {
        $organizador = Organizador::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'ong_nombre' => 'nullable|string|max:100',
            'telefono_emergencia' => 'nullable|string|max:15',
            'puesto' => 'nullable|string|max:50',
            'fecha_contratacion' => 'nullable|date',
            'departamento' => 'nullable|string|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $organizador->update($validator->validated());

        return response()->json($organizador);
    }

    public function destroy($id)
    {
        $organizador = Organizador::findOrFail($id);
        $organizador->delete();

        return response()->json(['message' => 'Organizador eliminado correctamente']);
    }
}
