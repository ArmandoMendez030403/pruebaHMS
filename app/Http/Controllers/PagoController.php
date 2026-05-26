<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegistrarPagoRequest;
use App\Models\Factura;
use App\Models\Pago;
use DomainException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class PagoController extends ApiController
{
    public function index(Request $request)
    {
        try {
            $pagos = Pago::query()
                ->with(['factura.cliente'])
                ->when($request->filled('factura_id'), function ($consulta) use ($request): void {
                    $consulta->where('factura_id', $request->integer('factura_id'));
                })
                ->orderByDesc('fecha_pago')
                ->paginate(15);

            return $this->responderExito(
                $this->formatearPaginacion($pagos, fn (Pago $pago): array => $this->formatearPagoConFactura($pago)),
                'Pagos obtenidos correctamente.'
            );
        } catch (Throwable $error) {
            return $this->responderError('No fue posible obtener los pagos.', 500);
        }
    }

    public function store(RegistrarPagoRequest $request, int $facturaId)
    {
        try {
            $resultado = DB::transaction(function () use ($request, $facturaId): array {
                $factura = Factura::with(['pagos', 'cliente', 'detallesFactura.producto.unidadMedida'])->findOrFail($facturaId);

                if ($factura->estaCancelada()) {
                    throw new DomainException('No se puede registrar un pago en una factura cancelada.');
                }

                $datos = $request->validated();
                $monto = (float) $datos['monto'];
                $saldoPagado = $factura->saldoPagado();

                if ($saldoPagado + $monto > (float) $factura->total) {
                    throw new DomainException('La suma de los pagos no puede superar el total de la factura.');
                }

                $pago = Pago::create([
                    'factura_id' => $factura->id,
                    'fecha_pago' => $datos['fecha_pago'] ?? today(),
                    'monto' => $monto,
                    'forma_pago' => $datos['forma_pago'],
                    'referencia' => $datos['referencia'] ?? null,
                ]);

                $factura->load('pagos');
                $factura->sincronizarEstatus();

                return [
                    'pago' => $pago->fresh(),
                    'factura' => $factura->fresh(['cliente', 'detallesFactura.producto.unidadMedida', 'pagos']),
                ];
            });

            return $this->responderExito(
                [
                    'pago' => $this->formatearPagoConFactura($resultado['pago']),
                    'factura' => $this->formatearFactura($resultado['factura']),
                ],
                'Pago registrado correctamente.',
                201
            );
        } catch (DomainException $error) {
            return $this->responderError($error->getMessage(), 400);
        } catch (ModelNotFoundException $error) {
            return $this->responderNoEncontrado('La factura solicitada no existe.');
        } catch (Throwable $error) {
            return $this->responderError('No fue posible registrar el pago.', 500);
        }
    }

    public function show(int $pagoId)
    {
        try {
            $pago = Pago::with(['factura.cliente'])->findOrFail($pagoId);

            return $this->responderExito(
                $this->formatearPagoConFactura($pago),
                'Pago obtenido correctamente.'
            );
        } catch (Throwable $error) {
            if ($error instanceof ModelNotFoundException) {
                return $this->responderNoEncontrado('El pago solicitado no existe.');
            }

            return $this->responderError('No fue posible obtener el pago.', 500);
        }
    }

    public function destroy(int $pagoId)
    {
        try {
            $pago = Pago::with('factura.pagos')->findOrFail($pagoId);
            $factura = $pago->factura;

            $pago->delete();

            if ($factura !== null) {
                $factura->load('pagos');
                $factura->sincronizarEstatus();
            }

            return $this->responderSinContenido('Pago eliminado correctamente.');
        } catch (Throwable $error) {
            if ($error instanceof ModelNotFoundException) {
                return $this->responderNoEncontrado('El pago solicitado no existe.');
            }

            return $this->responderError('No fue posible eliminar el pago.', 500);
        }
    }

    protected function formatearPagoConFactura(Pago $pago): array
    {
        $pago->loadMissing(['factura.cliente']);
        $datos = $this->formatearPago($pago);

        $datos['factura'] = $pago->factura === null ? null : [
            'id' => $pago->factura->id,
            'folio' => $pago->factura->folio,
            'cliente' => $pago->factura->cliente === null ? null : [
                'id' => $pago->factura->cliente->id,
                'nombre' => $pago->factura->cliente->nombre,
                'rfc' => $pago->factura->cliente->rfc,
            ],
            'total' => $pago->factura->total,
            'estatus' => $pago->factura->estatus,
        ];

        return $datos;
    }
}

