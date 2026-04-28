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
        Schema::create('spare_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('almacenista_id')->index();
            $table->integer('cantidad');
            $table->string('descripcion');
            $table->text('detalles')->nullable();
            $table->timestamps();

            $table->foreign('almacenista_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('spare_requests');
    }
};
