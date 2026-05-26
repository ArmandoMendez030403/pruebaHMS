<?php

namespace Database\Seeders;

use App\Models\DetalleFactura;
use App\Models\Factura;
use App\Models\Producto;
use Illuminate\Database\Seeder;

class DetalleFacturaSeeder extends Seeder
{
    public function run(): void
    {
        $facturas = [
            [
                'folio' => 'F-2026-0001',
                'detalles' => [
                    ['codigo' => 'PROD-003', 'cantidad' => 1, 'descuento' => 0],
                ],
            ],
            [
                'folio' => 'F-2026-0002',
                'detalles' => [
                    ['codigo' => 'PROD-004', 'cantidad' => 2, 'descuento' => 0],
                    ['codigo' => 'PROD-007', 'cantidad' => 4, 'descuento' => 0],
                ],
            ],
            [
                'folio' => 'F-2026-0003',
                'detalles' => [
                    ['codigo' => 'PROD-006', 'cantidad' => 1, 'descuento' => 0],
                ],
            ],
            [
                'folio' => 'F-2026-0004',
                'detalles' => [
                    ['codigo' => 'PROD-002', 'cantidad' => 2, 'descuento' => 0],
                    ['codigo' => 'PROD-008', 'cantidad' => 3, 'descuento' => 0],
                ],
            ],
            [
                'folio' => 'F-2026-0005',
                'detalles' => [
                    ['codigo' => 'PROD-005', 'cantidad' => 5, 'descuento' => 0],
                ],
            ],
        ];

        foreach ($facturas as $facturaData) {
            $factura = Factura::where('folio', $facturaData['folio'])->firstOrFail();

            foreach ($facturaData['detalles'] as $detalleData) {
                $producto = Producto::where('codigo', $detalleData['codigo'])->firstOrFail();
                $cantidad = (float) $detalleData['cantidad'];
                $descuento = (float) ($detalleData['descuento'] ?? 0);
                $precioUnitario = (float) $producto->precio_unitario;
                $subtotal = round(($cantidad * $precioUnitario) - $descuento, 2);

                DetalleFactura::updateOrCreate(
                    [
                        'factura_id' => $factura->id,
                        'producto_id' => $producto->id,
                    ],
                    [
                        'cantidad' => $cantidad,
                        'precio_unitario' => $precioUnitario,
                        'descuento' => $descuento,
                        'subtotal' => $subtotal,
                        'porcentaje_iva' => (float) $producto->porcentaje_iva,
                    ]
                );
            }

            $factura->refresh();
            $factura->recalcularTotales();
            $factura->save();
        }
    }
}

