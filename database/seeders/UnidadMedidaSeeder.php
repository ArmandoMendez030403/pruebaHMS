<?php

namespace Database\Seeders;

use App\Models\UnidadMedida;
use Illuminate\Database\Seeder;

class UnidadMedidaSeeder extends Seeder
{
    public function run(): void
    {
        $unidades = [
            ['nombre' => 'Pieza', 'abreviacion' => 'Pza'],
            ['nombre' => 'Kilogramo', 'abreviacion' => 'Kg'],
            ['nombre' => 'Gramo', 'abreviacion' => 'g'],
            ['nombre' => 'Litro', 'abreviacion' => 'L'],
            ['nombre' => 'Mililitro', 'abreviacion' => 'ml'],
            ['nombre' => 'Metro', 'abreviacion' => 'm'],
            ['nombre' => 'Centímetro', 'abreviacion' => 'cm'],
            ['nombre' => 'Metro cuadrado', 'abreviacion' => 'm²'],
            ['nombre' => 'Metro cúbico', 'abreviacion' => 'm³'],
            ['nombre' => 'Hora', 'abreviacion' => 'h'],
            ['nombre' => 'Día', 'abreviacion' => 'd'],
            ['nombre' => 'Caja', 'abreviacion' => 'Cj'],
            ['nombre' => 'Paquete', 'abreviacion' => 'Pkg'],
            ['nombre' => 'Servicio', 'abreviacion' => 'Srv'],
            ['nombre' => 'Unidad', 'abreviacion' => 'Un'],
        ];

        foreach ($unidades as $unidad) {
            UnidadMedida::updateOrCreate(
                ['nombre' => $unidad['nombre']],
                $unidad
            );
        }
    }
}

