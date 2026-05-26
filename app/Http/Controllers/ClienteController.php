<?php

namespace App\Http\Controllers;

use App\Http\Requests\ActualizarClienteRequest;
use App\Http\Requests\GuardarClienteRequest;
use App\Models\Cliente;
use Illuminate\Http\Request;
use Throwable;

class ClienteController extends ApiController
{
    public function index(Request $request)
    {
        try {
            $clientes = Cliente::query()
                ->withCount('facturas')
                ->buscar($request->input('buscar'))
                ->orderBy('nombre')
                ->paginate(15);

            return $this->responderExito(
                $this->formatearPaginacion($clientes, fn (Cliente $cliente): array => $this->formatearCliente($cliente)),
                'Clientes obtenidos correctamente.'
            );
        } catch (Throwable $error) {
            return $this->responderError('No fue posible obtener los clientes.', 500);
        }
    }

    public function store(GuardarClienteRequest $request)
    {
        try {
            $datos = $request->validated();

            if (! array_key_exists('fecha_registro', $datos) || $datos['fecha_registro'] === null) {
                unset($datos['fecha_registro']);
            }

            $cliente = Cliente::create($datos);
            $cliente->loadCount('facturas');

            return $this->responderExito(
                $this->formatearCliente($cliente),
                'Cliente registrado correctamente.',
                201
            );
        } catch (Throwable $error) {
            return $this->responderError('No fue posible registrar el cliente.', 500);
        }
    }

    public function show(int $clienteId)
    {
        try {
            $cliente = Cliente::withCount('facturas')->findOrFail($clienteId);

            return $this->responderExito(
                $this->formatearCliente($cliente),
                'Cliente obtenido correctamente.'
            );
        } catch (Throwable $error) {
            if ($error instanceof \Illuminate\Database\Eloquent\ModelNotFoundException) {
                return $this->responderNoEncontrado('El cliente solicitado no existe.');
            }

            return $this->responderError('No fue posible obtener el cliente.', 500);
        }
    }

    public function update(ActualizarClienteRequest $request, int $clienteId)
    {
        try {
            $cliente = Cliente::findOrFail($clienteId);
            $datos = $request->validated();

            if (! array_key_exists('fecha_registro', $datos) || $datos['fecha_registro'] === null) {
                unset($datos['fecha_registro']);
            }

            $cliente->update($datos);
            $cliente->loadCount('facturas');

            return $this->responderExito(
                $this->formatearCliente($cliente),
                'Cliente actualizado correctamente.'
            );
        } catch (Throwable $error) {
            if ($error instanceof \Illuminate\Database\Eloquent\ModelNotFoundException) {
                return $this->responderNoEncontrado('El cliente solicitado no existe.');
            }

            return $this->responderError('No fue posible actualizar el cliente.', 500);
        }
    }

    public function destroy(int $clienteId)
    {
        try {
            $cliente = Cliente::findOrFail($clienteId);

            if ($cliente->facturas()->exists()) {
                return $this->responderError('No se puede eliminar el cliente porque tiene facturas asociadas.', 400);
            }

            $cliente->delete();

            return $this->responderSinContenido('Cliente eliminado correctamente.');
        } catch (Throwable $error) {
            if ($error instanceof \Illuminate\Database\Eloquent\ModelNotFoundException) {
                return $this->responderNoEncontrado('El cliente solicitado no existe.');
            }

            return $this->responderError('No fue posible eliminar el cliente.', 500);
        }
    }
}

