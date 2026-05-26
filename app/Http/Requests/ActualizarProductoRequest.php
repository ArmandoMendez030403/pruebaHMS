<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class ActualizarProductoRequest extends ApiFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $productoId = $this->route('productoId');

        return [
            'codigo' => ['required', 'string', 'max:50', Rule::unique('productos', 'codigo')->ignore($productoId)],
            'descripcion' => ['required', 'string'],
            'precio_unitario' => ['required', 'numeric', 'min:0'],
            'unidad_medida_id' => ['required', 'integer', 'exists:unidades_medida,id'],
            'porcentaje_iva' => ['required', 'numeric', 'min:0', 'max:100'],
        ];
    }

    public function attributes(): array
    {
        return [
            'codigo' => 'código',
            'descripcion' => 'descripción',
            'precio_unitario' => 'precio unitario',
            'unidad_medida_id' => 'unidad de medida',
            'porcentaje_iva' => 'porcentaje de IVA',
        ];
    }
}

