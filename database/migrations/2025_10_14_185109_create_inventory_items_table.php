<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('inventory_items', function (Blueprint $table) {
            $table->id();
            $table->string('sigcov')->nullable();
            $table->string('descripcion');
            $table->string('pasillo')->nullable();
            $table->string('estante')->nullable();
            $table->string('peldaño')->nullable();
            $table->date('fecha')->nullable();
            $table->string('realizado_por')->nullable();
            $table->string('inventario')->nullable();
            $table->string('resp_accesorios')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_items');
    }
};
