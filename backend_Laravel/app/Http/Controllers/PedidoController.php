<?php

namespace App\Http\Controllers;

use App\Models\PedidoModelo;
use App\Models\PedidoComidaUsuarioModelo;
use App\Models\UsuarioModelo;
use App\Models\Comidas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PedidoController extends Controller
{
    public function index(Request $request)
    {
        $usuarioId = $request->query('usuario_id');
        $query = PedidoModelo::with(['detalles.usuario', 'detalles.comida']);

        if ($usuarioId) {
            $query->whereHas('detalles', function ($q) use ($usuarioId) {
                $q->where('usuario_id', $usuarioId);
            });
        }

        $pedidos = $query->orderBy('id', 'desc')->get(); // Ordenado desc por defecto

        return $pedidos->map(function ($pedido) {
            // Sacamos n_mesas del primer detalle para mostrarlo en la raíz
            $primeraMesa = $pedido->detalles->first()->n_mesas ?? 'N/A';

            return [
                'id' => $pedido->id,
                'fecha' => $pedido->fecha,
                'total' => $pedido->total,
                'n_mesas' => $primeraMesa, // <--- IMPORTANTE PARA ANDROID
                'comidas' => $pedido->detalles->map(function ($detalle) {
                    return [
                        'usuario_id' => $detalle->usuario_id,
                        'usuario_nombre' => $detalle->usuario->nombre ?? 'N/A',
                        'comida_id' => $detalle->comida_id,
                        'nombre' => $detalle->comida->nombre ?? 'Eliminado',
                        'precio' => $detalle->comida->precio ?? 0,
                        'cantidad' => $detalle->cantidad
                    ];
                })->toArray()
            ];
        });
    }

    public function store(Request $request)
    {
        $datos = $request->validate([
            'fecha' => 'required|date',
            'total' => 'required|numeric',
            'n_mesas' => 'required|string',
            'comidas' => 'required|array|min:1'
        ]);

        return DB::transaction(function () use ($datos) {
            try {
                // 1. Crear el pedido
                $pedido = PedidoModelo::create([
                    'fecha' => $datos['fecha'],
                    'total' => $datos['total']
                ]);

                $detalles = [];
                foreach ($datos['comidas'] as $item) {
                    // Validaciones
                    $usuario = UsuarioModelo::find($item['usuario_id']);
                    $comida = Comidas::find($item['comida_id']);

                    if (!$usuario || !$comida) {
                        throw new \Exception("Usuario o Comida no encontrados");
                    }

                    // 2. Insertar en tabla pivot (Aquí guardas la mesa)
                    $detalle = PedidoComidaUsuarioModelo::create([
                        'usuario_id' => $item['usuario_id'],
                        'pedido_id'  => $pedido->id,
                        'comida_id'  => $item['comida_id'],
                        'cantidad'   => $item['cantidad'],
                        'n_mesas'    => $datos['n_mesas'] // Se guarda por item pero se lee del pedido
                    ]);

                    $detalles[] = [
                        'comida_id' => $detalle->comida_id,
                        'nombre' => $comida->nombre,
                        'precio' => $comida->precio,
                        'cantidad' => $detalle->cantidad
                    ];
                }

                return response()->json([
                    'id' => $pedido->id,
                    'fecha' => $pedido->fecha,
                    'total' => $pedido->total,
                    'n_mesas' => $datos['n_mesas'], // <--- VOLVER A ENVIAR A ANDROID
                    'comidas' => $detalles
                ], 201);

            } catch (\Exception $e) {
                return response()->json(['error' => $e->getMessage()], 500);
            }
        });
    }
    
    // GET /api/pedidos/{id}
    public function show(string $id)
    {
        $pedido = PedidoModelo::with(['detalles.usuario', 'detalles.comida'])->find($id);

        if (!$pedido) {
            return response()->json(['error' => 'Pedido no encontrado'], 404);
        }

        $primeraMesa = $pedido->detalles->first()->n_mesas ?? 'N/A';

        return [
            'id' => $pedido->id,
            'fecha' => $pedido->fecha,
            'total' => $pedido->total,
            'n_mesas' => $primeraMesa,
            'comidas' => $pedido->detalles->map(function ($detalle) {
                return [
                    'usuario_id' => $detalle->usuario_id,
                    'usuario_nombre' => $detalle->usuario->nombre ?? 'N/A',
                    'comida_id' => $detalle->comida_id,
                    'nombre' => $detalle->comida->nombre ?? 'Eliminado',
                    'precio' => $detalle->comida->precio ?? 0,
                    'cantidad' => $detalle->cantidad
                ];
            })->toArray()
        ];
    }

    // PUT /api/pedidos/{id}
    public function update(Request $request, string $id)
    {
        $datos = $request->validate([
            'fecha' => 'required|date',
            'total' => 'required|numeric',
            'n_mesas' => 'required|string',
            'comidas' => 'required|array|min:1'
        ]);

        return DB::transaction(function () use ($datos, $id) {
            try {
                $pedido = PedidoModelo::find($id);
                if (!$pedido) {
                    return response()->json(['error' => 'Pedido no encontrado'], 404);
                }

                $pedido->update([
                    'fecha' => $datos['fecha'],
                    'total' => $datos['total']
                ]);

                // Eliminar comidas anteriores
                PedidoComidaUsuarioModelo::where('pedido_id', $id)->delete();

                $detalles = [];
                foreach ($datos['comidas'] as $item) {
                    $usuario = UsuarioModelo::find($item['usuario_id']);
                    $comida = Comidas::find($item['comida_id']);

                    if (!$usuario || !$comida) {
                        throw new \Exception("Usuario o Comida no encontrados");
                    }

                    $detalle = PedidoComidaUsuarioModelo::create([
                        'usuario_id' => $item['usuario_id'],
                        'pedido_id'  => $pedido->id,
                        'comida_id'  => $item['comida_id'],
                        'cantidad'   => $item['cantidad'],
                        'n_mesas'    => $datos['n_mesas']
                    ]);

                    $detalles[] = [
                        'comida_id' => $detalle->comida_id,
                        'nombre' => $comida->nombre,
                        'precio' => $comida->precio,
                        'cantidad' => $detalle->cantidad
                    ];
                }

                return response()->json([
                    'id' => $pedido->id,
                    'fecha' => $pedido->fecha,
                    'total' => $pedido->total,
                    'n_mesas' => $datos['n_mesas'],
                    'comidas' => $detalles
                ]);

            } catch (\Exception $e) {
                return response()->json(['error' => $e->getMessage()], 500);
            }
        });
    }

    // DELETE /api/pedidos/{id}
    public function destroy(string $id)
    {
        $pedido = PedidoModelo::find($id);
        if (!$pedido) {
            return response()->json(['error' => 'Pedido no encontrado'], 404);
        }

        $pedido->delete();
        return response()->json(null, 204);
    }
}