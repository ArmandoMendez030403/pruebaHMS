<?php

namespace App\Models;

use App\Models\Cliente;
use App\Models\DetalleFactura;
use App\Models\Pago;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Factura extends Model
{
    protected $table = 'facturas';

    protected $fillable = [
        'cliente_id',
        'folio',
        'fecha_emision',
        'fecha_vencimiento',
        'subtotal',
        'iva',
        'total',
        'estatus',
        'metodo_pago',
    ];

    protected $casts = [
        'fecha_emision' => 'date',
        'fecha_vencimiento' => 'date',
        'subtotal' => 'decimal:2',
        'iva' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }

    public function detallesFactura(): HasMany
    {
        return $this->hasMany(DetalleFactura::class, 'factura_id');
    }

    public function pagos(): HasMany
    {
        return $this->hasMany(Pago::class, 'factura_id');
    }

    public function scopeBuscar(Builder $consulta, ?string $termino): Builder
    {
        if ($termino === null || $termino === '') {
            return $consulta;
        }

        return $consulta->where('folio', 'like', "%{$termino}%");
    }

    public function saldoPagado(): float
    {
        $this->loadMissing('pagos');

        return (float) $this->pagos->sum(static function (Pago $pago): float {
            return (float) $pago->monto;
        });
    }

    public function saldoPendiente(): float
    {
        return max(0, (float) $this->total - $this->saldoPagado());
    }

    public function recalcularTotales(): void
    {
        $this->loadMissing('detallesFactura');

        $subtotal = $this->detallesFactura->sum(static function (DetalleFactura $detalle): float {
            return (float) $detalle->subtotal;
        });

        $iva = $this->detallesFactura->sum(static function (DetalleFactura $detalle): float {
            return (float) $detalle->subtotal * ((float) $detalle->porcentaje_iva / 100);
        });

        $this->subtotal = round($subtotal, 2);
        $this->iva = round($iva, 2);
        $this->total = round($subtotal + $iva, 2);
    }

    public function sincronizarEstatus(): bool
    {
        if ($this->estatus === 'cancelada') {
            return false;
        }

        $nuevoEstatus = 'pendiente';
        $saldoPagado = $this->saldoPagado();

        if ($saldoPagado >= (float) $this->total) {
            $nuevoEstatus = 'pagada';
        } elseif ($this->fecha_vencimiento !== null && $this->fecha_vencimiento->lt(today())) {
            $nuevoEstatus = 'vencida';
        }

        if ($this->estatus === $nuevoEstatus) {
            return false;
        }

        $this->estatus = $nuevoEstatus;
        $this->save();

        return true;
    }

    public function estaCancelada(): bool
    {
        return $this->estatus === 'cancelada';
    }
}


