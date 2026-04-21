<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PedidoComidaUsuario extends Model
{
    protected $table = 'pedido_comida_usuario';
    protected $fillable = ['usuario_id', 'pedido_id', 'comida_id', 'cantidad', 'n_mesas'];
    public $timestamps = false;

    // Relaciones
    public function usuario()
    {
        return $this->belongsTo(UsuarioModelo::class, 'usuario_id');
    }

    public function comida()
    {
        return $this->belongsTo(Comidas::class, 'comida_id');
    }
}