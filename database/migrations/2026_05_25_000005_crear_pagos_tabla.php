<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pagos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('factura_id')->constrained('facturas')->restrictOnDelete();
            $table->date('fecha_pago')->index();
            $table->decimal('monto', 12, 2);
            $table->string('forma_pago', 50);
            $table->string('referencia')->nullable();
            $table->timestamps();

            $table->index('factura_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pagos');
    }
};

