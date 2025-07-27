<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Order;
use App\Models\Customer;
use App\Models\ProductCategory;
use App\Models\ProductMetal;
use App\Models\ProductStone;
use App\Models\ProductFont;
use App\Models\RingSize;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\UploadedFile;
use App\Services\CsvHandler;

class ImportExportService
{
    /**
     * Export products to CSV
     */
    public function exportProducts(): string
    {
        $products = Product::with(['categoryRelation'])->get();
        
        // Headers
        $headers = [
            'ID', 'Name', 'SKU', 'Category', 'Sub Category', 'Custom Sub Category',
            'Metals', 'Local Pricing', 'International Pricing', 'Fonts', 'Font Requirement',
            'Stones', 'Requires Stones', 'Requires Ring Size', 'Is Active', 'Image URL',
            'Description', 'Created At', 'Updated At'
        ];
        
        $data = [];
        foreach ($products as $product) {
            $data[] = [
                $product->id,
                $product->name,
                $product->sku,
                $product->category,
                $product->sub_category,
                $product->custom_sub_category,
                is_array($product->metals) ? implode('|', $product->metals) : $product->metals,
                is_array($product->local_pricing) ? json_encode($product->local_pricing) : $product->local_pricing,
                is_array($product->international_pricing) ? json_encode($product->international_pricing) : $product->international_pricing,
                is_array($product->fonts) ? implode('|', $product->fonts) : $product->fonts,
                $product->font_requirement,
                is_array($product->stones) ? implode('|', $product->stones) : $product->stones,
                $product->requires_stones ? 'Yes' : 'No',
                $product->requires_ring_size ? 'Yes' : 'No',
                $product->is_active ? 'Yes' : 'No',
                $product->image,
                $product->description,
                $product->created_at,
                $product->updated_at
            ];
        }
        
        return CsvHandler::write($data, $headers);
    }

    /**
     * Import products from CSV
     */
    public function importProducts(UploadedFile $file): array
    {
        $results = [
            'success' => 0,
            'errors' => [],
            'total' => 0
        ];

        try {
            $records = CsvHandler::read($file->getPathname());
            
            DB::beginTransaction();
            
            foreach ($records as $index => $record) {
                $results['total']++;
                
                try {
                    $this->importProduct($record);
                    $results['success']++;
                } catch (\Exception $e) {
                    $results['errors'][] = "Row " . ($index + 2) . ": " . $e->getMessage();
                }
            }
            
            DB::commit();
            
        } catch (\Exception $e) {
            DB::rollBack();
            $results['errors'][] = "File processing error: " . $e->getMessage();
        }
        
        return $results;
    }

