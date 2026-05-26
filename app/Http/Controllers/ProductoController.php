<?php

namespace App\Http\Controllers;

use App\Http\Requests\ActualizarProductoRequest;
use App\Http\Requests\GuardarProductoRequest;
use App\Models\Producto;
use Illuminate\Http\Request;
use Throwable;

class ProductoController extends ApiController
{
    public function index(Request $request)
    {
        try {
            $productos = Producto::query()
                ->with('unidadMedida')
                ->buscar($request->input('buscar'))
                ->orderBy('descripcion')
                ->paginate(15);

            return $this->responderExito(
                $this->formatearPaginacion($productos, fn (Producto $producto): array => $this->formatearProducto($producto)),
                'Productos obtenidos correctamente.'
            );
        } catch (Throwable $error) {
            return $this->responderError('No fue posible obtener los productos.', 500);
        }
    }

    public function store(GuardarProductoRequest $request)
    {
        try {
            $producto = Producto::create($request->validated());

            return $this->responderExito(
                $this->formatearProducto($producto),
                'Producto registrado correctamente.',
                201
            );
        } catch (Throwable $error) {
            return $this->responderError('No fue posible registrar el producto.', 500);
        }
    }

    public function show(int $productoId)
    {
        try {
            $producto = Producto::with('unidadMedida')->findOrFail($productoId);

            return $this->responderExito(
                $this->formatearProducto($producto),
                'Producto obtenido correctamente.'
            );
        } catch (Throwable $error) {
            if ($error instanceof \Illuminate\Database\Eloquent\ModelNotFoundException) {
                return $this->responderNoEncontrado('El producto solicitado no existe.');
            }

            return $this->responderError('No fue posible obtener el producto.', 500);
        }
    }

    public function update(ActualizarProductoRequest $request, int $productoId)
    {
        try {
            $producto = Producto::findOrFail($productoId);
            $producto->update($request->validated());

            return $this->responderExito(
                $this->formatearProducto($producto),
                'Producto actualizado correctamente.'
            );
        } catch (Throwable $error) {
            if ($error instanceof \Illuminate\Database\Eloquent\ModelNotFoundException) {
                return $this->responderNoEncontrado('El producto solicitado no existe.');
            }

            return $this->responderError('No fue posible actualizar el producto.', 500);
        }
    }

    public function destroy(int $productoId)
    {
        try {
            $producto = Producto::findOrFail($productoId);

            if ($producto->detallesFactura()->exists()) {
                return $this->responderError('No se puede eliminar el producto porque ya aparece en detalles de factura.', 400);
            }

            $producto->delete();

            return $this->responderSinContenido('Producto eliminado correctamente.');
        } catch (Throwable $error) {
            if ($error instanceof \Illuminate\Database\Eloquent\ModelNotFoundException) {
                return $this->responderNoEncontrado('El producto solicitado no existe.');
            }

            return $this->responderError('No fue posible eliminar el producto.', 500);
        }
    }
}

