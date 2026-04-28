<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add the correctly spelled column if it doesn't exist and copy existing values
        if (! Schema::hasColumn('inventory_items', 'sigicov')) {
            Schema::table('inventory_items', function (Blueprint $table) {
                $table->string('sigicov')->nullable()->after('id');
            });

            // Copy existing values from the old column (if any)
            DB::statement("UPDATE inventory_items SET sigicov = sigcov WHERE sigcov IS NOT NULL");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('inventory_items', 'sigicov')) {
            Schema::table('inventory_items', function (Blueprint $table) {
                $table->dropColumn('sigicov');
            });
        }
    }
};
