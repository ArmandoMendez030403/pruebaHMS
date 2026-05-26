<?php

namespace App\Http\Controllers;

use App\Http\Requests\ActualizarFacturaRequest;
use App\Http\Requests\GuardarFacturaRequest;
use App\Models\DetalleFactura;
use App\Models\Factura;
use App\Models\Producto;
use DomainException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class FacturaController extends ApiController
{
    public function index(Request $request)
    {
        try {
            $facturas = Factura::query()
                ->with(['cliente', 'detallesFactura.producto.unidadMedida', 'pagos'])
                ->when($request->filled('cliente_id'), function ($consulta) use ($request): void {
                    $consulta->where('cliente_id', $request->integer('cliente_id'));
                })
                ->when($request->filled('estatus'), function ($consulta) use ($request): void {
                    $consulta->where('estatus', $request->input('estatus'));
                })
                ->buscar($request->input('buscar'))
                ->orderByDesc('fecha_emision')
                ->paginate(15);

            $facturas->getCollection()->each(fn (Factura $factura): bool => $factura->sincronizarEstatus());

            return $this->responderExito(
                $this->formatearPaginacion($facturas, fn (Factura $factura): array => $this->formatearFactura($factura)),
                'Facturas obtenidas correctamente.'
            );
        } catch (Throwable $error) {
            return $this->responderError('No fue posible obtener las facturas.', 500);
        }
    }

    public function store(GuardarFacturaRequest $request)
    {
        try {
            $factura = DB::transaction(function () use ($request): Factura {
                $datos = $request->validated();
                $factura = Factura::create([
                    'cliente_id' => $datos['cliente_id'],
                    'folio' => $datos['folio'],
                    'fecha_emision' => $datos['fecha_emision'],
                    'fecha_vencimiento' => $datos['fecha_vencimiento'],
                    'subtotal' => 0,
                    'iva' => 0,
                    'total' => 0,
                    'estatus' => 'pendiente',
                    'metodo_pago' => $datos['metodo_pago'],
                ]);

                $this->guardarDetallesFactura($factura, $datos['detalles']);

                $factura->load('detallesFactura');
                $factura->recalcularTotales();
                $factura->save();

                return $factura->fresh(['cliente', 'detallesFactura.producto.unidadMedida', 'pagos']);
            });

            return $this->responderExito(
                $this->formatearFactura($factura),
                'Factura registrada correctamente.',
                201
            );
        } catch (DomainException $error) {
            return $this->responderError($error->getMessage(), 400);
        } catch (ModelNotFoundException $error) {
            return $this->responderNoEncontrado('Alguno de los recursos relacionados no existe.');
        } catch (Throwable $error) {
            return $this->responderError('No fue posible registrar la factura.', 500);
        }
    }

    public function show(int $facturaId)
    {
        try {
            $factura = Factura::with(['cliente', 'detallesFactura.producto.unidadMedida', 'pagos'])->findOrFail($facturaId);
            $factura->sincronizarEstatus();
            $factura->refresh()->load(['cliente', 'detallesFactura.producto.unidadMedida', 'pagos']);

            return $this->responderExito(
                $this->formatearFactura($factura),
                'Factura obtenida correctamente.'
            );
        } catch (Throwable $error) {
            if ($error instanceof ModelNotFoundException) {
                return $this->responderNoEncontrado('La factura solicitada no existe.');
            }

            return $this->responderError('No fue posible obtener la factura.', 500);
        }
    }

    public function update(ActualizarFacturaRequest $request, int $facturaId)
    {
        try {
            $factura = DB::transaction(function () use ($request, $facturaId): Factura {
                $factura = Factura::with(['pagos', 'detallesFactura'])->findOrFail($facturaId);

                if ($factura->estaCancelada()) {
                    throw new DomainException('No se puede actualizar una factura cancelada.');
                }

                $datos = $request->validated();
                $factura->fill([
                    'cliente_id' => $datos['cliente_id'],
                    'folio' => $datos['folio'],
                    'fecha_emision' => $datos['fecha_emision'],
                    'fecha_vencimiento' => $datos['fecha_vencimiento'],
                    'metodo_pago' => $datos['metodo_pago'],
                ]);

                if (array_key_exists('detalles', $datos) && is_array($datos['detalles'])) {
                    $factura->detallesFactura()->delete();
                    $this->guardarDetallesFactura($factura, $datos['detalles']);
                }

                $factura->load(['detallesFactura', 'pagos']);
                $factura->recalcularTotales();

                if ($factura->saldoPagado() > (float) $factura->total) {
                    throw new DomainException('La suma de los pagos no puede ser mayor que el total de la factura.');
                }

                $factura->save();
                $factura->sincronizarEstatus();

                return $factura->fresh(['cliente', 'detallesFactura.producto.unidadMedida', 'pagos']);
            });

            return $this->responderExito(
                $this->formatearFactura($factura),
                'Factura actualizada correctamente.'
            );
        } catch (DomainException $error) {
            return $this->responderError($error->getMessage(), 400);
        } catch (ModelNotFoundException $error) {
            return $this->responderNoEncontrado('La factura solicitada no existe.');
        } catch (Throwable $error) {
            return $this->responderError('No fue posible actualizar la factura.', 500);
        }
    }

    public function destroy(int $facturaId)
    {
        try {
            $factura = Factura::with(['pagos', 'detallesFactura'])->findOrFail($facturaId);

            if ($factura->pagos()->exists()) {
                return $this->responderError('No se puede eliminar la factura porque ya tiene pagos registrados.', 400);
            }

            $factura->detallesFactura()->delete();
            $factura->delete();

            return $this->responderSinContenido('Factura eliminada correctamente.');
        } catch (Throwable $error) {
            if ($error instanceof ModelNotFoundException) {
                return $this->responderNoEncontrado('La factura solicitada no existe.');
            }

            return $this->responderError('No fue posible eliminar la factura.', 500);
        }
    }

    public function cancelar(int $facturaId)
    {
        try {
            $factura = Factura::with('pagos')->findOrFail($facturaId);

            if ($factura->pagos()->exists()) {
                return $this->responderError('No se puede cancelar la factura porque ya tiene pagos registrados.', 400);
            }

            $factura->estatus = 'cancelada';
            $factura->save();

            return $this->responderExito(
                $this->formatearFactura($factura->fresh(['cliente', 'detallesFactura.producto.unidadMedida', 'pagos'])),
                'Factura cancelada correctamente.'
            );
        } catch (Throwable $error) {
            if ($error instanceof ModelNotFoundException) {
                return $this->responderNoEncontrado('La factura solicitada no existe.');
            }

            return $this->responderError('No fue posible cancelar la factura.', 500);
        }
    }

    protected function guardarDetallesFactura(Factura $factura, array $detalles): void
    {
        foreach ($detalles as $detalle) {
            $producto = Producto::findOrFail($detalle['producto_id']);
            $cantidad = (float) $detalle['cantidad'];
            $precioUnitario = (float) $detalle['precio_unitario'];
            $descuento = (float) ($detalle['descuento'] ?? 0);
            $importeBruto = $cantidad * $precioUnitario;

            if ($descuento > $importeBruto) {
                throw new DomainException('El descuento de una línea no puede ser mayor que su importe bruto.');
            }

            $subtotal = round($importeBruto - $descuento, 2);

            DetalleFactura::create([
                'factura_id' => $factura->id,
                'producto_id' => $producto->id,
                'cantidad' => $cantidad,
                'precio_unitario' => $precioUnitario,
                'descuento' => $descuento,
                'subtotal' => $subtotal,
                'porcentaje_iva' => (float) $producto->porcentaje_iva,
            ]);
        }
    }
}

