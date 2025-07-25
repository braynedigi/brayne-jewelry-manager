<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Order;
use App\Models\OrderStatusHistory;
use App\Services\NotificationService;
use App\Events\OrderStatusChanged;

class FactoryController extends Controller
{
    /**
     * Show the factory production dashboard
     */
    public function dashboard()
    {
        $user = Auth::user();
        
        if (!$user->isFactory()) {
            abort(403, 'Access denied. Factory role required.');
        }

        // Get orders in production workflow
        $productionOrders = Order::whereIn('order_status', ['approved', 'in_production', 'finishing', 'ready_for_delivery'])
            ->with(['distributor.user', 'customer', 'products'])
            ->orderBy('priority', 'desc')
            ->orderBy('estimated_delivery_ready', 'asc')
            ->get();

        // Group orders by status
        $queueOrders = $productionOrders->where('order_status', 'approved');
        $inProductionOrders = $productionOrders->where('order_status', 'in_production');
        $finishingOrders = $productionOrders->where('order_status', 'finishing');
        $readyOrders = $productionOrders->where('order_status', 'ready_for_delivery');

        // Calculate workload statistics
        $totalEstimatedHours = $productionOrders->sum('estimated_production_hours') + $productionOrders->sum('estimated_finishing_hours');
        $urgentOrders = $productionOrders->where('priority', 'urgent')->count();
        $overdueOrders = $productionOrders->filter(function($order) {
            return $order->isOverdue();
        })->count();
        $dueToday = $productionOrders->filter(function($order) {
            return $order->estimated_delivery_ready && $order->estimated_delivery_ready->isToday();
        })->count();

        // Priority breakdown
        $priorityStats = [
            'urgent' => $productionOrders->where('priority', 'urgent')->count(),
            'normal' => $productionOrders->where('priority', 'normal')->count(),
            'low' => $productionOrders->where('priority', 'low')->count(),
        ];

        // Weekly workload
        $weeklyWorkload = $this->calculateWeeklyWorkload();

        return view('factory.dashboard', compact(
            'queueOrders',
            'inProductionOrders', 
            'finishingOrders',
            'readyOrders',
            'totalEstimatedHours',
            'urgentOrders',
            'overdueOrders',
            'dueToday',
            'priorityStats',
            'weeklyWorkload'
        ));
    }

    /**
     * Show production queue with filtering
     */
    public function queue(Request $request)
    {
        $user = Auth::user();
        
        if (!$user->isFactory()) {
            abort(403, 'Access denied. Factory role required.');
        }

        $query = Order::whereIn('order_status', ['approved', 'in_production', 'finishing', 'ready_for_delivery'])
            ->with(['distributor.user', 'customer', 'products']);

        // Apply filters
        if ($request->filled('status')) {
            $query->where('order_status', $request->status);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->filled('overdue')) {
            $query->overdue();
        }

        if ($request->filled('due_today')) {
            $query->dueToday();
        }

        // Sort by priority and due date
        $orders = $query->orderBy('priority', 'desc')
            ->orderBy('estimated_delivery_ready', 'asc')
            ->paginate(20);

        return view('factory.queue', compact('orders'));
    }

