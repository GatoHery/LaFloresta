<?php

namespace App\Http\Controllers;

use App\Models\Comidas;
use Illuminate\Http\Request;

class ComidasController extends Controller
{
    // GET /api/comidas
    public function index()
    {
        return Comidas::all();
    }

    // GET /api/comidas/{id}
    public function show(string $id)
    {
        $comida = Comidas::find($id);
        if (!$comida) {
            return response()->json(['error' => 'Comida no encontrada'], 404);
        }
        return $comida;
    }

    // POST /api/comidas
    public function store(Request $request)
    {
        $comida = Comidas::create($request->all());
        return response()->json($comida, 201);
    }

    // PUT /api/comidas/{id}
    public function update(Request $request, string $id)
    {
        $comida = Comidas::find($id);
        if (!$comida) {
            return response()->json(['error' => 'Comida no encontrada'], 404);
        }
        $comida->update($request->all());
        return $comida;
    }

    // DELETE /api/comidas/{id}
    public function destroy(string $id)
    {
        $comida = Comidas::find($id);
        if (!$comida) {
            return response()->json(['error' => 'Comida no encontrada'], 404);
        }
        $comida->delete();
        return response()->json(null, 204);
    }
}
