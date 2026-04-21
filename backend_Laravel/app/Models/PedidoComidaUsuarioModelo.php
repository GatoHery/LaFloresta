<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PedidoComidaUsuarioModelo extends Model
{
    // Nombre de la tabla en tu base de datos
    protected $table = 'pedido_comida_usuario';

    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int'; 

    // Campos que permitimos guardar desde el controlador
    protected $fillable = [
        'pedido_id', 
        'comida_id', 
        'usuario_id', 
        'cantidad', 
        'n_mesas' // <--- Importante: lo necesitas para tu controlador
    ];

    // Desactivamos timestamps si tu migración no tiene created_at/updated_at
    public $timestamps = false;

    // Relación con el Pedido
    public function pedido()
    {
        return $this->belongsTo(PedidoModelo::class, 'pedido_id');
    }

    // Relación con la Comida (Se llama Comidas según tu sidebar)
    public function comida()
    {
        return $this->belongsTo(Comidas::class, 'comida_id');
    }

    // Relación con el Usuario
    public function usuario()
    {
        return $this->belongsTo(UsuarioModelo::class, 'usuario_id');
    }
}