<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PedidoModelo extends Model
{
    protected $table = 'pedido';
    protected $fillable = ['fecha', 'total', 'estado', 'usuario_id', 'n_mesas'];
    public $timestamps = false;

    // Relación con PedidoComidaUsuario
    public function detalles()
    {
        return $this->hasMany(PedidoComidaUsuarioModelo::class, 'pedido_id');
    }
}
