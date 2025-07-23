<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Order;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Courier;
use App\Models\OrderStatusHistory;
use App\Models\Distributor;
use App\Services\NotificationService;
use App\Events\OrderStatusChanged;
use App\Events\NewNotification;
use App\Models\OrderTemplate;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        
        $query = Order::with(['distributor.user', 'customer', 'products', 'template']);

        // Apply role-based filtering
        if ($user->isDistributor()) {
            $query->where('distributor_id', $user->distributor_id);
        }

        // Advanced search and filtering
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhereHas('customer', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                  })
                  ->orWhereHas('distributor.user', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('order_status', $request->status);
        }

        // Payment status filter
        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        // Priority filter
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        // Date range filter
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Amount range filter
        if (!$user->isFactory()) {
            if ($request->filled('amount_min')) {
                $query->where('total_amount', '>=', $request->amount_min);
            }
            if ($request->filled('amount_max')) {
                $query->where('total_amount', '<=', $request->amount_max);
            }
        }

        // Customer filter
        if ($request->filled('customer_id')) {
            $query->where('customer_id', $request->customer_id);
        }

        // Distributor filter (admin only)
        if ($user->isAdmin() && $request->filled('distributor_id')) {
            $query->where('distributor_id', $request->distributor_id);
        }

        // Template filter
        if ($request->filled('template_id')) {
            $query->where('template_id', $request->template_id);
        }

        // Overdue filter
        if ($request->boolean('overdue')) {
            $query->overdue();
        }

        // Due today filter
        if ($request->boolean('due_today')) {
            $query->dueToday();
        }

        // Sort options
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        
        $allowedSortFields = ['created_at', 'order_number', 'order_status', 'priority'];
        if (!$user->isFactory()) {
            $allowedSortFields[] = 'total_amount';
        }
        
        if (in_array($sortBy, $allowedSortFields)) {
            $query->orderBy($sortBy, $sortOrder);
        } else {
            $query->latest();
        }

        $orders = $query->paginate(20)->withQueryString();

        // Get filter options for the form
        $filterOptions = $this->getFilterOptions($user);

        return view('orders.index', compact('orders', 'filterOptions'));
    }

    /**
     * Get filter options for the search form
     */
    private function getFilterOptions($user): array
    {
        $options = [
            'statuses' => [
                'pending_payment' => 'Pending Payment',
                'approved' => 'Approved',
                'in_production' => 'In Production',
                'finishing' => 'Finishing',
                'ready_for_delivery' => 'Ready for Delivery',
                'delivered_to_brayne' => 'Delivered to Brayne',
                'delivered_to_client' => 'Delivered to Client',
                'cancelled' => 'Cancelled',
            ],
            'priorities' => [
                'low' => 'Low',
                'normal' => 'Normal',
                'urgent' => 'Urgent',
            ],
            'sort_fields' => [
                'created_at' => 'Date Created',
                'order_number' => 'Order Number',
                'order_status' => 'Status',
                'priority' => 'Priority',
            ],
        ];

        // Add payment statuses only for non-factory users
        if (!$user->isFactory()) {
            $options['payment_statuses'] = [
                'unpaid' => 'Unpaid',
                'partially_paid' => 'Partially Paid',
                'fully_paid' => 'Fully Paid',
            ];
            
            // Add total amount to sort fields for non-factory users
            $options['sort_fields']['total_amount'] = 'Total Amount';
        }

        // Get customers for filter
        if ($user->isAdmin()) {
            $options['customers'] = Customer::orderBy('name')->pluck('name', 'id')->toArray();
        } elseif ($user->isDistributor()) {
            $options['customers'] = Customer::where('distributor_id', $user->distributor_id)
                ->orderBy('name')
                ->pluck('name', 'id')
                ->toArray();
        }

        // Get distributors for admin filter
        if ($user->isAdmin()) {
            $options['distributors'] = Distributor::with('user')
                ->get()
                ->pluck('user.name', 'id')
                ->toArray();
        }

        // Get templates for filter
        if ($user->isDistributor()) {
            $options['templates'] = OrderTemplate::forDistributor($user->distributor_id)
                ->active()
                ->pluck('name', 'id')
                ->toArray();
        }

        return $options;
    }

    public function create()
    {
        $user = Auth::user();
        
        if ($user->isAdmin()) {
            // Admin can see all distributors and their customers
            $distributors = Distributor::with('user')->get();
            $customers = Customer::with('distributor.user')->get();
        } elseif ($user->isDistributor()) {
            // Distributor can only see their own customers
            $customers = Customer::where('distributor_id', $user->distributor->id)->get();
            $distributors = collect([$user->distributor]);
        } else {
            return redirect()->route('orders.index')->with('error', 'Access denied.');
        }

        $products = Product::active()->get();
        $couriers = Courier::active()->get();

        return view('orders.create', compact('customers', 'products', 'couriers', 'distributors'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        
        $validationRules = [
            'customer_id' => 'required|exists:customers,id',
            'courier_id' => 'nullable|exists:couriers,id',
            'notes' => 'nullable|string|max:1000',
            'payment_status' => 'required|in:unpaid,partially_paid,fully_paid',
            'products' => 'required|array|min:1',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
            'products.*.metal' => 'required|string',
            'products.*.fonts' => 'nullable|array',
            'products.*.custom_fonts' => 'nullable|array',
        ];

        // Only require distributor_id for admins
        if ($user->isAdmin()) {
            $validationRules['distributor_id'] = 'required|exists:distributors,id';
        }

        $validated = $request->validate($validationRules);

        // Check if distributor can access this customer
        if ($user->isDistributor()) {
            $customer = Customer::find($validated['customer_id']);
            if ($customer->distributor_id !== $user->distributor->id) {
                return back()->withErrors(['customer_id' => 'You can only create orders for your own customers.']);
            }
            $validated['distributor_id'] = $user->distributor->id;
        }

        try {
            DB::beginTransaction();

            // Generate order number
            $orderNumber = 'ORD-' . date('Ymd') . '-' . str_pad(Order::whereDate('created_at', today())->count() + 1, 4, '0', STR_PAD_LEFT);

            // Calculate total amount
            $totalAmount = 0;
            foreach ($validated['products'] as $productData) {
                $product = Product::find($productData['product_id']);
                $distributor = Distributor::find($validated['distributor_id']);
                $isInternational = $distributor ? $distributor->is_international : false;
                $price = $product->getPriceForMetal($productData['metal'], $isInternational);
                if ($price === null) {
                    throw new \Exception("Price not set for {$product->name} with metal {$productData['metal']}");
                }
                $totalAmount += $price * $productData['quantity'];
            }

            // Create order
            $order = Order::create([
                'distributor_id' => $validated['distributor_id'],
                'customer_id' => $validated['customer_id'],
                'courier_id' => $validated['courier_id'],
                'order_number' => $orderNumber,
                'total_amount' => $totalAmount,
                'payment_status' => $validated['payment_status'],
                'order_status' => 'pending_payment',
                'notes' => $validated['notes'],
            ]);

            // Attach products to order
            foreach ($validated['products'] as $productData) {
                $product = Product::find($productData['product_id']);
                $distributor = Distributor::find($validated['distributor_id']);
                $isInternational = $distributor ? $distributor->is_international : false;
                $price = $product->getPriceForMetal($productData['metal'], $isInternational);
                
                // Handle fonts
                $selectedFonts = [];
                if (isset($productData['fonts']) && is_array($productData['fonts'])) {
                    $selectedFonts = array_filter($productData['fonts']);
                }
                if (isset($productData['custom_fonts']) && is_array($productData['custom_fonts'])) {
                    $customFonts = array_filter($productData['custom_fonts']);
                    $selectedFonts = array_merge($selectedFonts, $customFonts);
                }
                
                // Validate font requirement
                if ($product->font_requirement > 0) {
                    if (count($selectedFonts) !== $product->font_requirement) {
                        throw new \Exception("Product '{$product->name}' requires exactly {$product->font_requirement} font(s), but " . count($selectedFonts) . " were provided.");
                    }
                }
                
                $order->products()->attach($productData['product_id'], [
                    'quantity' => $productData['quantity'],
                    'price' => $price,
                    'metal' => $productData['metal'],
                    'font' => !empty($selectedFonts) ? implode(', ', $selectedFonts) : null,
                ]);
            }

            // Create initial status history
            OrderStatusHistory::create([
                'order_id' => $order->id,
                'status' => 'pending_payment',
                'notes' => 'Order created - pending 50% payment',
                'changed_by' => $user->id,
            ]);

            DB::commit();

            // Send notifications
            NotificationService::orderCreated($order);

            return redirect()->route('orders.show', $order)->with('success', 'Order created successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to create order: ' . $e->getMessage()]);
        }
    }

    public function show(Order $order)
    {
        $user = Auth::user();
        
        // Check access permissions
        if ($user->isDistributor() && $order->distributor_id !== $user->distributor->id) {
            abort(403, 'Access denied.');
        }

        return view('orders.show', compact('order'));
    }

    public function edit(Order $order)
    {
        $user = Auth::user();
        
        // Check access permissions
        if ($user->isDistributor() && $order->distributor_id !== $user->distributor->id) {
            abort(403, 'Access denied.');
        }

        if ($user->isAdmin()) {
            $distributors = Distributor::with('user')->get();
            $customers = Customer::with('distributor.user')->get();
        } elseif ($user->isDistributor()) {
            $customers = Customer::where('distributor_id', $user->distributor->id)->get();
            $distributors = collect([$user->distributor]);
        } else {
            return redirect()->route('orders.index')->with('error', 'Access denied.');
        }

        $products = Product::active()->get();
        $couriers = Courier::active()->get();

        return view('orders.edit', compact('order', 'customers', 'products', 'couriers', 'distributors'));
    }

    public function update(Request $request, Order $order)
    {
        $user = Auth::user();
        
        // Check access permissions
        if ($user->isDistributor() && $order->distributor_id !== $user->distributor->id) {
            abort(403, 'Access denied.');
        }

        $validated = $request->validate([
            'distributor_id' => 'required|exists:distributors,id',
            'customer_id' => 'required|exists:customers,id',
            'courier_id' => 'nullable|exists:couriers,id',
            'notes' => 'nullable|string|max:1000',
            'payment_status' => 'required|in:unpaid,partially_paid,fully_paid',
            'products' => 'required|array|min:1',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
            'products.*.metal' => 'required|string',
        ]);

        // Add order status validation if user can update status
        if ($order->canUpdateStatus($user)) {
            $statusValidation = [];
            if ($user->isAdmin()) {
                $statusValidation = ['order_status' => 'nullable|in:pending_payment,approved,in_production,finishing,ready_for_delivery,delivered_to_brayne,delivered_to_client'];
            } elseif ($user->isFactory()) {
                $statusValidation = ['order_status' => 'nullable|in:approved,in_production,finishing,ready_for_delivery'];
            }
            $validated = array_merge($validated, $request->validate($statusValidation));
        }

        // Check if distributor can access this customer
        if ($user->isDistributor()) {
            $customer = Customer::find($validated['customer_id']);
            if ($customer->distributor_id !== $user->distributor->id) {
                return back()->withErrors(['customer_id' => 'You can only update orders for your own customers.']);
            }
            $validated['distributor_id'] = $user->distributor->id;
        }

        try {
            DB::beginTransaction();

            // Calculate total amount
            $totalAmount = 0;
            foreach ($validated['products'] as $productData) {
                $product = Product::find($productData['product_id']);
                $distributor = Distributor::find($validated['distributor_id']);
                $isInternational = $distributor ? $distributor->is_international : false;
                $price = $product->getPriceForMetal($productData['metal'], $isInternational);
                if ($price === null) {
                    throw new \Exception("Price not set for {$product->name} with metal {$productData['metal']}");
                }
                $totalAmount += $price * $productData['quantity'];
            }

            // Prepare update data
            $updateData = [
                'distributor_id' => $validated['distributor_id'],
                'customer_id' => $validated['customer_id'],
                'courier_id' => $validated['courier_id'],
                'total_amount' => $totalAmount,
                'notes' => $validated['notes'],
                'payment_status' => $validated['payment_status'],
            ];

            // Add order status if provided and user has permission
            if (isset($validated['order_status']) && $order->canUpdateStatus($user)) {
                $oldStatus = $order->order_status;
                $updateData['order_status'] = $validated['order_status'];
                
                // Create status history if status changed
                if ($oldStatus !== $validated['order_status']) {
                    OrderStatusHistory::create([
                        'order_id' => $order->id,
                        'status' => $validated['order_status'],
                        'notes' => "Status updated from {$oldStatus} to {$validated['order_status']}",
                        'changed_by' => $user->id,
                    ]);

                    // Broadcast status change event
                    broadcast(new OrderStatusChanged($order, $oldStatus, $validated['order_status'], $user, $validated['notes'] ?? null))->toOthers();
                }
            }

            // Update order
            $order->update($updateData);

            // Remove existing products and attach new ones
            $order->products()->detach();
            foreach ($validated['products'] as $productData) {
                $product = Product::find($productData['product_id']);
                $distributor = Distributor::find($validated['distributor_id']);
                $isInternational = $distributor ? $distributor->is_international : false;
                $price = $product->getPriceForMetal($productData['metal'], $isInternational);
                
                $order->products()->attach($productData['product_id'], [
                    'quantity' => $productData['quantity'],
                    'price' => $price,
                    'metal' => $productData['metal'],
                    'font' => $product->fonts ? implode(', ', $product->fonts) : null,
                ]);
            }

            DB::commit();

            return redirect()->route('orders.show', $order)->with('success', 'Order updated successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to update order: ' . $e->getMessage()]);
        }
    }

    public function updateStatus(Request $request, Order $order)
    {
        $user = Auth::user();
        
        // Check if user can update this order's status
        if (!$order->canUpdateStatus($user)) {
            abort(403, 'You do not have permission to update this order status.');
        }

        // Get available statuses for current user
        $availableStatuses = $order->getNextAvailableStatuses($user);
        
        // Validate status based on user role and current status
        if ($user->isAdmin()) {
            $validated = $request->validate([
                'order_status' => 'required|in:pending_payment,approved,in_production,finishing,ready_for_delivery,delivered_to_brayne,delivered_to_client,cancelled',
                'notes' => 'nullable|string|max:1000',
            ]);
        } elseif ($user->isFactory()) {
            $validated = $request->validate([
                'order_status' => 'required|in:approved,in_production,finishing,ready_for_delivery',
                'notes' => 'nullable|string|max:1000',
            ]);
        } else {
            abort(403, 'You do not have permission to update order status.');
        }

        // Additional validation: ensure the new status is in the available statuses
        if (!array_key_exists($validated['order_status'], $availableStatuses)) {
            return back()->withErrors(['order_status' => 'Invalid status transition for current order state.']);
        }

        try {
            DB::beginTransaction();

            $oldStatus = $order->order_status;
            $order->update(['order_status' => $validated['order_status']]);

            // Create status history
            OrderStatusHistory::create([
                'order_id' => $order->id,
                'status' => $validated['order_status'],
                'notes' => $validated['notes'] ?: "Status changed from {$oldStatus} to {$validated['order_status']}",
                'changed_by' => $user->id,
            ]);

            DB::commit();

            // Broadcast status change event
            broadcast(new OrderStatusChanged($order, $oldStatus, $validated['order_status'], $user, $validated['notes'] ?? null))->toOthers();

            // Send notifications
            NotificationService::orderStatusUpdated($order, $oldStatus, $validated['order_status']);

            return redirect()->route('orders.show', $order)->with('success', 'Order status updated successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to update order status: ' . $e->getMessage()]);
        }
    }

    public function destroy(Order $order)
    {
        $user = Auth::user();
        
        // Check access permissions
        if ($user->isDistributor() && $order->distributor_id !== $user->distributor->id) {
            abort(403, 'Access denied.');
        }

        $order->delete();

        return redirect()->route('orders.index')->with('success', 'Order deleted successfully!');
    }
}
