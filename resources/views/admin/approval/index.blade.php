@extends('layouts.app')

@section('title', 'Order Approval Queue')

@section('page-title', 'Order Approval Queue')

@section('content')
<div class="row">
    <!-- Statistics Cards -->
    <div class="col-md-3 mb-4">
        <div class="card bg-warning text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4 class="mb-0">{{ $stats['pending_count'] }}</h4>
                        <p class="mb-0">Pending Orders</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-clock fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-4">
        <div class="card bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4 class="mb-0">{{ $stats['approved_today'] }}</h4>
                        <p class="mb-0">Approved Today</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-check-circle fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-4">
        <div class="card bg-info text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4 class="mb-0">{{ $stats['approved_this_week'] }}</h4>
                        <p class="mb-0">Approved This Week</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-calendar-week fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-4">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4 class="mb-0">{{ $pendingOrders->count() }}</h4>
                        <p class="mb-0">Ready for Review</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-eye fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bulk Actions -->
@if($pendingOrders->count() > 0)
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Bulk Actions</h5>
            </div>
            <div class="card-body">
                <form id="bulkApprovalForm" action="{{ route('admin.approval.bulk') }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="selectAll">
                                <label class="form-check-label" for="selectAll">
                                    <strong>Select All Orders</strong>
                                </label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <select class="form-select" name="action" required>
                                <option value="">Choose Action</option>
                                <option value="approve">Approve Selected</option>
                                <option value="reject">Reject Selected</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <textarea class="form-control" name="notes" rows="1" placeholder="Reason for approval/rejection (required)" required></textarea>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100" id="bulkSubmitBtn" disabled>
                                <i class="fas fa-play me-1"></i>Process
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Pending Orders -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Pending Orders ({{ $pendingOrders->count() }})</h5>
                <div>
                    <button class="btn btn-sm btn-outline-secondary" onclick="refreshStats()">
                        <i class="fas fa-sync-alt me-1"></i>Refresh
                    </button>
                </div>
            </div>
            <div class="card-body">
                @if($pendingOrders->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th width="50">
                                        <input type="checkbox" class="form-check-input" id="selectAllTable">
                                    </th>
                                    <th>Order #</th>
                                    <th>Distributor</th>
                                    <th>Customer</th>
                                    <th>Products</th>
                                    <th>Total Amount</th>
                                    <th>Payment Status</th>
                                    <th>Created</th>
                                    <th width="200">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pendingOrders as $order)
                                <tr>
                                    <td>
                                        <input type="checkbox" class="form-check-input order-checkbox" name="order_ids[]" value="{{ $order->id }}">
                                    </td>
                                    <td>
                                        <strong>{{ $order->order_number }}</strong>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($order->distributor->user->logo)
                                                <img src="{{ asset('storage/' . $order->distributor->user->logo) }}" 
                                                     alt="Logo" class="rounded-circle me-2" width="30" height="30">
                                            @else
                                                <div class="bg-secondary rounded-circle me-2 d-flex align-items-center justify-content-center" 
                                                     style="width: 30px; height: 30px;">
                                                    <i class="fas fa-user text-white"></i>
                                                </div>
                                            @endif
                                            <div>
                                                <div class="fw-bold">{{ $order->distributor->user->name }}</div>
                                                <small class="text-muted">{{ $order->distributor->company_name }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            <div class="fw-bold">{{ $order->customer->name }}</div>
                                            <small class="text-muted">{{ $order->customer->phone }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="small">
                                            @foreach($order->products->take(2) as $product)
                                                <div>{{ $product->name }} ({{ $product->pivot->quantity }}x)</div>
                                            @endforeach
                                            @if($order->products->count() > 2)
                                                <div class="text-muted">+{{ $order->products->count() - 2 }} more</div>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <span class="fw-bold">{{ $order->distributor->currency_symbol }}{{ number_format($order->total_amount, 2) }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $order->getPaymentStatusColor() }}">
                                            {{ $order->getPaymentStatusLabel() }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="small">
                                            <div>{{ $order->created_at->format('M d, Y') }}</div>
                                            <div class="text-muted">{{ $order->created_at->format('h:i A') }}</div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.approval.show', $order) }}" 
                                               class="btn btn-sm btn-outline-primary" title="Review Order">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-outline-success" 
                                                    onclick="quickApprove({{ $order->id }})" title="Quick Approve">
                                                <i class="fas fa-check"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-danger" 
                                                    onclick="quickReject({{ $order->id }})" title="Quick Reject">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-check-circle fa-4x text-success mb-4"></i>
                        <h5 class="text-success">No Pending Orders!</h5>
                        <p class="text-muted">All orders have been processed. Great job!</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Recently Approved Orders -->
@if($recentlyApproved->count() > 0)
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Recently Approved Orders (Last 7 Days)</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Order #</th>
                                <th>Distributor</th>
                                <th>Customer</th>
                                <th>Total Amount</th>
                                <th>Approved Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentlyApproved as $order)
                            <tr>
                                <td>
                                    <a href="{{ route('orders.show', $order) }}" class="text-decoration-none">
                                        <strong>{{ $order->order_number }}</strong>
                                    </a>
                                </td>
                                <td>{{ $order->distributor->user->name }}</td>
                                <td>{{ $order->customer->name }}</td>
                                <td>{{ $order->distributor->currency_symbol }}{{ number_format($order->total_amount, 2) }}</td>
                                <td>{{ $order->updated_at->format('M d, Y h:i A') }}</td>
                                <td>
                                    <span class="badge bg-success">Approved</span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Quick Approval Modal -->
<div class="modal fade" id="quickApprovalModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="quickModalTitle">Quick Approval</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="quickApprovalForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="quick_notes" class="form-label">Reason/Notes <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="quick_notes" name="notes" rows="3" 
                                  placeholder="Please provide a reason for approval/rejection..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="quickSubmitBtn">
                        <i class="fas fa-check me-1"></i>Confirm
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let currentOrderId = null;
let currentAction = null;

// Select all functionality
document.getElementById('selectAll').addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.order-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = this.checked;
    });
    updateBulkSubmitButton();
});

