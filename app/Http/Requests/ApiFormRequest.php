<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

abstract class ApiFormRequest extends FormRequest
{
    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(response()->json([
            'exito' => false,
            'datos' => null,
            'mensaje' => 'Los datos enviados no son válidos.',
            'errores' => $validator->errors(),
        ], 422));
    }

    protected function failedAuthorization(): void
    {
        throw new HttpResponseException(response()->json([
            'exito' => false,
            'datos' => null,
            'mensaje' => 'No está autorizado para realizar esta acción.',
        ], 403));
    }

    public function messages(): array
    {
        return [
            'required' => 'El campo :attribute es obligatorio.',
            'string' => 'El campo :attribute debe ser una cadena de texto.',
            'email' => 'El campo :attribute debe ser un correo electrónico válido.',
            'unique' => 'El campo :attribute ya existe.',
            'exists' => 'El valor seleccionado para :attribute no es válido.',
            'numeric' => 'El campo :attribute debe ser numérico.',
            'integer' => 'El campo :attribute debe ser un número entero.',
            'array' => 'El campo :attribute debe ser un arreglo.',
            'date' => 'El campo :attribute debe ser una fecha válida.',
            'in' => 'El campo :attribute contiene un valor no permitido.',
            'min.numeric' => 'El campo :attribute debe ser al menos :min.',
            'min.string' => 'El campo :attribute debe tener al menos :min caracteres.',
            'min.array' => 'El campo :attribute debe contener al menos :min elementos.',
            'max.string' => 'El campo :attribute no debe superar :max caracteres.',
            'max.numeric' => 'El campo :attribute no debe ser mayor que :max.',
            'after_or_equal' => 'El campo :attribute debe ser una fecha igual o posterior a :date.',
            'nullable' => 'El campo :attribute es inválido.',
        ];
    }
}