    /**
     * Update order status and timeline
     */
    public function updateStatus(Request $request, Order $order)
    {
        $user = Auth::user();
        
        if (!$user->isFactory()) {
            abort(403, 'Access denied. Factory role required.');
        }

        // Check if order is in factory workflow
        if (!in_array($order->order_status, ['approved', 'in_production', 'finishing', 'ready_for_delivery'])) {
            return back()->withErrors(['error' => 'Order is not in factory workflow.']);
        }

        // Get available statuses for factory
        $availableStatuses = $order->getNextAvailableStatuses($user);
        
        $validated = $request->validate([
            'order_status' => 'required|in:approved,in_production,finishing,ready_for_delivery',
            'production_notes' => 'nullable|string|max:1000',
            'estimated_production_hours' => 'nullable|integer|min:1',
            'estimated_finishing_hours' => 'nullable|integer|min:1',
        ]);

        // Additional validation: ensure the new status is in the available statuses
        if (!array_key_exists($validated['order_status'], $availableStatuses)) {
            return back()->withErrors(['order_status' => 'Invalid status transition for current order state.']);
        }

        try {
            DB::beginTransaction();

            $oldStatus = $order->order_status;
            $newStatus = $validated['order_status'];

            // Update status and timeline
            $updateData = [
                'order_status' => $newStatus,
                'production_notes' => $validated['production_notes'] ?? null,
            ];

            // Update estimated hours if provided
            if (isset($validated['estimated_production_hours'])) {
                $updateData['estimated_production_hours'] = $validated['estimated_production_hours'];
            }
            if (isset($validated['estimated_finishing_hours'])) {
                $updateData['estimated_finishing_hours'] = $validated['estimated_finishing_hours'];
            }

            // Update timeline based on status change
            if ($oldStatus !== $newStatus) {
                $updateData = array_merge($updateData, $this->calculateTimelineUpdates($order, $newStatus));
            }

            $order->update($updateData);

            // Create status history
            OrderStatusHistory::create([
                'order_id' => $order->id,
                'status' => $newStatus,
                'notes' => ($validated['production_notes'] ?? null) ?: "Status changed from {$oldStatus} to {$newStatus}",
                'changed_by' => $user->id,
            ]);

            DB::commit();

            // Broadcast status change event
            broadcast(new OrderStatusChanged($order, $oldStatus, $newStatus, $user, $validated['production_notes'] ?? null))->toOthers();

            // Send notification to distributor
            if ($order->distributor && $order->distributor->user) {
                $title = "Order Status Updated";
                $message = "Order #{$order->order_number} is now {$newStatus}";
                
                NotificationService::sendToUser(
                    $order->distributor->user->id,
                    'order_status_updated',
                    $title,
                    $message,
                    ['order_id' => $order->id, 'order_number' => $order->order_number, 'status' => $newStatus]
                );
            }

            return back()->with('success', "Order #{$order->order_number} status updated successfully!");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to update order: ' . $e->getMessage()]);
        }
    }

    /**
     * Update order priority
     */
    public function updatePriority(Request $request, Order $order)
    {
        $user = Auth::user();
        
        if (!$user->isFactory()) {
            abort(403, 'Access denied. Factory role required.');
        }

        $validated = $request->validate([
            'priority' => 'required|in:low,normal,urgent',
        ]);

        $order->update(['priority' => $validated['priority']]);

        return back()->with('success', "Order #{$order->order_number} priority updated to {$validated['priority']}!");
    }

    /**
     * Update production timeline
     */
    public function updateTimeline(Request $request, Order $order)
    {
        $user = Auth::user();
        
        if (!$user->isFactory()) {
            abort(403, 'Access denied. Factory role required.');
        }

        $validated = $request->validate([
            'estimated_start_date' => 'nullable|date|after:today',
            'estimated_production_complete' => 'nullable|date|after:estimated_start_date',
            'estimated_finishing_complete' => 'nullable|date|after:estimated_production_complete',
            'estimated_delivery_ready' => 'nullable|date|after:estimated_finishing_complete',
            'estimated_production_hours' => 'nullable|integer|min:1',
            'estimated_finishing_hours' => 'nullable|integer|min:1',
        ]);

        $order->update($validated);

        return back()->with('success', "Order #{$order->order_number} timeline updated successfully!");
    }

    /**
     * Get workload statistics
     */
    public function workload()
    {
        $user = Auth::user();
        
        if (!$user->isFactory()) {
            abort(403, 'Access denied. Factory role required.');
        }

        // Get production orders for statistics
        $productionOrders = Order::whereIn('order_status', ['approved', 'in_production', 'finishing', 'ready_for_delivery'])
            ->with(['distributor.user', 'customer', 'products'])
            ->get();

        $weeklyWorkload = $this->calculateWeeklyWorkload();
        $monthlyWorkload = $this->calculateMonthlyWorkload();
        $capacityData = $this->calculateCapacityData($productionOrders);
        
        // Priority statistics
        $priorityStats = [
            'urgent' => $productionOrders->where('priority', 'urgent')->count(),
            'normal' => $productionOrders->where('priority', 'normal')->count(),
            'low' => $productionOrders->where('priority', 'low')->count(),
        ];
        
        // Status statistics
        $statusStats = [
            'approved' => $productionOrders->where('order_status', 'approved')->count(),
            'in_production' => $productionOrders->where('order_status', 'in_production')->count(),
            'finishing' => $productionOrders->where('order_status', 'finishing')->count(),
            'ready_for_delivery' => $productionOrders->where('order_status', 'ready_for_delivery')->count(),
        ];

        return view('factory.workload', compact(
            'weeklyWorkload', 
            'monthlyWorkload', 
            'capacityData',
            'priorityStats',
            'statusStats'
        ));
    }

