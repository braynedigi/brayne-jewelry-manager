@extends('layouts.app')

@section('title', 'Edit Email Template')

@section('page-title', 'Edit Email Template')
@section('page-subtitle', 'Modify template content and settings')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-1">
                            <i class="fas fa-edit me-2"></i>Edit Email Template
                        </h5>
                        <p class="text-muted mb-0">Update template: {{ $emailTemplate->name }}</p>
                    </div>
                    <a href="{{ route('email-templates.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Back to List
                    </a>
                </div>
            </div>
            <div class="card-body">
                <form action="{{ route('email-templates.update', $emailTemplate) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="name" class="form-label">Template Name *</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name', $emailTemplate->name) }}" required>
                            <div class="form-text">A unique name for this template</div>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="type" class="form-label">Template Type *</label>
                            <select class="form-select @error('type') is-invalid @enderror" id="type" name="type" required>
                                <option value="">Select Template Type</option>
                                @foreach($templateTypes as $key => $label)
                                    <option value="{{ $key }}" {{ old('type', $emailTemplate->type) == $key ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="form-text">Choose the type of notification this template will be used for</div>
                            @error('type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                  id="description" name="description" rows="2">{{ old('description', $emailTemplate->description) }}</textarea>
                        <div class="form-text">Optional description of what this template is used for</div>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="subject" class="form-label">Email Subject *</label>
                        <input type="text" class="form-control @error('subject') is-invalid @enderror" 
                               id="subject" name="subject" value="{{ old('subject', $emailTemplate->subject) }}" required>
                        <div class="form-text">The subject line of the email. You can use variables like {{order_number}}</div>
                        @error('subject')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="content" class="form-label">Email Content *</label>
                        <div class="mb-2">
                            <div class="btn-group" role="group">
                                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="insertVariable('order_number')">
                                    Order Number
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="insertVariable('customer_name')">
                                    Customer Name
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="insertVariable('old_status')">
                                    Old Status
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="insertVariable('new_status')">
                                    New Status
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="insertVariable('total_amount')">
                                    Total Amount
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="insertVariable('estimated_delivery')">
                                    Estimated Delivery
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="insertVariable('user_name')">
                                    User Name
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="insertVariable('system_name')">
                                    System Name
                                </button>
                            </div>
                        </div>
                        <textarea class="form-control @error('content') is-invalid @enderror" 
                                  id="content" name="content" rows="15" required>{{ old('content', $emailTemplate->content) }}</textarea>
                        <div class="form-text">
                            Write your email content here. Use variables like {{order_number}} to insert dynamic data. 
                            You can use HTML formatting.
                        </div>
                        @error('content')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" 
                                   {{ old('is_active', $emailTemplate->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">
                                Active Template
                            </label>
                            <div class="form-text">Only active templates will be used for sending emails</div>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('email-templates.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times me-2"></i>Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Update Template
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Sample Template Modal -->
<div class="modal fade" id="sampleTemplateModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-file-alt me-2"></i>Sample Template
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="sampleTemplateContent"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="loadSampleTemplate()">Load Template</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function insertVariable(variable) {
    const textarea = document.getElementById('content');
    const cursorPos = textarea.selectionStart;
    const textBefore = textarea.value.substring(0, cursorPos);
    const textAfter = textarea.value.substring(cursorPos);
    const variableText = '{{' + variable + '}}';
    
    textarea.value = textBefore + variableText + textAfter;
    textarea.focus();
    textarea.setSelectionRange(cursorPos + variableText.length, cursorPos + variableText.length);
}

function loadSampleTemplate() {
    const type = document.getElementById('type').value;
    if (!type) {
        alert('Please select a template type first.');
        return;
    }
    
    const sampleTemplates = {
        'order_status_updated': {
            subject: 'Order #{{order_number}} Status Updated - {{new_status}}',
            content: `<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Order Status Update</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="background: #f8f9fa; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
        <h2 style="color: #007bff; margin-top: 0;">Order Status Update</h2>
        <p>Hello {{user_name}},</p>
        <p>The status of order <strong>#{{order_number}}</strong> has been updated.</p>
        
        <div style="background: white; padding: 15px; border-radius: 5px; margin: 15px 0;">
            <p><strong>Previous Status:</strong> {{old_status}}</p>
            <p><strong>New Status:</strong> <span style="color: #28a745; font-weight: bold;">{{new_status}}</span></p>
            <p><strong>Customer:</strong> {{customer_name}}</p>
            <p><strong>Total Amount:</strong> ${{total_amount}}</p>
            <p><strong>Estimated Delivery:</strong> {{estimated_delivery}}</p>
        </div>
        
        <p>Thank you for using {{system_name}}!</p>
    </div>
    
    <div style="text-align: center; color: #6c757d; font-size: 12px;">
        <p>This is an automated notification from {{system_name}}.</p>
    </div>
</body>
</html>`
        },
        'order_created': {
            subject: 'New Order #{{order_number}} Created',
            content: `<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>New Order Created</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="background: #f8f9fa; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
        <h2 style="color: #28a745; margin-top: 0;">New Order Created</h2>
        <p>Hello {{user_name}},</p>
        <p>A new order has been created in the system.</p>
        
        <div style="background: white; padding: 15px; border-radius: 5px; margin: 15px 0;">
            <h3 style="margin-top: 0; color: #007bff;">Order Details</h3>
            <p><strong>Order Number:</strong> #{{order_number}}</p>
            <p><strong>Customer:</strong> {{customer_name}}</p>
            <p><strong>Distributor:</strong> {{distributor_name}}</p>
            <p><strong>Status:</strong> <span style="color: #007bff; font-weight: bold;">{{status}}</span></p>
            <p><strong>Priority:</strong> <span style="color: #dc3545; font-weight: bold;">{{priority}}</span></p>
            <p><strong>Total Amount:</strong> ${{total_amount}}</p>
            <p><strong>Estimated Delivery:</strong> {{estimated_delivery}}</p>
        </div>
        
        <p>Please review and process this order accordingly.</p>
        <p>Thank you for using {{system_name}}!</p>
    </div>
    
    <div style="text-align: center; color: #6c757d; font-size: 12px;">
        <p>This is an automated notification from {{system_name}}.</p>
    </div>
</body>
</html>`
        }
    };
    
    const template = sampleTemplates[type];
    if (template) {
        document.getElementById('subject').value = template.subject;
        document.getElementById('content').value = template.content;
        bootstrap.Modal.getInstance(document.getElementById('sampleTemplateModal')).hide();
    } else {
        alert('No sample template available for this type.');
    }
}

// Show sample template modal when type changes
document.getElementById('type').addEventListener('change', function() {
    const type = this.value;
    if (type) {
        const sampleTemplates = {
            'order_status_updated': 'Order Status Update Template',
            'order_created': 'New Order Template',
            'customer_created': 'Customer Created Template',
            'product_created': 'Product Created Template',
            'general': 'General Notification Template'
        };
        
        if (sampleTemplates[type]) {
            document.getElementById('sampleTemplateContent').innerHTML = 
                '<p>Would you like to load a sample template for <strong>' + sampleTemplates[type] + '</strong>?</p>';
            new bootstrap.Modal(document.getElementById('sampleTemplateModal')).show();
        }
    }
});
</script>
@endpush 