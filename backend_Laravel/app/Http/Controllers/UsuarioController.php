<?php

namespace App\Http\Controllers;

use App\Models\UsuarioModelo;
use Illuminate\Http\Request;

class UsuarioController extends Controller
{
    // GET /api/usuarios
    public function index()
    {
        return UsuarioModelo::all();
    }

    // POST /api/usuarios
    public function store(Request $request)
    {
        $usuario = UsuarioModelo::create($request->all());
        return response()->json($usuario, 201);
    }

    // GET /api/usuarios/{id}
    public function show(string $id)
    {
        $usuario = UsuarioModelo::find($id);
        if (!$usuario) {
            return response()->json(['error' => 'Usuario no encontrado'], 404);
        }
        return $usuario;
    }

    // PUT /api/usuarios/{id}
    public function update(Request $request, string $id)
    {
        $usuario = UsuarioModelo::find($id);
        if (!$usuario) {
            return response()->json(['error' => 'Usuario no encontrado'], 404);
        }
        $usuario->update($request->all());
        return $usuario;
    }

    // DELETE /api/usuarios/{id}
    public function destroy(string $id)
    {
        $usuario = UsuarioModelo::find($id);
        if (!$usuario) {
            return response()->json(['error' => 'Usuario no encontrado'], 404);
        }
        $usuario->delete();
        return response()->json(null, 204);
    }

    // GET /api/login?nombre=x&contrasena=y
    public function login(Request $request)
    {
        $nombre = $request->query('nombre');
        $contrasena = $request->query('contrasena');

        $usuario = UsuarioModelo::where('nombre', $nombre)
                                 ->where('contrasena', $contrasena)
                                 ->first();

        if (!$usuario) {
            return response()->json(['error' => 'Usuario o contraseña incorrectos'], 401);
        }

        return response()->json($usuario->only(['id', 'nombre', 'rol']));
    }
}
