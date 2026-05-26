<?php

use App\Http\Controllers\ClienteController;
use App\Http\Controllers\FacturaController;
use App\Http\Controllers\PagoController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\UnidadMedidaController;
use Illuminate\Support\Facades\Route;

Route::get('unidades-medida', [UnidadMedidaController::class, 'index']);
Route::get('unidades-medida/{unidadMedidaId}', [UnidadMedidaController::class, 'show']);

Route::get('clientes', [ClienteController::class, 'index']);
Route::post('clientes', [ClienteController::class, 'store']);
Route::get('clientes/{clienteId}', [ClienteController::class, 'show']);
Route::put('clientes/{clienteId}', [ClienteController::class, 'update']);
Route::delete('clientes/{clienteId}', [ClienteController::class, 'destroy']);

Route::get('productos', [ProductoController::class, 'index']);
Route::post('productos', [ProductoController::class, 'store']);
Route::get('productos/{productoId}', [ProductoController::class, 'show']);
Route::put('productos/{productoId}', [ProductoController::class, 'update']);
Route::delete('productos/{productoId}', [ProductoController::class, 'destroy']);

Route::get('facturas', [FacturaController::class, 'index']);
Route::post('facturas', [FacturaController::class, 'store']);
Route::get('facturas/{facturaId}', [FacturaController::class, 'show']);
Route::put('facturas/{facturaId}', [FacturaController::class, 'update']);
Route::patch('facturas/{facturaId}/cancelar', [FacturaController::class, 'cancelar']);
Route::delete('facturas/{facturaId}', [FacturaController::class, 'destroy']);

Route::get('pagos', [PagoController::class, 'index']);
Route::post('facturas/{facturaId}/pagos', [PagoController::class, 'store']);
Route::get('pagos/{pagoId}', [PagoController::class, 'show']);
Route::delete('pagos/{pagoId}', [PagoController::class, 'destroy']);


