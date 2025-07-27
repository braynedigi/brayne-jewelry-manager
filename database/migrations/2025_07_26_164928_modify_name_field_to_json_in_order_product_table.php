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
        Schema::table('order_product', function (Blueprint $table) {
            // Drop the existing name column
            $table->dropColumn('name');
        });

        Schema::table('order_product', function (Blueprint $table) {
            // Add the new JSON name column
            $table->json('names')->nullable()->after('font');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_product', function (Blueprint $table) {
            // Drop the JSON names column
            $table->dropColumn('names');
        });

        Schema::table('order_product', function (Blueprint $table) {
            // Restore the original string name column
            $table->string('name')->nullable()->after('font');
        });
    }
};
