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
        Schema::table('spare_requests', function (Blueprint $table) {
            $table->string('status')->default('pending')->after('detalles');
            $table->text('reject_reason')->nullable()->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('spare_requests', function (Blueprint $table) {
            $table->dropColumn(['status', 'reject_reason']);
        });
    }
};
