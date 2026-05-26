<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class ActualizarClienteRequest extends ApiFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $clienteId = $this->route('clienteId');

        return [
            'nombre' => ['required', 'string', 'max:255'],
            'rfc' => ['required', 'string', 'max:13', Rule::unique('clientes', 'rfc')->ignore($clienteId)],
            'direccion' => ['required', 'string'],
            'correo' => ['required', 'email', Rule::unique('clientes', 'correo')->ignore($clienteId)],
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

