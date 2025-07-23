@extends('layouts.app')

@section('title', 'Production Queue')

@section('page-title', 'Production Queue')
@section('page-subtitle', 'Manage and filter production orders')

@section('content')
<!-- Filters -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-filter me-2"></i>Filters
                </h5>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('factory.queue') }}" class="row g-3">
                    <div class="col-md-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" name="status" id="status">
                            <option value="">All Statuses</option>
                            <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Queue</option>
                            <option value="in_production" {{ request('status') === 'in_production' ? 'selected' : '' }}>In Production</option>
                            <option value="finishing" {{ request('status') === 'finishing' ? 'selected' : '' }}>Finishing</option>
                            <option value="ready_for_delivery" {{ request('status') === 'ready_for_delivery' ? 'selected' : '' }}>Ready for Delivery</option>
                        </select>
                    </div>
                    
                    <div class="col-md-3">
                        <label for="priority" class="form-label">Priority</label>
                        <select class="form-select" name="priority" id="priority">
                            <option value="">All Priorities</option>
                            <option value="urgent" {{ request('priority') === 'urgent' ? 'selected' : '' }}>Urgent</option>
                            <option value="normal" {{ request('priority') === 'normal' ? 'selected' : '' }}>Normal</option>
                            <option value="low" {{ request('priority') === 'low' ? 'selected' : '' }}>Low</option>
                        </select>
                    </div>
                    
                    <div class="col-md-2">
                        <label class="form-label">&nbsp;</label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="overdue" id="overdue" value="1" {{ request('overdue') ? 'checked' : '' }}>
                            <label class="form-check-label" for="overdue">
                                Overdue Only
                            </label>
                        </div>
                    </div>
                    
                    <div class="col-md-2">
                        <label class="form-label">&nbsp;</label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="due_today" id="due_today" value="1" {{ request('due_today') ? 'checked' : '' }}>
                            <label class="form-check-label" for="due_today">
                                Due Today
                            </label>
                        </div>
                    </div>
                    
                    <div class="col-md-2">
                        <label class="form-label">&nbsp;</label>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search me-1"></i>Filter
                            </button>
                            <a href="{{ route('factory.queue') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-1"></i>Clear
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Orders Table -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-list me-2"></i>Production Orders ({{ $orders->total() }})
                </h5>
                <div>
                    <a href="{{ route('factory.dashboard') }}" class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-tachometer-alt me-1"></i>Dashboard
                    </a>
                </div>
            </div>
            <div class="card-body">
                @if($orders->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Order #</th>
                                    <th>Customer</th>
                                    <th>Distributor</th>
                                    <th>Priority</th>
                                    <th>Status</th>
                                    <th>Progress</th>
                                    <th>Due Date</th>
                                    <th>Hours</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($orders as $order)
                                <tr class="{{ $order->priority === 'urgent' ? 'table-danger' : '' }} {{ $order->isOverdue() ? 'table-warning' : '' }}">
                                    <td>
                                        <div class="fw-bold">{{ $order->order_number }}</div>
                                        <small class="text-muted">{{ $order->created_at->format('M d, Y') }}</small>
                                    </td>
                                    <td>
                                        <div class="fw-bold">{{ $order->customer->name }}</div>
                                        <small class="text-muted">{{ $order->customer->phone }}</small>
                                    </td>
                                    <td>
                                        <div class="fw-bold">{{ $order->distributor->company_name }}</div>
                                        <small class="text-muted">{{ $order->distributor->user->name }}</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $order->getPriorityColor() }}">
                                            <i class="{{ $order->getPriorityIcon() }} me-1"></i>{{ $order->getPriorityLabel() }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $order->getOrderStatusColor() }}">
                                            {{ $order->getOrderStatusLabel() }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="progress flex-grow-1 me-2" style="height: 6px;">
                                                <div class="progress-bar bg-{{ $order->getOrderStatusColor() }}" 
                                                     style="width: {{ $order->getProductionProgress() }}%"></div>
                                            </div>
                                            <small class="text-muted">{{ $order->getProductionProgress() }}%</small>
                                        </div>
                                    </td>
                                    <td>
                                        @if($order->estimated_delivery_ready)
                                            <div class="{{ $order->isOverdue() ? 'text-danger' : 'text-success' }}">
                                                <i class="fas fa-calendar-alt me-1"></i>
                                                {{ $order->estimated_delivery_ready->format('M d, Y') }}
                                            </div>
                                            @if($order->isOverdue())
                                                <small class="text-danger">Overdue</small>
                                            @endif
                                        @else
                                            <span class="text-muted">Not set</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="fw-bold">{{ $order->getTotalEstimatedHours() }}h</div>
                                        <small class="text-muted">
                                            P: {{ $order->estimated_production_hours ?? 0 }}h | 
                                            F: {{ $order->estimated_finishing_hours ?? 0 }}h
                                        </small>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('orders.show', $order) }}" 
                                               class="btn btn-sm btn-outline-primary" title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            
                                            @if($order->canUpdateStatus(auth()->user()))
                                                <button type="button" 
                                                        class="btn btn-sm btn-outline-success"
                                                        onclick="openStatusModal({{ $order->id }}, '{{ $order->order_status }}')"
                                                        title="Update Status">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                            @endif
                                            
                                            <button type="button" 
                                                    class="btn btn-sm btn-outline-info"
                                                    onclick="openPriorityModal({{ $order->id }}, '{{ $order->priority }}')"
                                                    title="Change Priority">
                                                <i class="fas fa-flag"></i>
                                            </button>
                                            
                                            <button type="button" 
                                                    class="btn btn-sm btn-outline-warning"
                                                    onclick="openTimelineModal({{ $order->id }})"
                                                    title="Update Timeline">
                                                <i class="fas fa-calendar"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    <div class="d-flex justify-content-center mt-4">
                        {{ $orders->appends(request()->query())->links() }}
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-search fa-4x text-muted mb-4"></i>
                        <h5 class="text-muted">No orders found</h5>
                        <p class="text-muted">Try adjusting your filters or check the dashboard for available orders.</p>
                        <a href="{{ route('factory.dashboard') }}" class="btn btn-primary">
                            <i class="fas fa-tachometer-alt me-2"></i>Go to Dashboard
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Status Update Modal -->
<div class="modal fade" id="statusModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Order Status</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="statusForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="order_status" class="form-label">New Status</label>
                        <select class="form-select" name="order_status" required>
                            <option value="approved">Queue</option>
                            <option value="in_production">In Production</option>
                            <option value="finishing">Finishing</option>
                            <option value="ready_for_delivery">Ready for Delivery</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="estimated_production_hours" class="form-label">Estimated Production Hours</label>
                        <input type="number" class="form-control" name="estimated_production_hours" min="1">
                    </div>
                    
                    <div class="mb-3">
                        <label for="estimated_finishing_hours" class="form-label">Estimated Finishing Hours</label>
                        <input type="number" class="form-control" name="estimated_finishing_hours" min="1">
                    </div>
                    
                    <div class="mb-3">
                        <label for="production_notes" class="form-label">Production Notes</label>
                        <textarea class="form-control" name="production_notes" rows="3" 
                                  placeholder="Add any production notes..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Status</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Priority Update Modal -->