    /**
     * Import a single product record
     */
    protected function importProduct(array $record): void
    {
        // Validate required fields
        $validator = Validator::make($record, [
            'Name' => 'required|string|max:255',
            'SKU' => 'required|string|max:100|unique:products,sku',
            'Category' => 'nullable|string|max:255',
            'Metals' => 'nullable|string',
            'Local Pricing' => 'nullable|string',
            'International Pricing' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            throw new \Exception($validator->errors()->first());
        }

        // Parse arrays from strings
        $metals = !empty($record['Metals']) ? explode('|', $record['Metals']) : [];
        $localPricing = !empty($record['Local Pricing']) ? json_decode($record['Local Pricing'], true) : [];
        $internationalPricing = !empty($record['International Pricing']) ? json_decode($record['International Pricing'], true) : [];
        $fonts = !empty($record['Fonts']) ? explode('|', $record['Fonts']) : [];
        $stones = !empty($record['Stones']) ? explode('|', $record['Stones']) : [];

        Product::create([
            'name' => $record['Name'],
            'sku' => $record['SKU'],
            'category' => $record['Category'] ?? null,
            'sub_category' => $record['Sub Category'] ?? null,
            'custom_sub_category' => $record['Custom Sub Category'] ?? null,
            'metals' => $metals,
            'local_pricing' => $localPricing,
            'international_pricing' => $internationalPricing,
            'fonts' => $fonts,
            'font_requirement' => $record['Font Requirement'] ?? 0,
            'stones' => $stones,
            'requires_stones' => strtolower($record['Requires Stones'] ?? 'No') === 'yes',
            'requires_ring_size' => strtolower($record['Requires Ring Size'] ?? 'No') === 'yes',
            'is_active' => strtolower($record['Is Active'] ?? 'Yes') === 'yes',
            'image' => $record['Image URL'] ?? null,
            'description' => $record['Description'] ?? null,
        ]);
    }

    /**
     * Export customers to CSV
     */
    public function exportCustomers(): string
    {
        $customers = Customer::all();
        
        // Headers
        $headers = [
            'ID', 'Name', 'Email', 'Phone', 'Address', 'City', 'State', 
            'Postal Code', 'Country', 'Created At', 'Updated At'
        ];
        
        $data = [];
        foreach ($customers as $customer) {
            $data[] = [
                $customer->id,
                $customer->name,
                $customer->email,
                $customer->phone,
                $customer->address,
                $customer->city,
                $customer->state,
                $customer->postal_code,
                $customer->country,
                $customer->created_at,
                $customer->updated_at
            ];
        }
        
        return CsvHandler::write($data, $headers);
    }

    /**
     * Import customers from CSV
     */
    public function importCustomers(UploadedFile $file): array
    {
        $results = [
            'success' => 0,
            'errors' => [],
            'total' => 0
        ];

        try {
            $records = CsvHandler::read($file->getPathname());
            
            DB::beginTransaction();
            
            foreach ($records as $index => $record) {
                $results['total']++;
                
                try {
                    $this->importCustomer($record);
                    $results['success']++;
                } catch (\Exception $e) {
                    $results['errors'][] = "Row " . ($index + 2) . ": " . $e->getMessage();
                }
            }
            
            DB::commit();
            
        } catch (\Exception $e) {
            DB::rollBack();
            $results['errors'][] = "File processing error: " . $e->getMessage();
        }
        
        return $results;
    }

    /**
     * Import a single customer record
     */
    protected function importCustomer(array $record): void
    {
        $validator = Validator::make($record, [
            'Name' => 'required|string|max:255',
            'Email' => 'nullable|email|max:255|unique:customers,email',
            'Phone' => 'nullable|string|max:20',
        ]);

        if ($validator->fails()) {
            throw new \Exception($validator->errors()->first());
        }

        Customer::create([
            'name' => $record['Name'],
            'email' => $record['Email'] ?? null,
            'phone' => $record['Phone'] ?? null,
            'address' => $record['Address'] ?? '',
            'city' => $record['City'] ?? '',
            'state' => $record['State'] ?? '',
            'postal_code' => $record['Postal Code'] ?? '',
            'country' => $record['Country'] ?? '',
        ]);
    }

    /**
     * Export orders to CSV
     */
    public function exportOrders(): string
    {
        $orders = Order::with(['distributor', 'customer', 'courier', 'products'])->get();
        
        // Headers
        $headers = [
            'Order ID', 'Order Number', 'Distributor', 'Customer', 'Courier',
            'Total Amount', 'Payment Status', 'Order Status', 'Priority',
            'Notes', 'Created At', 'Updated At'
        ];
        
        $data = [];
        foreach ($orders as $order) {
            $data[] = [
                $order->id,
                $order->order_number,
                $order->distributor->company_name ?? '',
                $order->customer->name ?? '',
                $order->courier->name ?? '',
                $order->total_amount,
                $order->payment_status,
                $order->order_status,
                $order->priority,
                $order->notes,
                $order->created_at,
                $order->updated_at
            ];
        }
        
        return CsvHandler::write($data, $headers);
    }

    /**
     * Generate template CSV for products
     */
    public function generateProductTemplate(): string
    {
        $headers = [
            'Name', 'SKU', 'Category', 'Sub Category', 'Custom Sub Category',
            'Metals', 'Local Pricing', 'International Pricing', 'Fonts', 'Font Requirement',
            'Stones', 'Requires Stones', 'Requires Ring Size', 'Is Active', 'Image URL', 'Description'
        ];
        
        $data = [
            [
                'Gold Wedding Ring',
                'RING-001',
                'Rings',
                'Wedding Rings',
                '',
                'Gold|Silver|Platinum',
                '{"Gold": 299.99, "Silver": 199.99, "Platinum": 499.99}',
                '{"Gold": 359.99, "Silver": 239.99, "Platinum": 599.99}',
                'Arial|Times New Roman|Script',
                '1',
                'Diamond|Ruby|Sapphire',
                'Yes',
                'Yes',
                'Yes',
                'https://example.com/ring-image.jpg',
                'Beautiful gold wedding ring with customizable options'
            ]
        ];
        
        return CsvHandler::write($data, $headers);
    }

    /**
     * Generate template CSV for customers
     */
    public function generateCustomerTemplate(): string
    {
        $headers = [
            'Name', 'Email', 'Phone', 'Address', 'City', 'State', 'Postal Code', 'Country'
        ];
        
        $data = [
            [
                'John Doe',
                'john.doe@example.com',
                '+1234567890',
                '123 Main Street',
                'New York',
                'NY',
                '10001',
                'USA'
            ]
        ];
        
        return CsvHandler::write($data, $headers);
    }

    /**
     * Get import statistics
     */
    public function getImportStats(): array
    {
        return [
            'products' => Product::count(),
            'customers' => Customer::count(),
            'orders' => Order::count(),
        ];
    }
} 