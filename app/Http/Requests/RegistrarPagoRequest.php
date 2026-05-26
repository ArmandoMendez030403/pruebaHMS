<?php

namespace App\Http\Requests;

class RegistrarPagoRequest extends ApiFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'fecha_pago' => ['nullable', 'date'],
            'monto' => ['required', 'numeric', 'min:0.01'],
            'forma_pago' => ['required', 'string', 'max:50'],
            'referencia' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function attributes(): array
    {
        return [
            'fecha_pago' => 'fecha de pago',
            'monto' => 'monto',
            'forma_pago' => 'forma de pago',
            'referencia' => 'referencia',
        ];
    }
}