    /**
     * Calculate timeline updates based on status change
     */
    private function calculateTimelineUpdates(Order $order, string $newStatus): array
    {
        $updates = [];

        switch ($newStatus) {
            case 'in_production':
                if (!$order->production_started_at) {
                    $updates['production_started_at'] = now();
                }
                break;
                
            case 'finishing':
                if (!$order->production_completed_at) {
                    $updates['production_completed_at'] = now();
                }
                if (!$order->finishing_started_at) {
                    $updates['finishing_started_at'] = now();
                }
                break;
                
            case 'ready_for_delivery':
                if (!$order->finishing_completed_at) {
                    $updates['finishing_completed_at'] = now();
                }
                break;
        }

        return $updates;
    }

    /**
     * Calculate weekly workload
     */
    private function calculateWeeklyWorkload(): array
    {
        $workload = [];
        
        for ($i = 0; $i < 7; $i++) {
            $date = now()->addDays($i);
            $dayName = $date->format('D');
            
            $orders = Order::whereIn('order_status', ['approved', 'in_production', 'finishing', 'ready_for_delivery'])
                ->whereDate('estimated_delivery_ready', $date)
                ->get();
            
            $workload[$dayName] = [
                'date' => $date->format('M d'),
                'orders_count' => $orders->count(),
                'total_hours' => $orders->sum('estimated_production_hours') + $orders->sum('estimated_finishing_hours'),
                'urgent_count' => $orders->where('priority', 'urgent')->count(),
            ];
        }
        
        return $workload;
    }

    /**
     * Calculate monthly workload
     */
    private function calculateMonthlyWorkload(): array
    {
        $workload = [];
        
        // Group by month for the next 3 months
        for ($i = 0; $i < 3; $i++) {
            $month = now()->addMonths($i);
            $monthKey = $month->format('M Y');
            
            $orders = Order::whereIn('order_status', ['approved', 'in_production', 'finishing', 'ready_for_delivery'])
                ->whereYear('estimated_delivery_ready', $month->year)
                ->whereMonth('estimated_delivery_ready', $month->month)
                ->get();
            
            $workload[$monthKey] = [
                'orders_count' => $orders->count(),
                'total_hours' => $orders->sum('estimated_production_hours') + $orders->sum('estimated_finishing_hours'),
                'urgent_count' => $orders->where('priority', 'urgent')->count(),
            ];
        }
        
        return $workload;
    }

    /**
     * Calculate capacity data for workload analysis
     */
    private function calculateCapacityData($productionOrders): array
    {
        // Assuming 8 hours per day, 5 days per week = 40 hours capacity
        $weeklyCapacity = 40;
        
        $totalHours = $productionOrders->sum(function($order) {
            return ($order->estimated_production_hours ?? 0) + ($order->estimated_finishing_hours ?? 0);
        });
        
        $completedHours = $productionOrders->whereIn('order_status', ['ready_for_delivery'])
            ->sum(function($order) {
                return ($order->estimated_production_hours ?? 0) + ($order->estimated_finishing_hours ?? 0);
            });
        
        $remainingHours = $totalHours - $completedHours;
        
        $utilizationRate = $totalHours > 0 ? round(($totalHours / $weeklyCapacity) * 100, 1) : 0;
        
        return [
            'total_hours' => $totalHours,
            'completed_hours' => $completedHours,
            'remaining_hours' => $remainingHours,
            'utilization_rate' => $utilizationRate,
        ];
    }
} 