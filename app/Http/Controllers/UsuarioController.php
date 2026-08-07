<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UsuarioController extends Controller
{
    public function index(Request $request)
    {
        $query = Usuario::query();

        if ($request->filled('rol')) {
            $query->where('rol', $request->rol);
        }
        if ($request->filled('activo')) {
            $query->where('activo', $request->boolean('activo'));
        }
        if ($request->filled('buscar')) {
            $q = $request->buscar;
            $query->where(function ($sub) use ($q) {
                $sub->where('nombres', 'like', "%{$q}%")
                    ->orWhere('apellidos', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%")
                    ->orWhere('dni', 'like', "%{$q}%");
            });
        }

        $usuarios = $query->orderBy('id_usuario', 'desc')->paginate(15);

        return response()->json($usuarios);
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
            'rol' => 'required|in:voluntario,organizador,administrador',
            'activo' => 'boolean',
            'foto_perfil' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();
        $data['password_hash'] = Hash::make($data['password']);
        unset($data['password']);
        $data['fecha_registro'] = now();

        $usuario = Usuario::create($data);

        return response()->json($usuario, 201);
    }

    public function show($id)
    {
        $usuario = Usuario::with(['voluntario', 'organizador'])->findOrFail($id);
        return response()->json($usuario);
    }

    public function update(Request $request, $id)
    {
        $usuario = Usuario::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'email' => 'sometimes|required|email|max:100|unique:usuarios,email,' . $id . ',id_usuario',
            'password' => 'nullable|string|min:8',
            'nombres' => 'sometimes|required|string|max:60',
            'apellidos' => 'sometimes|required|string|max:60',
            'dni' => 'sometimes|required|string|size:8|unique:usuarios,dni,' . $id . ',id_usuario',
            'telefono' => 'nullable|string|max:15',
            'rol' => 'sometimes|required|in:voluntario,organizador,administrador',
            'activo' => 'boolean',
            'foto_perfil' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();
        if (!empty($data['password'])) {
            $data['password_hash'] = Hash::make($data['password']);
        }
        unset($data['password']);

        $usuario->update($data);

        return response()->json($usuario);
    }

    public function destroy($id)
    {
        $usuario = Usuario::findOrFail($id);
        $usuario->delete();

        return response()->json(['message' => 'Usuario eliminado correctamente']);
    }
}
