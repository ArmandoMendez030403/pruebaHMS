<?php

namespace Database\Seeders;

use App\Models\Cliente;
use App\Models\Factura;
use Illuminate\Database\Seeder;

class FacturaSeeder extends Seeder
{
    public function run(): void
    {
        $facturas = [
            [
                'folio' => 'F-2026-0001',
                'cliente_rfc' => 'CNO010101AAA',
                'fecha_emision' => '2026-05-03',
                'fecha_vencimiento' => '2026-05-18',
                'estatus' => 'pendiente',
                'metodo_pago' => 'Transferencia',
            ],
            [
                'folio' => 'F-2026-0002',
                'cliente_rfc' => 'PCB020202BBB',
                'fecha_emision' => '2026-05-12',
                'fecha_vencimiento' => '2026-06-05',
                'estatus' => 'pendiente',
                'metodo_pago' => 'Efectivo',
            ],
            [
                'folio' => 'F-2026-0003',
                'cliente_rfc' => 'SIP030303CCC',
                'fecha_emision' => '2026-04-01',
                'fecha_vencimiento' => '2026-04-15',
                'estatus' => 'pendiente',
                'metodo_pago' => 'Tarjeta',
            ],
            [
                'folio' => 'F-2026-0004',
                'cliente_rfc' => 'DAC040404DDD',
                'fecha_emision' => '2026-03-20',
                'fecha_vencimiento' => '2026-04-19',
                'estatus' => 'pendiente',
                'metodo_pago' => 'Transferencia',
            ],
            [
                'folio' => 'F-2026-0005',
                'cliente_rfc' => 'CEM050505EEE',
                'fecha_emision' => '2026-05-20',
                'fecha_vencimiento' => '2026-06-20',
                'estatus' => 'pendiente',
                'metodo_pago' => 'Efectivo',
            ],
        ];

        foreach ($facturas as $factura) {
            $cliente = Cliente::where('rfc', $factura['cliente_rfc'])->firstOrFail();

            Factura::updateOrCreate(
                ['folio' => $factura['folio']],
                [
                    'cliente_id' => $cliente->id,
                    'fecha_emision' => $factura['fecha_emision'],
                    'fecha_vencimiento' => $factura['fecha_vencimiento'],
                    'subtotal' => 0,
                    'iva' => 0,
                    'total' => 0,
                    'estatus' => $factura['estatus'],
                    'metodo_pago' => $factura['metodo_pago'],
                ]
            );
        }
    }
}

