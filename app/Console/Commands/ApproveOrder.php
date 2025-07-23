<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Order;

class ApproveOrder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'order:approve {order_number}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Approve an order by order number';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $orderNumber = $this->argument('order_number');
        
        $order = Order::where('order_number', $orderNumber)->first();
        
        if (!$order) {
            $this->error("Order with number '{$orderNumber}' not found.");
            return 1;
        }
        
        if ($order->order_status !== 'pending_payment') {
            $this->warn("Order is not in 'pending_payment' status. Current status: {$order->order_status}");
            if (!$this->confirm('Do you want to approve it anyway?')) {
                return 0;
            }
        }
        
        $oldStatus = $order->order_status;
        $order->update(['order_status' => 'approved']);
        
        // Create status history
        \App\Models\OrderStatusHistory::create([
            'order_id' => $order->id,
            'status' => 'approved',
            'notes' => "Order approved via command line (from {$oldStatus})",
            'changed_by' => 1, // Assuming admin user ID is 1
        ]);
        
        $this->info("Order {$orderNumber} has been approved successfully!");
        $this->info("Status changed from '{$oldStatus}' to 'approved'");
        
        return 0;
    }
}
