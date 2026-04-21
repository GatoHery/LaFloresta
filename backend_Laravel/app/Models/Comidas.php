<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comidas extends Model
{
    protected $table = 'comidas';
    protected $fillable = ['nombre', 'precio', 'tipo'];
    public $timestamps = false;
    protected $casts = [
        'nombre' => 'string',
        'precio' => 'decimal:2',
        'tipo' => 'string',
    ];
}
