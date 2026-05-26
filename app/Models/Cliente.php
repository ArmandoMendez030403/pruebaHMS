<?php

namespace App\Models;

use App\Models\Factura;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cliente extends Model
{
    protected $table = 'clientes';

    protected $fillable = [
        'nombre',
        'rfc',
        'direccion',
        'correo',
        'telefono',
        'fecha_registro',
    ];

    protected $casts = [
        'fecha_registro' => 'datetime',
    ];

    public function facturas(): HasMany
    {
        return $this->hasMany(Factura::class, 'cliente_id');
    }

    public function scopeBuscar(Builder $consulta, ?string $termino): Builder
    {
        if ($termino === null || $termino === '') {
            return $consulta;
        }

        return $consulta->where(function (Builder $subconsulta) use ($termino): void {
            $subconsulta->where('nombre', 'like', "%{$termino}%")
                ->orWhere('rfc', 'like', "%{$termino}%")
                ->orWhere('correo', 'like', "%{$termino}%");
        });
    }
}


