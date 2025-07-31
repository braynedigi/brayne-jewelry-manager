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
            
            <button type="button" 
                    class="btn btn-sm btn-outline-info flex-fill"
                    onclick="openPriorityModal({{ $order->id }}, '{{ $order->priority }}')"
                    title="Change Priority">
                <i class="fas fa-flag"></i>
            </button>
        </div>

        <!-- Quick Status Update -->
        @if($order->canUpdateStatus(auth()->user()) && !in_array($order->order_status, ['ready_for_delivery', 'delivered_to_brayne']))
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

        <!-- Quick Delivery Update -->
        @if($order->canUpdateStatus(auth()->user()) && $order->order_status === 'ready_for_delivery')
        <div class="mt-2">
            <div class="btn-group w-100" role="group">
                <button type="button" class="btn btn-sm btn-primary" 
                        onclick="quickUpdateStatus({{ $order->id }}, 'delivered_to_brayne')">
                    Mark Delivered to Brayne
                </button>
            </div>
        </div>
        @endif
    </div>
</div>





 <script>
 function openPriorityModal(orderId, currentPriority) {
     const modal = document.getElementById('priorityModal');
     const form = document.getElementById('priorityForm');
     form.action = `/factory/orders/${orderId}/priority`;
     
     // Set current priority
     const prioritySelect = form.querySelector('select[name="priority"]');
     prioritySelect.value = currentPriority;
     
     new bootstrap.Modal(modal).show();
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