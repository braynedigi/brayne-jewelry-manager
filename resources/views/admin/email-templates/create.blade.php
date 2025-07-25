@extends('layouts.app')

@section('title', 'Create Email Template')

@section('page-title', 'Create Email Template')
@section('page-subtitle', 'Design a new email notification template')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-plus me-2"></i>Create New Email Template
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('email-templates.store') }}" method="POST">
                    @csrf
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="name" class="form-label">Template Name *</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name') }}" required>
                            <div class="form-text">A unique name for this template (e.g., "Order Status Update")</div>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="type" class="form-label">Template Type *</label>
                            <select class="form-select @error('type') is-invalid @enderror" id="type" name="type" required>
                                <option value="">Select Template Type</option>
                                @foreach($templateTypes as $key => $label)
                                    <option value="{{ $key }}" {{ old('type') == $key ? 'selected' : '' }}>
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
                                  id="description" name="description" rows="2">{{ old('description') }}</textarea>
                        <div class="form-text">Optional description of what this template is used for</div>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="subject" class="form-label">Email Subject *</label>
                        <input type="text" class="form-control @error('subject') is-invalid @enderror" 
                               id="subject" name="subject" value="{{ old('subject') }}" required>
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
                            </div>
                        </div>
                        <textarea class="form-control @error('content') is-invalid @enderror" 
                                  id="content" name="content" rows="15" required>{{ old('content') }}</textarea>
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
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" 
                                   {{ old('is_active', true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">
                                Active Template
                            </label>
                        </div>
                        <div class="form-text">Inactive templates won't be used for sending emails</div>
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.email-templates.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Back to Templates
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Create Template
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
                <div class="mb-3">
                    <label class="form-label fw-bold">Subject:</label>
                    <div id="sampleSubject" class="p-2 bg-light rounded"></div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Content:</label>
                    <div id="sampleContent" class="p-3 bg-light rounded" style="max-height: 400px; overflow-y: auto;"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="useSampleTemplate()">Use This Template</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function insertVariable(variable) {
    const textarea = document.getElementById('content');
    const variableText = '{{' + variable + '}}';
    
    // Get cursor position
    const start = textarea.selectionStart;
    const end = textarea.selectionEnd;
    
    // Insert variable at cursor position
    const text = textarea.value;
    const before = text.substring(0, start);
    const after = text.substring(end);
    
    textarea.value = before + variableText + after;
    
    // Set cursor position after inserted variable
    const newCursorPos = start + variableText.length;
    textarea.setSelectionRange(newCursorPos, newCursorPos);
    
    // Focus back to textarea
    textarea.focus();
}

// Update available variables when template type changes
document.getElementById('type').addEventListener('change', function() {
    const type = this.value;
    const variableButtons = document.querySelectorAll('.btn-group .btn');
    
    // Hide all variable buttons first
    variableButtons.forEach(btn => btn.style.display = 'none');
    
    // Show relevant variables based on type
    if (type) {
        const variables = @json($defaultVariables);
        const typeVariables = variables[type] || [];
        
        variableButtons.forEach(btn => {
            const variable = btn.textContent.trim().toLowerCase().replace(/\s+/g, '_');
            if (typeVariables.includes(variable)) {
                btn.style.display = 'inline-block';
            }
        });
    }
});

// Load sample template when type is selected
document.getElementById('type').addEventListener('change', function() {
    const type = this.value;
    if (type) {
        loadSampleTemplate(type);
    }
});

function loadSampleTemplate(type) {
    const samples = {
        'order_status': {
            subject: 'Order Status Updated - {{order_number}}',
            content: `<h2>Order Status Updated</h2>
<p>Hello,</p>
<p>The status of order <strong>{{order_number}}</strong> has been updated.</p>

<div style="background-color: #f8f9fa; padding: 20px; border-radius: 8px; margin: 20px 0;">
    <h3>Order Information</h3>
    <p><strong>Order Number:</strong> {{order_number}}</p>
    <p><strong>Customer:</strong> {{customer_name}}</p>
    <p><strong>Previous Status:</strong> {{old_status}}</p>
    <p><strong>New Status:</strong> {{new_status}}</p>
    <p><strong>Estimated Delivery:</strong> {{estimated_delivery}}</p>
    <p><strong>Total Amount:</strong> {{total_amount}}</p>
</div>

<p>You can view the complete order details by clicking the button below:</p>
<a href="#" style="display: inline-block; padding: 12px 24px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; text-decoration: none; border-radius: 6px; font-weight: 500; margin: 20px 0;">View Order Details</a>

<p>If you have any questions, please contact your administrator.</p>
<p>Best regards,<br>Jewelry Manager Team</p>`
        },
        'order_created': {
            subject: 'New Order Created - {{order_number}}',
            content: `<h2>New Order Created</h2>
<p>Hello,</p>
<p>A new order <strong>{{order_number}}</strong> has been created in the system.</p>

<div style="background-color: #f8f9fa; padding: 20px; border-radius: 8px; margin: 20px 0;">
    <h3>Order Information</h3>
    <p><strong>Order Number:</strong> {{order_number}}</p>
    <p><strong>Customer:</strong> {{customer_name}}</p>
    <p><strong>Distributor:</strong> {{distributor_name}}</p>
    <p><strong>Status:</strong> {{status}}</p>
    <p><strong>Priority:</strong> {{priority}}</p>
    <p><strong>Total Amount:</strong> {{total_amount}}</p>
    <p><strong>Estimated Delivery:</strong> {{estimated_delivery}}</p>
</div>

<p>Please review and process this order according to your workflow.</p>
<p>Best regards,<br>Jewelry Manager Team</p>`
        },
        'general': {
            subject: '{{title}}',
            content: `<h2>{{title}}</h2>
<p>Hello {{user_name}},</p>
<p>{{message}}</p>
<p>Best regards,<br>{{system_name}} Team</p>`
        }
    };
    
    const sample = samples[type];
    if (sample) {
        document.getElementById('sampleSubject').textContent = sample.subject;
        document.getElementById('sampleContent').innerHTML = sample.content;
        
        // Show sample template modal
        new bootstrap.Modal(document.getElementById('sampleTemplateModal')).show();
    }
}

function useSampleTemplate() {
    const subject = document.getElementById('sampleSubject').textContent;
    const content = document.getElementById('sampleContent').innerHTML;
    
    document.getElementById('subject').value = subject;
    document.getElementById('content').value = content;
    
    bootstrap.Modal.getInstance(document.getElementById('sampleTemplateModal')).hide();
}
</script>
@endpush 