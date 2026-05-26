<?php

namespace Database\Seeders;

use App\Models\Factura;
use App\Models\Pago;
use Illuminate\Database\Seeder;

class PagoSeeder extends Seeder
{
    public function run(): void
    {
        $pagos = [
            [
                'folio' => 'F-2026-0001',
                'movimientos' => [
                    ['referencia' => 'PAG-F-2026-0001-001', 'fecha_pago' => '2026-05-08', 'monto' => 250.00, 'forma_pago' => 'Transferencia'],
                    ['referencia' => 'PAG-F-2026-0001-002', 'fecha_pago' => '2026-05-15', 'monto' => 150.00, 'forma_pago' => 'Transferencia'],
                ],
            ],
            [
                'folio' => 'F-2026-0004',
                'movimientos' => [
                    ['referencia' => 'PAG-F-2026-0004-001', 'fecha_pago' => '2026-04-01', 'monto' => 900.00, 'forma_pago' => 'Depósito'],
                    ['referencia' => 'PAG-F-2026-0004-002', 'fecha_pago' => '2026-04-10', 'monto' => 538.40, 'forma_pago' => 'Depósito'],
                ],
            ],
        ];

        foreach ($pagos as $pagoData) {
            $factura = Factura::where('folio', $pagoData['folio'])->firstOrFail();

            foreach ($pagoData['movimientos'] as $movimiento) {
                Pago::updateOrCreate(
                    [
                        'factura_id' => $factura->id,
                        'referencia' => $movimiento['referencia'],
                    ],
                    [
                        'fecha_pago' => $movimiento['fecha_pago'],
                        'monto' => $movimiento['monto'],
                        'forma_pago' => $movimiento['forma_pago'],
                    ]
                );
            }
        }

        Factura::query()->get()->each(function (Factura $factura): void {
            $factura->sincronizarEstatus();
        });
    }
}

