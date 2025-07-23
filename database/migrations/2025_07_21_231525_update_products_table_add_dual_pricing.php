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
        Schema::table('products', function (Blueprint $table) {
            // Drop the old metal_pricing and currency columns
            $table->dropColumn(['metal_pricing', 'currency']);
            
            // Add new dual pricing columns
            $table->json('local_pricing')->nullable()->after('metals');
            $table->json('international_pricing')->nullable()->after('local_pricing');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Drop the new pricing columns
            $table->dropColumn(['local_pricing', 'international_pricing']);
            
            // Restore old columns
            $table->json('metal_pricing')->nullable()->after('metals');
            $table->enum('currency', ['USD', 'PHP'])->default('PHP')->after('metal_pricing');
        });
    }
};
