<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UsuarioModelo extends Model
{
    protected $table = 'usuarios';
    protected $fillable = ['nombre', 'rol', 'contrasena'];
    public $timestamps = false;
    protected $casts = [
        'nombre' => 'string',
        'rol' => 'string',
        'contrasena' => 'string',
    ];
}
