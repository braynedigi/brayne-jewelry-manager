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
        Schema::table('orders', function (Blueprint $table) {
            // Priority system
            $table->enum('priority', ['low', 'normal', 'urgent'])->default('normal')->after('order_status');
            
            // Production timeline
            $table->timestamp('estimated_start_date')->nullable()->after('priority');
            $table->timestamp('estimated_production_complete')->nullable()->after('estimated_start_date');
            $table->timestamp('estimated_finishing_complete')->nullable()->after('estimated_production_complete');
            $table->timestamp('estimated_delivery_ready')->nullable()->after('estimated_finishing_complete');
            
            // Production tracking
            $table->timestamp('production_started_at')->nullable()->after('estimated_delivery_ready');
            $table->timestamp('production_completed_at')->nullable()->after('production_started_at');
            $table->timestamp('finishing_started_at')->nullable()->after('production_completed_at');
            $table->timestamp('finishing_completed_at')->nullable()->after('finishing_started_at');
            
            // Workload management
            $table->integer('estimated_production_hours')->nullable()->after('finishing_completed_at');
            $table->integer('estimated_finishing_hours')->nullable()->after('estimated_production_hours');
            $table->text('production_notes')->nullable()->after('estimated_finishing_hours');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'priority',
                'estimated_start_date',
                'estimated_production_complete',
                'estimated_finishing_complete',
                'estimated_delivery_ready',
                'production_started_at',
                'production_completed_at',
                'finishing_started_at',
                'finishing_completed_at',
                'estimated_production_hours',
                'estimated_finishing_hours',
                'production_notes'
            ]);
        });
    }
};
