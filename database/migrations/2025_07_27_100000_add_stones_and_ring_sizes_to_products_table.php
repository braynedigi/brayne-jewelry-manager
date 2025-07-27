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
            // Add stone and ring size fields
            $table->json('stones')->nullable()->after('fonts');
            $table->boolean('requires_stones')->default(false)->after('stones');
            $table->boolean('requires_ring_size')->default(false)->after('requires_stones');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['stones', 'requires_stones', 'requires_ring_size']);
        });
    }
}; 