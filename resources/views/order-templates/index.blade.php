@extends('layouts.app')

@section('title', 'Order Templates')

@section('page-title', 'Order Templates')

@section('content')
<div class="card shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Order Templates</h5>
        <a href="{{ route('order-templates.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Create Template
        </a>
    </div>
    <div class="card-body">
        @if($templates->count() > 0)
            <div class="row">
                @foreach($templates as $template)
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card h-100 border-0 shadow-sm">
                            <div class="card-header bg-light">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h6 class="mb-1">{{ $template->name }}</h6>
                                        @if($template->description)
                                            <small class="text-muted">{{ Str::limit($template->description, 50) }}</small>
                                        @endif
                                    </div>
                                    <span class="badge bg-{{ $template->getPriorityColor() }}">
                                        {{ $template->getPriorityLabel() }}
                                    </span>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <h6 class="text-muted mb-2">Products ({{ count($template->products) }})</h6>
                                    @foreach(array_slice($template->products, 0, 3) as $productConfig)
                                        @php
                                            $product = \App\Models\Product::find($productConfig['product_id']);
                                        @endphp
                                        @if($product)
                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                <small>{{ $product->name }}</small>
                                                <small class="text-muted">{{ $productConfig['quantity'] }}x</small>
                                            </div>
                                        @endif
                                    @endforeach
                                    @if(count($template->products) > 3)
                                        <small class="text-muted">+{{ count($template->products) - 3 }} more</small>
                                    @endif
                                </div>
                                
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <small class="text-muted">
                                        <i class="fas fa-clock me-1"></i>
                                        Used {{ $template->usage_count }} times
                                    </small>
                                    <small class="text-muted">
                                        <i class="fas fa-calendar me-1"></i>
                                        {{ $template->created_at->format('M d, Y') }}
                                    </small>
                                </div>

                                @if($template->default_notes && isset($template->default_notes['notes']))
                                    <div class="mb-3">
                                        <small class="text-muted">
                                            <i class="fas fa-sticky-note me-1"></i>
                                            {{ Str::limit($template->default_notes['notes'], 60) }}
                                        </small>
                                    </div>
                                @endif
                            </div>
                            <div class="card-footer bg-transparent">
                                <div class="btn-group w-100" role="group">
                                    <button type="button" class="btn btn-sm btn-outline-primary" 
                                            onclick="useTemplate({{ $template->id }}, '{{ $template->name }}')">
                                        <i class="fas fa-play me-1"></i>Use
                                    </button>
                                    <a href="{{ route('order-templates.show', $template) }}" class="btn btn-sm btn-outline-info">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('order-templates.edit', $template) }}" class="btn btn-sm btn-outline-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('order-templates.destroy', $template) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" 
                                                onclick="return confirm('Are you sure you want to delete this template?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-clipboard-list fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No templates found</h5>
                <p class="text-muted">Create your first order template to save time on repetitive orders.</p>
                <a href="{{ route('order-templates.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Create First Template
                </a>
            </div>
        @endif
    </div>
</div>

<!-- Use Template Modal -->
<div class="modal fade" id="useTemplateModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Use Template: <span id="templateName"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="useTemplateForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="customer_id" class="form-label">Customer *</label>
                        <select class="form-select" id="customer_id" name="customer_id" required>
                            <option value="">Select a customer</option>
                            @foreach(\App\Models\Customer::where('distributor_id', auth()->user()->distributor_id)->orderBy('name')->get() as $customer)
                                <option value="{{ $customer->id }}">{{ $customer->name }} ({{ $customer->email }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="priority" class="form-label">Priority</label>
                        <select class="form-select" id="priority" name="priority">
                            <option value="normal">Normal</option>
                            <option value="low">Low</option>
                            <option value="urgent">Urgent</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="notes" class="form-label">Additional Notes</label>
                        <textarea class="form-control" id="notes" name="notes" rows="3" placeholder="Add any additional notes for this order"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create Order</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function useTemplate(templateId, templateName) {
    document.getElementById('templateName').textContent = templateName;
    document.getElementById('useTemplateForm').action = `/order-templates/${templateId}/use`;
    
    const modal = new bootstrap.Modal(document.getElementById('useTemplateModal'));
    modal.show();
}
</script>
@endpush
@endsection 