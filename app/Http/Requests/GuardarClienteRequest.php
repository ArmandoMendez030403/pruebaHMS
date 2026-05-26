<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class GuardarClienteRequest extends ApiFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre' => ['required', 'string', 'max:255'],
            'rfc' => ['required', 'string', 'max:13', Rule::unique('clientes', 'rfc')],
            'direccion' => ['required', 'string'],
            'correo' => ['required', 'email', Rule::unique('clientes', 'correo')],
            'telefono' => ['nullable', 'string', 'max:20'],
            'fecha_registro' => ['nullable', 'date'],
        ];
    }

    public function attributes(): array
    {
        return [
            'nombre' => 'nombre',
            'rfc' => 'RFC',
            'direccion' => 'dirección',
            'correo' => 'correo',
            'telefono' => 'teléfono',
            'fecha_registro' => 'fecha de registro',
        ];
    }
}

