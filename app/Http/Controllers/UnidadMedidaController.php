<?php

namespace App\Http\Controllers;

use App\Models\UnidadMedida;
use Throwable;

class UnidadMedidaController extends ApiController
{
    public function index()
    {
        try {
            $unidades = UnidadMedida::query()
                ->orderBy('nombre')
                ->get();

            return $this->responderExito(
                $unidades->map(fn (UnidadMedida $unidad): array => [
                    'id' => $unidad->id,
                    'nombre' => $unidad->nombre,
                    'abreviacion' => $unidad->abreviacion,
                ])->values()->all(),
                'Unidades de medida obtenidas correctamente.'
            );
        } catch (Throwable $error) {
            return $this->responderError('No fue posible obtener las unidades de medida.', 500);
        }
    }

    public function show(int $unidadMedidaId)
    {
        try {
            $unidad = UnidadMedida::findOrFail($unidadMedidaId);

            return $this->responderExito(
                [
                    'id' => $unidad->id,
                    'nombre' => $unidad->nombre,
                    'abreviacion' => $unidad->abreviacion,
                ],
                'Unidad de medida obtenida correctamente.'
            );
        } catch (Throwable $error) {
            if ($error instanceof \Illuminate\Database\Eloquent\ModelNotFoundException) {
                return $this->responderNoEncontrado('La unidad de medida solicitada no existe.');
            }

            return $this->responderError('No fue posible obtener la unidad de medida.', 500);
        }
    }
}