<div class="modal fade" id="priorityModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Order Priority</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="priorityForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="priority" class="form-label">Priority Level</label>
                        <select class="form-select" name="priority" required>
                            <option value="low">Low</option>
                            <option value="normal">Normal</option>
                            <option value="urgent">Urgent</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Priority</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Timeline Update Modal -->
<div class="modal fade" id="timelineModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Production Timeline</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="timelineForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="estimated_start_date" class="form-label">Estimated Start Date</label>
                        <input type="date" class="form-control" name="estimated_start_date">
                    </div>
                    
                    <div class="mb-3">
                        <label for="estimated_production_complete" class="form-label">Production Complete Date</label>
                        <input type="date" class="form-control" name="estimated_production_complete">
                    </div>
                    
                    <div class="mb-3">
                        <label for="estimated_finishing_complete" class="form-label">Finishing Complete Date</label>
                        <input type="date" class="form-control" name="estimated_finishing_complete">
                    </div>
                    
                    <div class="mb-3">
                        <label for="estimated_delivery_ready" class="form-label">Ready for Delivery Date</label>
                        <input type="date" class="form-control" name="estimated_delivery_ready">
                    </div>
                    
                    <div class="mb-3">
                        <label for="estimated_production_hours" class="form-label">Estimated Production Hours</label>
                        <input type="number" class="form-control" name="estimated_production_hours" min="1">
                    </div>
                    
                    <div class="mb-3">
                        <label for="estimated_finishing_hours" class="form-label">Estimated Finishing Hours</label>
                        <input type="number" class="form-control" name="estimated_finishing_hours" min="1">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Timeline</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function openStatusModal(orderId, currentStatus) {
    const modal = document.getElementById('statusModal');
    const form = document.getElementById('statusForm');
    form.action = `/factory/orders/${orderId}/status`;
    
    // Set current status
    const statusSelect = form.querySelector('select[name="order_status"]');
    statusSelect.value = currentStatus;
    
    new bootstrap.Modal(modal).show();
}

function openPriorityModal(orderId, currentPriority) {
    const modal = document.getElementById('priorityModal');
    const form = document.getElementById('priorityForm');
    form.action = `/factory/orders/${orderId}/priority`;
    
    // Set current priority
    const prioritySelect = form.querySelector('select[name="priority"]');
    prioritySelect.value = currentPriority;
    
    new bootstrap.Modal(modal).show();
}

function openTimelineModal(orderId) {
    const modal = document.getElementById('timelineModal');
    const form = document.getElementById('timelineForm');
    form.action = `/factory/orders/${orderId}/timeline`;
    
    new bootstrap.Modal(modal).show();
}
</script>
@endpush 