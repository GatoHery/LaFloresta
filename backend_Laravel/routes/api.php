<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\ComidasController;
use App\Http\Controllers\PedidoController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/login', [UsuarioController::class, 'login']);
Route::apiResource('usuarios', UsuarioController::class);
Route::apiResource('comidas', ComidasController::class);
Route::apiResource('pedidos', PedidoController::class);
