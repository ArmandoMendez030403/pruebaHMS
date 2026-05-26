<?php

namespace Database\Seeders;

use App\Models\Producto;
use App\Models\UnidadMedida;
use Illuminate\Database\Seeder;

class ProductoSeeder extends Seeder
{
    public function run(): void
    {
        $productos = [
            [
                'codigo' => 'PROD-001',
                'descripcion' => 'Caja de tornillos galvanizados',
                'precio_unitario' => 120.00,
                'unidad' => 'Caja',
                'porcentaje_iva' => 16.00,
            ],
            [
                'codigo' => 'PROD-002',
                'descripcion' => 'Martillo profesional',
                'precio_unitario' => 350.00,
                'unidad' => 'Pieza',
                'porcentaje_iva' => 16.00,
            ],
            [
                'codigo' => 'PROD-003',
                'descripcion' => 'Taladro inalámbrico',
                'precio_unitario' => 890.00,
                'unidad' => 'Pieza',
                'porcentaje_iva' => 16.00,
            ],
            [
                'codigo' => 'PROD-004',
                'descripcion' => 'Galón de pintura blanca',
                'precio_unitario' => 220.00,
                'unidad' => 'Litro',
                'porcentaje_iva' => 16.00,
            ],
            [
                'codigo' => 'PROD-005',
                'descripcion' => 'Cable eléctrico 10 metros',
                'precio_unitario' => 95.00,
                'unidad' => 'Metro',
                'porcentaje_iva' => 16.00,
            ],
            [
                'codigo' => 'PROD-006',
                'descripcion' => 'Servicio de instalación básica',
                'precio_unitario' => 650.00,
                'unidad' => 'Servicio',
                'porcentaje_iva' => 16.00,
            ],
            [
                'codigo' => 'PROD-007',
                'descripcion' => 'Paquete de lijas industriales',
                'precio_unitario' => 45.00,
                'unidad' => 'Paquete',
                'porcentaje_iva' => 16.00,
            ],
            [
                'codigo' => 'PROD-008',
                'descripcion' => 'Caja organizadora multiusos',
                'precio_unitario' => 180.00,
                'unidad' => 'Caja',
                'porcentaje_iva' => 16.00,
            ],
        ];

        foreach ($productos as $producto) {
            $unidad = UnidadMedida::where('nombre', $producto['unidad'])->firstOrFail();

            Producto::updateOrCreate(
                ['codigo' => $producto['codigo']],
                [
                    'descripcion' => $producto['descripcion'],
                    'precio_unitario' => $producto['precio_unitario'],
                    'unidad_medida_id' => $unidad->id,
                    'porcentaje_iva' => $producto['porcentaje_iva'],
                ]
            );
        }
    }
}

