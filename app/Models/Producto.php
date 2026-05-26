<?php

namespace App\Models;

use App\Models\DetalleFactura;
use App\Models\UnidadMedida;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Producto extends Model
{
    protected $table = 'productos';

    protected $fillable = [
        'codigo',
        'descripcion',
        'precio_unitario',
        'unidad_medida_id',
        'porcentaje_iva',
    ];

    protected $casts = [
        'precio_unitario' => 'decimal:2',
        'porcentaje_iva' => 'decimal:2',
    ];

    public function unidadMedida(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(UnidadMedida::class, 'unidad_medida_id');
    }

    public function detallesFactura(): HasMany
    {
        return $this->hasMany(DetalleFactura::class, 'producto_id');
    }

    public function scopeBuscar(Builder $consulta, ?string $termino): Builder
    {
        if ($termino === null || $termino === '') {
            return $consulta;
        }

        return $consulta->where(function (Builder $subconsulta) use ($termino): void {
            $subconsulta->where('codigo', 'like', "%{$termino}%")
                ->orWhere('descripcion', 'like', "%{$termino}%");
        });
    }
}


