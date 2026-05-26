<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('facturas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('clientes')->restrictOnDelete();
            $table->string('folio')->unique();
            $table->date('fecha_emision');
            $table->date('fecha_vencimiento')->index();
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('iva', 12, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);
            $table->string('estatus', 20)->default('pendiente')->index();
            $table->string('metodo_pago', 50);
            $table->timestamps();

            $table->index(['cliente_id', 'fecha_emision']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('facturas');
    }
};

