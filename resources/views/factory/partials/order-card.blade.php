<div class="order-card {{ $order->priority === 'urgent' ? 'urgent' : '' }} {{ $order->isOverdue() ? 'overdue' : '' }}" 
     data-order-id="{{ $order->id }}">
    <div class="p-3">
        <!-- Order Header -->
        <div class="d-flex justify-content-between align-items-start mb-2">
            <div>
                <h6 class="mb-0 fw-bold">{{ $order->order_number }}</h6>
                <small class="text-muted">{{ $order->customer->name }}</small>
            </div>
            <div class="text-end">
                <span class="badge bg-{{ $order->getPriorityColor() }} mb-1">
                    <i class="{{ $order->getPriorityIcon() }} me-1"></i>{{ $order->getPriorityLabel() }}
                </span>
                @if($order->isOverdue())
                    <div class="badge bg-danger">Overdue</div>
                @endif
            </div>
        </div>

        <!-- Progress Bar -->
        <div class="mb-2">
            <div class="d-flex justify-content-between align-items-center mb-1">
                <small class="text-muted">Progress</small>
                <small class="text-muted">{{ $order->getProductionProgress() }}%</small>
            </div>
            <div class="progress" style="height: 6px;">
                <div class="progress-bar bg-{{ $order->getOrderStatusColor() }}" 
                     style="width: {{ $order->getProductionProgress() }}%"></div>
            </div>
        </div>

        <!-- Order Details -->
        <div class="mb-2">
            <div class="row g-1">
                <div class="col-6">
                    <small class="text-muted">Products:</small>
                    <div class="small">{{ $order->products->count() }} items</div>
                </div>
                <div class="col-6">
                    <small class="text-muted">Hours:</small>
                    <div class="small">{{ $order->getTotalEstimatedHours() }}h</div>
                </div>
            </div>
        </div>

        <!-- Timeline -->
        @if($order->getEstimatedCompletionDate())
        <div class="mb-2">
            <small class="text-muted">Due: {{ $order->getEstimatedCompletionDate() }}</small>
            @if($order->estimated_delivery_ready)
                <div class="small {{ $order->isOverdue() ? 'text-danger' : 'text-success' }}">
                    <i class="fas fa-calendar-alt me-1"></i>
                    {{ $order->estimated_delivery_ready->format('M d, Y') }}
                </div>
            @endif
        </div>
        @endif

        <!-- Status Badge -->
        <div class="mb-2">
            <span class="badge bg-{{ $order->getOrderStatusColor() }}">
                {{ $order->getOrderStatusLabel() }}
            </span>
        </div>

        <!-- Actions -->
        <div class="d-flex gap-1">
            <a href="{{ route('orders.show', $order) }}" 
               class="btn btn-sm btn-outline-primary flex-fill" 
               title="View Details">
                <i class="fas fa-eye"></i>
            </a>
            
            @if($order->canUpdateStatus(auth()->user()))
                <button type="button" 
                        class="btn btn-sm btn-outline-success flex-fill"
                        onclick="openStatusModal({{ $order->id }}, '{{ $order->order_status }}')"
                        title="Update Status">
                    <i class="fas fa-edit"></i>
                </button>
            @endif
            
            <button type="button" 
                    class="btn btn-sm btn-outline-info flex-fill"
                    onclick="openPriorityModal({{ $order->id }}, '{{ $order->priority }}')"
                    title="Change Priority">
                <i class="fas fa-flag"></i>
            </button>
        </div>

        <!-- Quick Status Update -->
        @if($order->canUpdateStatus(auth()->user()) && $order->order_status !== 'ready_for_delivery')
        <div class="mt-2">
            <div class="btn-group w-100" role="group">
                @if($order->order_status === 'approved')
                    <button type="button" class="btn btn-sm btn-success" 
                            onclick="quickUpdateStatus({{ $order->id }}, 'in_production')">
                        Start Production
                    </button>
                @elseif($order->order_status === 'in_production')
                    <button type="button" class="btn btn-sm btn-info" 
                            onclick="quickUpdateStatus({{ $order->id }}, 'finishing')">
                        Move to Finishing
                    </button>
                @elseif($order->order_status === 'finishing')
                    <button type="button" class="btn btn-sm btn-success" 
                            onclick="quickUpdateStatus({{ $order->id }}, 'ready_for_delivery')">
                        Mark Ready
                    </button>
                @endif
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Status Update Modal -->
<div class="modal fade" id="statusModal{{ $order->id }}" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Order Status</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('factory.update-status', $order) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="order_status" class="form-label">New Status</label>
                        <select class="form-select" name="order_status" required>
                            @foreach($order->getNextAvailableStatuses(auth()->user()) as $status => $label)
                                <option value="{{ $status }}" {{ $order->order_status === $status ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="estimated_production_hours" class="form-label">Estimated Production Hours</label>
                        <input type="number" class="form-control" name="estimated_production_hours" 
                               value="{{ $order->estimated_production_hours }}" min="1">
                    </div>
                    
                    <div class="mb-3">
                        <label for="estimated_finishing_hours" class="form-label">Estimated Finishing Hours</label>
                        <input type="number" class="form-control" name="estimated_finishing_hours" 
                               value="{{ $order->estimated_finishing_hours }}" min="1">
                    </div>
                    
                    <div class="mb-3">
                        <label for="production_notes" class="form-label">Production Notes</label>
                        <textarea class="form-control" name="production_notes" rows="3" 
                                  placeholder="Add any production notes...">{{ $order->production_notes }}</textarea>
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
<div class="modal fade" id="priorityModal{{ $order->id }}" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Order Priority</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('factory.update-priority', $order) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="priority" class="form-label">Priority Level</label>
                        <select class="form-select" name="priority" required>
                            <option value="low" {{ $order->priority === 'low' ? 'selected' : '' }}>Low</option>
                            <option value="normal" {{ $order->priority === 'normal' ? 'selected' : '' }}>Normal</option>
                            <option value="urgent" {{ $order->priority === 'urgent' ? 'selected' : '' }}>Urgent</option>
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

<script>
function openStatusModal(orderId, currentStatus) {
    new bootstrap.Modal(document.getElementById('statusModal' + orderId)).show();
}

function openPriorityModal(orderId, currentPriority) {
    new bootstrap.Modal(document.getElementById('priorityModal' + orderId)).show();
}

function quickUpdateStatus(orderId, newStatus) {
    if (confirm('Are you sure you want to update this order status?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/factory/orders/${orderId}/status`;
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        const methodField = document.createElement('input');
        methodField.type = 'hidden';
        methodField.name = '_method';
        methodField.value = 'PUT';
        
        const statusField = document.createElement('input');
        statusField.type = 'hidden';
        statusField.name = 'order_status';
        statusField.value = newStatus;
        
        form.appendChild(csrfToken);
        form.appendChild(methodField);
        form.appendChild(statusField);
        
        document.body.appendChild(form);
        form.submit();
    }
}
</script> 