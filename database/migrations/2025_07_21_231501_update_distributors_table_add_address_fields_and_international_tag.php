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
        Schema::table('distributors', function (Blueprint $table) {
            $table->string('street')->nullable()->after('address');
            $table->string('barangay')->nullable()->after('street');
            $table->string('city')->nullable()->after('barangay');
            $table->string('province')->nullable()->after('city');
            $table->string('country')->nullable()->after('province');
            $table->boolean('is_international')->default(false)->after('country');
            
            // Drop the old address column
            $table->dropColumn('address');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('distributors', function (Blueprint $table) {
            $table->string('address')->nullable()->after('phone');
            $table->dropColumn(['street', 'barangay', 'city', 'province', 'country', 'is_international']);
        });
    }
};