document.getElementById('selectAllTable').addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.order-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = this.checked;
    });
    document.getElementById('selectAll').checked = this.checked;
    updateBulkSubmitButton();
});

// Individual checkbox change
document.addEventListener('change', function(e) {
    if (e.target.classList.contains('order-checkbox')) {
        updateBulkSubmitButton();
        updateSelectAllCheckboxes();
    }
});

function updateBulkSubmitButton() {
    const checkedBoxes = document.querySelectorAll('.order-checkbox:checked');
    const actionSelect = document.querySelector('select[name="action"]');
    const notesTextarea = document.querySelector('textarea[name="notes"]');
    const submitBtn = document.getElementById('bulkSubmitBtn');
    
    const canSubmit = checkedBoxes.length > 0 && 
                     actionSelect.value && 
                     notesTextarea.value.trim().length > 0;
    
    submitBtn.disabled = !canSubmit;
}

function updateSelectAllCheckboxes() {
    const checkboxes = document.querySelectorAll('.order-checkbox');
    const checkedBoxes = document.querySelectorAll('.order-checkbox:checked');
    
    const selectAllCheckbox = document.getElementById('selectAll');
    const selectAllTableCheckbox = document.getElementById('selectAllTable');
    
    if (checkedBoxes.length === 0) {
        selectAllCheckbox.checked = false;
        selectAllTableCheckbox.checked = false;
    } else if (checkedBoxes.length === checkboxes.length) {
        selectAllCheckbox.checked = true;
        selectAllTableCheckbox.checked = true;
    } else {
        selectAllCheckbox.checked = false;
        selectAllTableCheckbox.checked = false;
    }
}

// Quick approval/rejection
function quickApprove(orderId) {
    currentOrderId = orderId;
    currentAction = 'approve';
    document.getElementById('quickModalTitle').textContent = 'Quick Approve Order';
    document.getElementById('quickSubmitBtn').innerHTML = '<i class="fas fa-check me-1"></i>Approve Order';
    document.getElementById('quickSubmitBtn').className = 'btn btn-success';
    document.getElementById('quickApprovalForm').action = `/approval/${orderId}`;
    
    // Add hidden input for action
    let actionInput = document.querySelector('input[name="action"]');
    if (!actionInput) {
        actionInput = document.createElement('input');
        actionInput.type = 'hidden';
        actionInput.name = 'action';
        document.getElementById('quickApprovalForm').appendChild(actionInput);
    }
    actionInput.value = 'approve';
    
    document.getElementById('quick_notes').value = '';
    new bootstrap.Modal(document.getElementById('quickApprovalModal')).show();
}

function quickReject(orderId) {
    currentOrderId = orderId;
    currentAction = 'reject';
    document.getElementById('quickModalTitle').textContent = 'Quick Reject Order';
    document.getElementById('quickSubmitBtn').innerHTML = '<i class="fas fa-times me-1"></i>Reject Order';
    document.getElementById('quickSubmitBtn').className = 'btn btn-danger';
    document.getElementById('quickApprovalForm').action = `/approval/${orderId}`;
    
    // Add hidden input for action
    let actionInput = document.querySelector('input[name="action"]');
    if (!actionInput) {
        actionInput = document.createElement('input');
        actionInput.type = 'hidden';
        actionInput.name = 'action';
        document.getElementById('quickApprovalForm').appendChild(actionInput);
    }
    actionInput.value = 'reject';
    
    document.getElementById('quick_notes').value = '';
    new bootstrap.Modal(document.getElementById('quickApprovalModal')).show();
}

// Refresh stats
function refreshStats() {
    fetch('/approval/stats')
        .then(response => response.json())
        .then(data => {
            // Update the stats cards
            document.querySelector('.bg-warning h4').textContent = data.pending_count;
            document.querySelector('.bg-success h4').textContent = data.approved_today;
            document.querySelector('.bg-info h4').textContent = data.approved_this_week;
        })
        .catch(error => {
            console.error('Error refreshing stats:', error);
        });
}

// Form validation
document.querySelector('select[name="action"]').addEventListener('change', updateBulkSubmitButton);
document.querySelector('textarea[name="notes"]').addEventListener('input', updateBulkSubmitButton);
</script>
@endpush 