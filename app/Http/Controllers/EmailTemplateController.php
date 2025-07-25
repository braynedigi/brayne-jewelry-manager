<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EmailTemplate;

class EmailTemplateController extends Controller
{
    public function index()
    {
        $templates = EmailTemplate::latest()->get();
        return view('admin.email-templates.index', compact('templates'));
    }

    public function create()
    {
        $templateTypes = [
            'order_status' => 'Order Status Updates',
            'order_created' => 'New Order Created',
            'customer_created' => 'New Customer Added',
            'product_created' => 'New Product Added',
            'general' => 'General Notifications'
        ];

        $defaultVariables = [
            'order_status' => ['order_number', 'customer_name', 'old_status', 'new_status', 'estimated_delivery', 'total_amount', 'production_notes'],
            'order_created' => ['order_number', 'customer_name', 'distributor_name', 'status', 'priority', 'total_amount', 'estimated_delivery'],
            'customer_created' => ['customer_name', 'customer_email', 'distributor_name'],
            'product_created' => ['product_name', 'product_category', 'admin_name'],
            'general' => ['title', 'message', 'user_name', 'system_name']
        ];

        return view('admin.email-templates.create', compact('templateTypes', 'defaultVariables'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:email_templates',
            'subject' => 'required|string|max:255',
            'content' => 'required|string',
            'type' => 'required|string|max:50',
            'variables' => 'nullable|array',
            'description' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        $validated['is_active'] = $request->has('is_active');

        EmailTemplate::create($validated);

        return redirect()->route('email-templates.index')
            ->with('success', 'Email template created successfully!');
    }

    public function show(EmailTemplate $emailTemplate)
    {
        return view('admin.email-templates.show', compact('emailTemplate'));
    }

    public function edit(EmailTemplate $emailTemplate)
    {
        $templateTypes = [
            'order_status' => 'Order Status Updates',
            'order_created' => 'New Order Created',
            'customer_created' => 'New Customer Added',
            'product_created' => 'New Product Added',
            'general' => 'General Notifications'
        ];

        $defaultVariables = [
            'order_status' => ['order_number', 'customer_name', 'old_status', 'new_status', 'estimated_delivery', 'total_amount', 'production_notes'],
            'order_created' => ['order_number', 'customer_name', 'distributor_name', 'status', 'priority', 'total_amount', 'estimated_delivery'],
            'customer_created' => ['customer_name', 'customer_email', 'distributor_name'],
            'product_created' => ['product_name', 'product_category', 'admin_name'],
            'general' => ['title', 'message', 'user_name', 'system_name']
        ];

        return view('admin.email-templates.edit', compact('emailTemplate', 'templateTypes', 'defaultVariables'));
    }

    public function update(Request $request, EmailTemplate $emailTemplate)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:email_templates,name,' . $emailTemplate->id,
            'subject' => 'required|string|max:255',
            'content' => 'required|string',
            'type' => 'required|string|max:50',
            'variables' => 'nullable|array',
            'description' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        $validated['is_active'] = $request->has('is_active');

        $emailTemplate->update($validated);

        return redirect()->route('email-templates.index')
            ->with('success', 'Email template updated successfully!');
    }

    public function destroy(EmailTemplate $emailTemplate)
    {
        $emailTemplate->delete();

        return redirect()->route('email-templates.index')
            ->with('success', 'Email template deleted successfully!');
    }

    /**
     * Preview email template with sample data
     */
    public function preview(Request $request, EmailTemplate $emailTemplate)
    {
        $sampleData = $this->getSampleData($emailTemplate->type);
        $rendered = $emailTemplate->render($sampleData);

        return response()->json([
            'subject' => $rendered['subject'],
            'content' => $rendered['content'],
            'sample_data' => $sampleData
        ]);
    }

    /**
     * Test email template by sending to specified email
     */
    public function test(Request $request, EmailTemplate $emailTemplate)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        try {
            $sampleData = $this->getSampleData($emailTemplate->type);
            $rendered = $emailTemplate->render($sampleData);

            // Send test email
            \Mail::to($request->email)->send(new \App\Mail\GeneralNotification(
                $rendered['subject'],
                $rendered['content'],
                'info'
            ));

            return response()->json(['success' => true, 'message' => 'Test email sent successfully!']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to send test email: ' . $e->getMessage()]);
        }
    }

    /**
     * Get sample data for template preview
     */
    private function getSampleData($type)
    {
        $sampleData = [
            'order_status' => [
                'order_number' => 'ORD-2025-001',
                'customer_name' => 'John Doe',
                'old_status' => 'In Production',
                'new_status' => 'Finishing',
                'estimated_delivery' => '2025-08-15',
                'total_amount' => '$1,250.00',
                'production_notes' => 'Quality check completed, ready for finishing process.'
            ],
            'order_created' => [
                'order_number' => 'ORD-2025-002',
                'customer_name' => 'Jane Smith',
                'distributor_name' => 'ABC Distributors',
                'status' => 'Pending Payment',
                'priority' => 'High',
                'total_amount' => '$2,500.00',
                'estimated_delivery' => '2025-08-20'
            ],
            'customer_created' => [
                'customer_name' => 'Mike Johnson',
                'customer_email' => 'mike@example.com',
                'distributor_name' => 'XYZ Distributors'
            ],
            'product_created' => [
                'product_name' => 'Diamond Ring Collection',
                'product_category' => 'Rings',
                'admin_name' => 'Admin User'
            ],
            'general' => [
                'title' => 'System Notification',
                'message' => 'This is a sample notification message.',
                'user_name' => 'Test User',
                'system_name' => 'Jewelry Manager'
            ]
        ];

        return $sampleData[$type] ?? [];
    }
}
