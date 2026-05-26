<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('productos', function (Blueprint $table) {
            $table->id();
            $table->string('codigo')->unique();
            $table->string('descripcion');
            $table->decimal('precio_unitario', 12, 2);
            $table->foreignId('unidad_medida_id')->constrained('unidades_medida')->restrictOnDelete();
            $table->decimal('porcentaje_iva', 5, 2)->default(16.00);
            $table->timestamps();

            $table->index('descripcion');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('productos');
    }
};

