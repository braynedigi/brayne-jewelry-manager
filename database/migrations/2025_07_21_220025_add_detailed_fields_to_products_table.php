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
            $table->string('sub_category')->nullable()->after('category');
            $table->string('custom_sub_category')->nullable()->after('sub_category');
            $table->json('metals')->nullable()->after('custom_sub_category');
            $table->json('fonts')->nullable()->after('metals');
            $table->enum('currency', ['USD', 'PHP'])->default('USD')->after('price');
            $table->dropColumn('description'); // Remove description as per requirements
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->text('description')->nullable();
            $table->dropColumn(['sub_category', 'custom_sub_category', 'metals', 'fonts', 'currency']);
        });
    }
};
