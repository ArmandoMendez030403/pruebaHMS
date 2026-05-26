<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\DetalleFactura;
use App\Models\Factura;
use App\Models\Pago;
use App\Models\Producto;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\JsonResponse;

class ApiController extends Controller
{
    protected function responderExito(mixed $datos, string $mensaje, int $codigo = 200): JsonResponse
    {
        return response()->json([
            'exito' => true,
            'datos' => $datos,
            'mensaje' => $mensaje,
        ], $codigo);
    }

    protected function responderError(string $mensaje, int $codigo = 400, mixed $errores = null): JsonResponse
    {
        $respuesta = [
            'exito' => false,
            'datos' => null,
            'mensaje' => $mensaje,
        ];

        if ($errores !== null) {
            $respuesta['errores'] = $errores;
        }

        return response()->json($respuesta, $codigo);
    }

    protected function responderSinContenido(string $mensaje): JsonResponse
    {
        return response()->json([
            'exito' => true,
            'datos' => null,
            'mensaje' => $mensaje,
        ], 204);
    }

    protected function responderNoEncontrado(string $mensaje = 'Recurso no encontrado.'): JsonResponse
    {
        return $this->responderError($mensaje, 404);
    }

    protected function formatearPaginacion(LengthAwarePaginator $paginador, callable $formateador): array
    {
        return [
            'registros' => array_map($formateador, $paginador->items()),
            'paginacion' => [
                'pagina_actual' => $paginador->currentPage(),
                'ultima_pagina' => $paginador->lastPage(),
                'por_pagina' => $paginador->perPage(),
                'total' => $paginador->total(),
            ],
        ];
    }

    protected function formatearCliente(Cliente $cliente): array
    {
        return [
            'id' => $cliente->id,
            'nombre' => $cliente->nombre,
            'rfc' => $cliente->rfc,
            'direccion' => $cliente->direccion,
            'correo' => $cliente->correo,
            'telefono' => $cliente->telefono,
            'fecha_registro' => $cliente->fecha_registro?->toDateTimeString(),
            'facturas_total' => $cliente->facturas_count ?? null,
            'creado_en' => $cliente->created_at?->toDateTimeString(),
            'actualizado_en' => $cliente->updated_at?->toDateTimeString(),
        ];
    }

    protected function formatearProducto(Producto $producto): array
    {
        $producto->loadMissing('unidadMedida');

        return [
            'id' => $producto->id,
            'codigo' => $producto->codigo,
            'descripcion' => $producto->descripcion,
            'precio_unitario' => $producto->precio_unitario,
            'unidad_medida' => $producto->unidadMedida ? [
                'id' => $producto->unidadMedida->id,
                'nombre' => $producto->unidadMedida->nombre,
                'abreviacion' => $producto->unidadMedida->abreviacion,
            ] : null,
            'porcentaje_iva' => $producto->porcentaje_iva,
            'creado_en' => $producto->created_at?->toDateTimeString(),
            'actualizado_en' => $producto->updated_at?->toDateTimeString(),
        ];
    }

    protected function formatearDetalleFactura(DetalleFactura $detalle): array
    {
        $detalle->loadMissing('producto');

        return [
            'id' => $detalle->id,
            'factura_id' => $detalle->factura_id,
            'producto_id' => $detalle->producto_id,
            'producto' => $detalle->producto ? $this->formatearProducto($detalle->producto) : null,
            'cantidad' => $detalle->cantidad,
            'precio_unitario' => $detalle->precio_unitario,
            'descuento' => $detalle->descuento,
            'subtotal' => $detalle->subtotal,
            'porcentaje_iva' => $detalle->porcentaje_iva,
        ];
    }

    protected function formatearPago(Pago $pago): array
    {
        return [
            'id' => $pago->id,
            'factura_id' => $pago->factura_id,
            'fecha_pago' => $pago->fecha_pago?->toDateString(),
            'monto' => $pago->monto,
            'forma_pago' => $pago->forma_pago,
            'referencia' => $pago->referencia,
            'creado_en' => $pago->created_at?->toDateTimeString(),
            'actualizado_en' => $pago->updated_at?->toDateTimeString(),
        ];
    }

    protected function formatearFactura(Factura $factura): array
    {
        $factura->loadMissing(['cliente', 'detallesFactura.producto', 'pagos']);

        return [
            'id' => $factura->id,
            'folio' => $factura->folio,
            'cliente' => $factura->cliente ? $this->formatearCliente($factura->cliente) : null,
            'fecha_emision' => $factura->fecha_emision?->toDateString(),
            'fecha_vencimiento' => $factura->fecha_vencimiento?->toDateString(),
            'subtotal' => $factura->subtotal,
            'iva' => $factura->iva,
            'total' => $factura->total,
            'saldo_pagado' => number_format($factura->saldoPagado(), 2, '.', ''),
            'saldo_pendiente' => number_format($factura->saldoPendiente(), 2, '.', ''),
            'estatus' => $factura->estatus,
            'metodo_pago' => $factura->metodo_pago,
            'detalles' => $factura->detallesFactura->map(fn (DetalleFactura $detalle): array => $this->formatearDetalleFactura($detalle))->values()->all(),
            'pagos' => $factura->pagos->map(fn (Pago $pago): array => $this->formatearPago($pago))->values()->all(),
            'creado_en' => $factura->created_at?->toDateTimeString(),
            'actualizado_en' => $factura->updated_at?->toDateTimeString(),
        ];
    }
}


