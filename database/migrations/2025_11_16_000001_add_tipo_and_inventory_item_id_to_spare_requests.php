<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('spare_requests', function (Blueprint $table) {
            $table->string('tipo')->nullable()->after('detalles')->comment('tipo de solicitud: compra|eliminacion');
            $table->unsignedBigInteger('inventory_item_id')->nullable()->after('tipo');
            $table->foreign('inventory_item_id')->references('id')->on('inventory_items')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('spare_requests', function (Blueprint $table) {
            $table->dropForeign(['inventory_item_id']);
            $table->dropColumn(['inventory_item_id', 'tipo']);
        });
    }
};
