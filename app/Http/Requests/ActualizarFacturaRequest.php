<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class ActualizarFacturaRequest extends ApiFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $facturaId = $this->route('facturaId');

        return [
            'cliente_id' => ['required', 'integer', 'exists:clientes,id'],
            'folio' => ['required', 'string', 'max:50', Rule::unique('facturas', 'folio')->ignore($facturaId)],
            'fecha_emision' => ['required', 'date'],
            'fecha_vencimiento' => ['required', 'date', 'after_or_equal:fecha_emision'],
            'metodo_pago' => ['required', 'string', 'max:50'],
            'detalles' => ['nullable', 'array', 'min:1'],
            'detalles.*.producto_id' => ['required_with:detalles', 'integer', 'exists:productos,id'],
            'detalles.*.cantidad' => ['required_with:detalles', 'numeric', 'min:0.01'],
            'detalles.*.precio_unitario' => ['required_with:detalles', 'numeric', 'min:0'],
            'detalles.*.descuento' => ['nullable', 'numeric', 'min:0'],
        ];
    }

    public function attributes(): array
    {
        return [
            'cliente_id' => 'cliente',
            'folio' => 'folio',
            'fecha_emision' => 'fecha de emisión',
            'fecha_vencimiento' => 'fecha de vencimiento',
            'metodo_pago' => 'método de pago',
            'detalles' => 'detalles',
            'detalles.*.producto_id' => 'producto',
            'detalles.*.cantidad' => 'cantidad',
            'detalles.*.precio_unitario' => 'precio unitario',
            'detalles.*.descuento' => 'descuento',
        ];
    }
}

