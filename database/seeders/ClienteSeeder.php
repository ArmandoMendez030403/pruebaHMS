<?php

namespace Database\Seeders;

use App\Models\Cliente;
use Illuminate\Database\Seeder;

class ClienteSeeder extends Seeder
{
    public function run(): void
    {
        $clientes = [
            [
                'nombre' => 'Construcciones del Norte S.A. de C.V.',
                'rfc' => 'CNO010101AAA',
                'direccion' => 'Av. Industrial 1200, Monterrey, N.L.',
                'correo' => 'compras@construccionesnorte.test',
                'telefono' => '8180001001',
                'fecha_registro' => '2026-01-10 08:00:00',
            ],
            [
                'nombre' => 'Papelería Central del Bajío',
                'rfc' => 'PCB020202BBB',
                'direccion' => 'Calle Hidalgo 245, León, Gto.',
                'correo' => 'facturacion@papeleriacentral.test',
                'telefono' => '4770002002',
                'fecha_registro' => '2026-02-14 09:15:00',
            ],
            [
                'nombre' => 'Servicios Integrales del Pacífico',
                'rfc' => 'SIP030303CCC',
                'direccion' => 'Blvd. Costero 88, Mazatlán, Sin.',
                'correo' => 'cobranza@integralespacifico.test',
                'telefono' => '6690003003',
                'fecha_registro' => '2026-03-03 10:30:00',
            ],
            [
                'nombre' => 'Distribuidora Alfa del Centro',
                'rfc' => 'DAC040404DDD',
                'direccion' => 'Av. Reforma 500, Ciudad de México',
                'correo' => 'administracion@distribuidoralfa.test',
                'telefono' => '5510004004',
                'fecha_registro' => '2026-03-28 11:45:00',
            ],
            [
                'nombre' => 'Comercializadora Épsilon',
                'rfc' => 'CEM050505EEE',
                'direccion' => 'Calle 5 de Mayo 77, Puebla, Pue.',
                'correo' => 'pagos@comercializadoraepsilon.test',
                'telefono' => '2220005005',
                'fecha_registro' => '2026-04-18 12:00:00',
            ],
            [
                'nombre' => 'Mantenimiento y Obra Fina',
                'rfc' => 'MOF060606FFF',
                'direccion' => 'Periférico Sur 950, Guadalajara, Jal.',
                'correo' => 'ventas@mantenimientoobrafina.test',
                'telefono' => '3330006006',
                'fecha_registro' => '2026-05-02 13:10:00',
            ],
        ];

        foreach ($clientes as $cliente) {
            Cliente::updateOrCreate(
                ['rfc' => $cliente['rfc']],
                $cliente
            );
        }
    }
}

