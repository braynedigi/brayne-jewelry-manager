<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Order;
use App\Models\OrderStatusHistory;
use App\Services\NotificationService;

class ApprovalController extends Controller
{
    /**
     * Show the approval queue dashboard
     */
    public function index()
    {
        $user = Auth::user();
        
        if (!$user->isAdmin()) {
            abort(403, 'Access denied. Admin role required.');
        }

        // Get pending orders that need approval
        $pendingOrders = Order::where('order_status', 'pending_payment')
            ->with(['distributor.user', 'customer', 'courier', 'products'])
            ->latest()
            ->get();

        // Get recently approved orders (last 7 days)
        $recentlyApproved = Order::where('order_status', 'approved')
            ->where('updated_at', '>=', now()->subDays(7))
            ->with(['distributor.user', 'customer'])
            ->latest()
            ->take(10)
            ->get();

        $stats = [
            'pending_count' => $pendingOrders->count(),
            'approved_today' => Order::where('order_status', 'approved')
                ->whereDate('updated_at', today())
                ->count(),
            'approved_this_week' => Order::where('order_status', 'approved')
                ->where('updated_at', '>=', now()->startOfWeek())
                ->count(),
        ];

        return view('admin.approval.index', compact('pendingOrders', 'recentlyApproved', 'stats'));
    }

    /**
     * Show approval form for a single order
     */
    public function show(Order $order)
    {
        $user = Auth::user();
        
        if (!$user->isAdmin()) {
            abort(403, 'Access denied. Admin role required.');
        }

        if ($order->order_status !== 'pending_payment') {
            return redirect()->route('admin.approval.index')
                ->with('error', 'This order is not pending approval.');
        }

        return view('admin.approval.show', compact('order'));
    }

    /**
     * Approve or reject a single order
     */
    public function update(Request $request, Order $order)
    {
        $user = Auth::user();
        
        if (!$user->isAdmin()) {
            abort(403, 'Access denied. Admin role required.');
        }

        if ($order->order_status !== 'pending_payment') {
            return redirect()->route('admin.approval.index')
                ->with('error', 'This order is not pending approval.');
        }

        $validated = $request->validate([
            'action' => 'required|in:approve,reject',
            'notes' => 'required|string|max:1000',
        ]);

        try {
            DB::beginTransaction();

            $oldStatus = $order->order_status;
            $newStatus = $validated['action'] === 'approve' ? 'approved' : 'cancelled';
            
            $order->update(['order_status' => $newStatus]);

            // Create status history
            OrderStatusHistory::create([
                'order_id' => $order->id,
                'status' => $newStatus,
                'notes' => $validated['notes'],
                'changed_by' => $user->id,
            ]);

            DB::commit();

            // Send notification to distributor
            $action = $validated['action'] === 'approve' ? 'approved' : 'rejected';
            $title = "Order {$action}";
            $message = "Order #{$order->order_number} has been {$action}. Reason: {$validated['notes']}";
            
            if ($order->distributor && $order->distributor->user) {
                NotificationService::sendToUser(
                    $order->distributor->user->id,
                    'order_approval',
                    $title,
                    $message,
                    ['order_id' => $order->id, 'order_number' => $order->order_number, 'action' => $validated['action']]
                );
            }

            return redirect()->route('admin.approval.index')
                ->with('success', "Order #{$order->order_number} has been {$action} successfully!");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to process order: ' . $e->getMessage()]);
        }
    }

    /**
     * Bulk approve/reject orders
     */
    public function bulkUpdate(Request $request)
    {
        $user = Auth::user();
        
        if (!$user->isAdmin()) {
            abort(403, 'Access denied. Admin role required.');
        }

        $validated = $request->validate([
            'order_ids' => 'required|array|min:1',
            'order_ids.*' => 'exists:orders,id',
            'action' => 'required|in:approve,reject',
            'notes' => 'required|string|max:1000',
        ]);

        $orders = Order::whereIn('id', $validated['order_ids'])
            ->where('order_status', 'pending_payment')
            ->with(['distributor.user'])
            ->get();

        if ($orders->isEmpty()) {
            return back()->withErrors(['error' => 'No valid orders found for bulk processing.']);
        }

        $successCount = 0;
        $failedCount = 0;

        try {
            DB::beginTransaction();

            foreach ($orders as $order) {
                try {
                    $oldStatus = $order->order_status;
                    $newStatus = $validated['action'] === 'approve' ? 'approved' : 'cancelled';
                    
                    $order->update(['order_status' => $newStatus]);

                    // Create status history
                    OrderStatusHistory::create([
                        'order_id' => $order->id,
                        'status' => $newStatus,
                        'notes' => $validated['notes'],
                        'changed_by' => $user->id,
                    ]);

                    // Send notification to distributor
                    $action = $validated['action'] === 'approve' ? 'approved' : 'rejected';
                    $title = "Order {$action}";
                    $message = "Order #{$order->order_number} has been {$action}. Reason: {$validated['notes']}";
                    
                    if ($order->distributor && $order->distributor->user) {
                        NotificationService::sendToUser(
                            $order->distributor->user->id,
                            'order_approval',
                            $title,
                            $message,
                            ['order_id' => $order->id, 'order_number' => $order->order_number, 'action' => $validated['action']]
                        );
                    }

                    $successCount++;
                } catch (\Exception $e) {
                    $failedCount++;
                    \Log::error("Failed to process order {$order->id}: " . $e->getMessage());
                }
            }

            DB::commit();

            $message = "Successfully processed {$successCount} orders.";
            if ($failedCount > 0) {
                $message .= " {$failedCount} orders failed to process.";
            }

            return redirect()->route('admin.approval.index')
                ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to process bulk operation: ' . $e->getMessage()]);
        }
    }

    /**
     * Get approval statistics for dashboard
     */
    public function stats()
    {
        $user = Auth::user();
        
        if (!$user->isAdmin()) {
            abort(403, 'Access denied. Admin role required.');
        }

        $stats = [
            'pending_count' => Order::where('order_status', 'pending_payment')->count(),
            'approved_today' => Order::where('order_status', 'approved')
                ->whereDate('updated_at', today())
                ->count(),
            'approved_this_week' => Order::where('order_status', 'approved')
                ->where('updated_at', '>=', now()->startOfWeek())
                ->count(),
            'rejected_today' => Order::where('order_status', 'cancelled')
                ->whereDate('updated_at', today())
                ->count(),
        ];

        return response()->json($stats);
    }
} 