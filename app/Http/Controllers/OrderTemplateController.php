<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\OrderTemplate;
use App\Models\Order;
use App\Models\Product;

class OrderTemplateController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        if (!$user->isDistributor()) {
            abort(403, 'Access denied. Distributor role required.');
        }

        $templates = OrderTemplate::forDistributor($user->distributor_id)
            ->active()
            ->orderBy('usage_count', 'desc')
            ->orderBy('name')
            ->get();

        return view('order-templates.index', compact('templates'));
    }

    public function create()
    {
        $user = Auth::user();
        
        if (!$user->isDistributor()) {
            abort(403, 'Access denied. Distributor role required.');
        }

        $products = Product::active()->get();
        $priorities = OrderTemplate::getPriorities();

        return view('order-templates.create', compact('products', 'priorities'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        
        if (!$user->isDistributor()) {
            abort(403, 'Access denied. Distributor role required.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'products' => 'required|array|min:1',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
            'products.*.metal' => 'required|string',
            'products.*.font' => 'nullable|string',
            'default_notes' => 'nullable|string|max:1000',
            'priority' => 'required|in:low,normal,urgent',
        ]);

        try {
            DB::beginTransaction();

            $template = OrderTemplate::create([
                'distributor_id' => $user->distributor_id,
                'name' => $validated['name'],
                'description' => $validated['description'],
                'products' => $validated['products'],
                'default_notes' => ['notes' => $validated['default_notes']],
                'priority' => $validated['priority'],
            ]);

            DB::commit();

            return redirect()->route('order-templates.index')->with('success', 'Order template created successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to create template: ' . $e->getMessage()]);
        }
    }

    public function show(OrderTemplate $template)
    {
        $user = Auth::user();
        
        if (!$user->isDistributor() || $template->distributor_id !== $user->distributor_id) {
            abort(403, 'Access denied.');
        }

        return view('order-templates.show', compact('template'));
    }

    public function edit(OrderTemplate $template)
    {
        $user = Auth::user();
        
        if (!$user->isDistributor() || $template->distributor_id !== $user->distributor_id) {
            abort(403, 'Access denied.');
        }

        $products = Product::active()->get();
        $priorities = OrderTemplate::getPriorities();

        return view('order-templates.edit', compact('template', 'products', 'priorities'));
    }

    public function update(Request $request, OrderTemplate $template)
    {
        $user = Auth::user();
        
        if (!$user->isDistributor() || $template->distributor_id !== $user->distributor_id) {
            abort(403, 'Access denied.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'products' => 'required|array|min:1',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
            'products.*.metal' => 'required|string',
            'products.*.font' => 'nullable|string',
            'default_notes' => 'nullable|string|max:1000',
            'priority' => 'required|in:low,normal,urgent',
            'is_active' => 'boolean',
        ]);

        try {
            DB::beginTransaction();

            $template->update([
                'name' => $validated['name'],
                'description' => $validated['description'],
                'products' => $validated['products'],
                'default_notes' => ['notes' => $validated['default_notes']],
                'priority' => $validated['priority'],
                'is_active' => $request->has('is_active'),
            ]);

            DB::commit();

            return redirect()->route('order-templates.index')->with('success', 'Order template updated successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to update template: ' . $e->getMessage()]);
        }
    }

    public function destroy(OrderTemplate $template)
    {
        $user = Auth::user();
        
        if (!$user->isDistributor() || $template->distributor_id !== $user->distributor_id) {
            abort(403, 'Access denied.');
        }

        $template->delete();

        return redirect()->route('order-templates.index')->with('success', 'Order template deleted successfully!');
    }

    /**
     * Create template from existing order
     */
    public function createFromOrder(Order $order)
    {
        $user = Auth::user();
        
        if (!$user->isDistributor() || $order->distributor_id !== $user->distributor_id) {
            abort(403, 'Access denied.');
        }

        $products = [];
        foreach ($order->products as $product) {
            $products[] = [
                'product_id' => $product->id,
                'quantity' => $product->pivot->quantity,
                'metal' => $product->pivot->metal,
                'font' => $product->pivot->font,
            ];
        }

        $priorities = OrderTemplate::getPriorities();

        return view('order-templates.create-from-order', compact('order', 'products', 'priorities'));
    }

    /**
     * Use template to create new order
     */
    public function useTemplate(Request $request, OrderTemplate $template)
    {
        $user = Auth::user();
        
        if (!$user->isDistributor() || $template->distributor_id !== $user->distributor_id) {
            abort(403, 'Access denied.');
        }

        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'priority' => 'nullable|in:low,normal,urgent',
            'notes' => 'nullable|string|max:1000',
        ]);

        try {
            DB::beginTransaction();

            $order = $template->createOrder([
                'customer_id' => $validated['customer_id'],
                'priority' => $validated['priority'],
                'notes' => $validated['notes'],
            ]);

            DB::commit();

            return redirect()->route('orders.show', $order)->with('success', 'Order created from template successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to create order from template: ' . $e->getMessage()]);
        }
    }

    /**
     * Quick reorder from existing order
     */
    public function quickReorder(Order $order)
    {
        $user = Auth::user();
        
        if (!$user->isDistributor() || $order->distributor_id !== $user->distributor_id) {
            abort(403, 'Access denied.');
        }

        try {
            DB::beginTransaction();

            // Create new order with same products
            $newOrder = Order::create([
                'distributor_id' => $order->distributor_id,
                'customer_id' => $order->customer_id,
                'order_number' => Order::generateOrderNumber(),
                'total_amount' => $order->total_amount,
                'order_status' => 'pending_payment',
                'payment_status' => 'unpaid',
                'priority' => $order->priority,
                'notes' => "Reordered from Order #{$order->order_number}",
            ]);

            // Copy products
            foreach ($order->products as $product) {
                $newOrder->products()->attach($product->id, [
                    'quantity' => $product->pivot->quantity,
                    'price' => $product->pivot->price,
                    'metal' => $product->pivot->metal,
                    'font' => $product->pivot->font,
                ]);
            }

            DB::commit();

            return redirect()->route('orders.show', $newOrder)->with('success', 'Order reordered successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to reorder: ' . $e->getMessage()]);
        }
    }
}
