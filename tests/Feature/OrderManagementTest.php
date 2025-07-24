<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Order;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Distributor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

class OrderManagementTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $admin;
    protected $distributor;
    protected $factory;
    protected $customer;
    protected $product;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create test users
        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->distributor = User::factory()->create(['role' => 'distributor']);
        $this->factory = User::factory()->create(['role' => 'factory']);
        
        // Create distributor profile
        $this->distributor->distributor()->create([
            'company_name' => 'Test Distributor',
            'phone' => '+1234567890',
            'street' => '123 Test St',
            'city' => 'Test City',
            'province' => 'Test Province',
            'country' => 'Test Country',
        ]);
        
        // Create test customer
        $this->customer = Customer::factory()->create([
            'distributor_id' => $this->distributor->distributor->id,
        ]);
        
        // Create test product
        $this->product = Product::factory()->create([
            'name' => 'Test Product',
            'sku' => 'TEST-001',
            'category' => 'Rings',
            'local_pricing' => ['Stainless' => 1000.00],
            'international_pricing' => ['Stainless' => 20.00],
            'metals' => ['Stainless'],
            'fonts' => ['Arial'],
            'font_requirement' => 1,
            'is_active' => true,
        ]);
    }

    /** @test */
    public function distributor_can_create_order()
    {
        $this->actingAs($this->distributor);

        $orderData = [
            'customer_id' => $this->customer->id,
            'total_amount' => 1000.00,
            'payment_status' => 'unpaid',
            'order_status' => 'pending_payment',
            'notes' => 'Test order',
            'products' => [
                [
                    'product_id' => $this->product->id,
                    'quantity' => 1,
                    'price' => 1000.00,
                    'metal' => 'Stainless',
                    'font' => 'Arial'
                ]
            ]
        ];

        $response = $this->post('/orders', $orderData);

        $response->assertRedirect();
        $this->assertDatabaseHas('orders', [
            'distributor_id' => $this->distributor->distributor->id,
            'customer_id' => $this->customer->id,
            'total_amount' => 1000.00,
            'payment_status' => 'unpaid',
            'order_status' => 'pending_payment'
        ]);
    }

    /** @test */
    public function admin_can_approve_order()
    {
        $order = Order::factory()->create([
            'distributor_id' => $this->distributor->distributor->id,
            'customer_id' => $this->customer->id,
            'order_status' => 'pending_payment',
            'payment_status' => 'partially_paid'
        ]);

        $this->actingAs($this->admin);

        $response = $this->put("/orders/{$order->id}/status", [
            'order_status' => 'approved'
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'order_status' => 'approved'
        ]);
    }

    /** @test */
    public function factory_can_update_production_status()
    {
        $order = Order::factory()->create([
            'distributor_id' => $this->distributor->distributor->id,
            'customer_id' => $this->customer->id,
            'order_status' => 'approved'
        ]);

        $this->actingAs($this->factory);

        $response = $this->put("/orders/{$order->id}/status", [
            'order_status' => 'in_production'
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'order_status' => 'in_production'
        ]);
    }

    /** @test */
    public function factory_cannot_see_financial_information()
    {
        $order = Order::factory()->create([
            'distributor_id' => $this->distributor->distributor->id,
            'customer_id' => $this->customer->id,
            'total_amount' => 5000.00,
            'payment_status' => 'fully_paid'
        ]);

        $this->actingAs($this->factory);

        $response = $this->get("/factory/queue");

        $response->assertStatus(200);
        $response->assertDontSee('5000.00');
        $response->assertDontSee('fully_paid');
    }

    /** @test */
    public function order_status_flow_is_valid()
    {
        $order = Order::factory()->create([
            'distributor_id' => $this->distributor->distributor->id,
            'customer_id' => $this->customer->id,
            'order_status' => 'pending_payment'
        ]);

        $validStatuses = [
            'pending_payment' => ['approved', 'cancelled'],
            'approved' => ['in_production', 'cancelled'],
            'in_production' => ['finishing', 'cancelled'],
            'finishing' => ['ready_for_delivery', 'cancelled'],
            'ready_for_delivery' => ['delivered_to_brayne', 'cancelled'],
            'delivered_to_brayne' => ['delivered_to_client', 'cancelled'],
            'delivered_to_client' => [],
            'cancelled' => []
        ];

        foreach ($validStatuses as $currentStatus => $allowedNextStatuses) {
            $order->update(['order_status' => $currentStatus]);
            
            foreach ($allowedNextStatuses as $nextStatus) {
                $this->actingAs($this->admin);
                $response = $this->put("/orders/{$order->id}/status", [
                    'order_status' => $nextStatus
                ]);
                
                if ($nextStatus !== 'cancelled') {
                    $response->assertRedirect();
                    $this->assertDatabaseHas('orders', [
                        'id' => $order->id,
                        'order_status' => $nextStatus
                    ]);
                }
            }
        }
    }

    /** @test */
    public function payment_status_affects_order_approval()
    {
        $order = Order::factory()->create([
            'distributor_id' => $this->distributor->distributor->id,
            'customer_id' => $this->customer->id,
            'order_status' => 'pending_payment',
            'payment_status' => 'unpaid'
        ]);

        $this->actingAs($this->admin);

        // Should not be able to approve unpaid order
        $response = $this->put("/orders/{$order->id}/status", [
            'order_status' => 'approved'
        ]);

        $response->assertSessionHasErrors();
        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'order_status' => 'pending_payment'
        ]);

        // Update payment status
        $order->update(['payment_status' => 'partially_paid']);

        // Should be able to approve partially paid order
        $response = $this->put("/orders/{$order->id}/status", [
            'order_status' => 'approved'
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'order_status' => 'approved'
        ]);
    }

    /** @test */
    public function order_templates_work_correctly()
    {
        $this->actingAs($this->distributor);

        // Create an order first
        $order = Order::factory()->create([
            'distributor_id' => $this->distributor->distributor->id,
            'customer_id' => $this->customer->id,
        ]);

        // Create template from order
        $templateData = [
            'name' => 'Test Template',
            'description' => 'Test template description',
            'products' => [
                [
                    'product_id' => $this->product->id,
                    'quantity' => 1,
                    'price' => 1000.00,
                    'metal' => 'Stainless',
                    'font' => 'Arial'
                ]
            ]
        ];

        $response = $this->post("/orders/{$order->id}/create-template", $templateData);

        $response->assertRedirect();
        $this->assertDatabaseHas('order_templates', [
            'name' => 'Test Template',
            'distributor_id' => $this->distributor->distributor->id
        ]);
    }

    /** @test */
    public function order_priority_affects_production_queue()
    {
        $order1 = Order::factory()->create([
            'distributor_id' => $this->distributor->distributor->id,
            'customer_id' => $this->customer->id,
            'order_status' => 'approved',
            'priority' => 'normal'
        ]);

        $order2 = Order::factory()->create([
            'distributor_id' => $this->distributor->distributor->id,
            'customer_id' => $this->customer->id,
            'order_status' => 'approved',
            'priority' => 'high'
        ]);

        $this->actingAs($this->factory);

        $response = $this->get("/factory/queue");

        $response->assertStatus(200);
        // High priority orders should appear first
        $response->assertSeeInOrder([$order2->order_number, $order1->order_number]);
    }
} 