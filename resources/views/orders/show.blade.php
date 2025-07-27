@extends('layouts.app')

@section('title', 'Order Details')

@section('page-title', 'Order Details')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-10">
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Order #{{ $order->order_number }}</h5>
                <div>
                    @if(auth()->user()->isAdmin() || (auth()->user()->isDistributor() && $order->distributor_id === auth()->user()->distributor->id))
                        <a href="{{ route('orders.edit', $order) }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit me-1"></i>Edit
                        </a>
                    @endif
                    @if(auth()->user()->isAdmin() || auth()->user()->isFactory())
                        <button type="button" class="btn btn-info btn-sm" 
                                onclick="openStatusModal({{ $order->id }}, '{{ $order->order_status }}')">
                            <i class="fas fa-sync-alt me-1"></i>Update Status
                        </button>
                    @endif
                    <a href="{{ route('orders.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left me-1"></i>Back
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="text-muted">Order Information</h6>
                        <table class="table table-borderless">
                            <tr>
                                <td><strong>Order Number:</strong></td>
                                <td>{{ $order->order_number }}</td>
                            </tr>
                            <tr>
                                <td><strong>Status:</strong></td>
                                <td>
                                    <span class="badge bg-{{ $order->getOrderStatusColor() }}">
                                        {{ $order->getOrderStatusLabel() }}
                                    </span>
                                </td>
                            </tr>
                            @if(!auth()->user()->isFactory())
                                <tr>
                                    <td><strong>Payment Status:</strong></td>
                                    <td>
                                        <span class="badge bg-{{ $order->getPaymentStatusColor() }}">
                                            {{ $order->getPaymentStatusLabel() }}
                                        </span>
                                    </td>
                                </tr>
                            @endif
                            @if(!auth()->user()->isFactory())
                                <tr>
                                    <td><strong>Total Amount:</strong></td>
                                    <td>
                                        <span class="fw-bold text-primary">
                                            @if($order->distributor && $order->distributor->is_international)
                                                ${{ number_format($order->total_amount, 2) }}
                                            @else
                                                ₱{{ number_format($order->total_amount, 2) }}
                                            @endif
                                        </span>
                                    </td>
                                </tr>
                            @endif
                            <tr>
                                <td><strong>Created:</strong></td>
                                <td>{{ $order->created_at->format('M d, Y H:i') }}</td>
                            </tr>
                            <tr>
                                <td><strong>Last Updated:</strong></td>
                                <td>{{ $order->updated_at->format('M d, Y H:i') }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted">Contact Information</h6>
                        <table class="table table-borderless">
                            @if(auth()->user()->isAdmin())
                                <tr>
                                    <td><strong>Distributor:</strong></td>
                                    <td>
                                        @if($order->distributor)
                                            <div class="d-flex align-items-center">
                                                                                        @if($order->distributor->user->hasLogo())
                                            <img src="{{ $order->distributor->user->getLogoUrl() }}" 
                                                 alt="{{ $order->distributor->user->name }}" 
                                                 class="img-thumbnail me-2" 
                                                 style="width: 40px; height: 40px; object-fit: cover;">
                                        @else
                                                    <div class="bg-light d-flex align-items-center justify-content-center me-2" 
                                                         style="width: 40px; height: 40px;">
                                                        <i class="fas fa-user text-muted"></i>
                                                    </div>
                                                @endif
                                                <div>
                                                    <div class="fw-bold">{{ $order->distributor->user->name }}</div>
                                                    <small class="text-muted">{{ $order->distributor->company_name }}</small>
                                                </div>
                                            </div>
                                        @else
                                            <span class="text-muted">No distributor assigned</span>
                                        @endif
                                    </td>
                                </tr>
                            @endif
                            <tr>
                                <td><strong>Customer:</strong></td>
                                <td>
                                    <div class="fw-bold">{{ $order->customer->name }}</div>
                                    @if($order->customer->email)
                                        <small class="text-muted">{{ $order->customer->email }}</small><br>
                                    @endif
                                    @if($order->customer->phone)
                                        <small class="text-muted">{{ $order->customer->phone }}</small><br>
                                    @endif
                                    @if($order->customer->hasAddress())
                                        <small class="text-muted">{{ $order->customer->full_address }}</small>
                                    @endif
                                </td>
                            </tr>
                            @if($order->courier)
                                <tr>
                                    <td><strong>Courier:</strong></td>
                                    <td>
                                        <div class="fw-bold">{{ $order->courier->name }}</div>
                                        @if($order->courier->phone)
                                            <small class="text-muted">{{ $order->courier->phone }}</small><br>
                                        @endif
                                        @if($order->courier->email)
                                            <small class="text-muted">{{ $order->courier->email }}</small>
                                        @endif
                                    </td>
                                </tr>
                            @endif
                        </table>
                    </div>
                </div>

                @if($order->notes)
                    <hr>
                    <h6 class="text-muted">Notes</h6>
                    <div class="alert alert-info">
                        {{ $order->notes }}
                    </div>
                @endif

                <hr>
                <h6 class="text-muted">Order Items</h6>
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Metal</th>
                                <th>Stones</th>
                                <th>Ring Size</th>
                                <th>Font</th>
                                <th>Names</th>
                                <th>Quantity</th>
                                @if(!auth()->user()->isFactory())
                                    <th>Unit Price</th>
                                    <th>Total</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->products as $product)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($product->hasImage())
                                                <img src="{{ $product->getImageUrl() }}" 
                                                     alt="{{ $product->name }}" 
                                                     class="img-thumbnail me-2" 
                                                     style="width: 40px; height: 40px; object-fit: cover;">
                                            @else
                                                <div class="bg-light d-flex align-items-center justify-content-center me-2" 
                                                     style="width: 40px; height: 40px;">
                                                    <i class="fas fa-gem text-muted"></i>
                                                </div>
                                            @endif
                                            <div>
                                                <div class="fw-bold">{{ $product->name }}</div>
                                                <small class="text-muted">{{ $product->sku }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary">{{ $product->pivot->metal }}</span>
                                    </td>
                                    <td>
                                        @if($product->pivot->stones && trim($product->pivot->stones))
                                            @php
                                                $stones = is_array($product->pivot->stones) ? $product->pivot->stones : explode(', ', $product->pivot->stones);
                                                $stones = array_filter(array_map('trim', $stones));
                                            @endphp
                                            @if(count($stones) > 0)
                                                @foreach($stones as $stone)
                                                    <span class="badge bg-warning me-1">
                                                        <i class="fas fa-gem me-1"></i>{{ $stone }}
                                                    </span>
                                                @endforeach
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($product->pivot->ring_size && trim($product->pivot->ring_size))
                                            <span class="badge bg-dark">
                                                <i class="fas fa-circle me-1"></i>{{ trim($product->pivot->ring_size) }}
                                            </span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($product->pivot->font)
                                            @php
                                                $fonts = is_array($product->pivot->font) ? $product->pivot->font : explode(', ', $product->pivot->font);
                                            @endphp
                                            @foreach($fonts as $font)
                                                @if(trim($font))
                                                    <span class="badge bg-info me-1">{{ trim($font) }}</span>
                                                @endif
                                            @endforeach
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            $names = is_array($product->pivot->names) ? $product->pivot->names : json_decode($product->pivot->names, true);
                                            $fonts = is_array($product->pivot->font) ? $product->pivot->font : explode(', ', $product->pivot->font);
                                        @endphp
                                        @if($names && is_array($names) && count($names) > 0)
                                            @foreach($names as $index => $name)
                                                @if($name)
                                                    <div class="mb-1">
                                                        <span class="badge bg-success">
                                                            <i class="fas fa-signature me-1"></i>{{ $name }}
                                                        </span>
                                                        @if(isset($fonts[$index]) && trim($fonts[$index]))
                                                            <span class="badge bg-info ms-1">
                                                                <i class="fas fa-font me-1"></i>{{ trim($fonts[$index]) }}
                                                            </span>
                                                        @endif
                                                    </div>
                                                @endif
                                            @endforeach
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>{{ $product->pivot->quantity }}</td>
                                    @if(!auth()->user()->isFactory())
                                                                <td>{{ $order->distributor->is_international ? '$' : '₱' }}{{ number_format($product->pivot->price, 2) }}</td>
                        <td>{{ $order->distributor->is_international ? '$' : '₱' }}{{ number_format($product->pivot->price * $product->pivot->quantity, 2) }}</td>
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @php
                    // Filter status history based on user role
                    $filteredHistory = $order->statusHistory->sortBy('created_at');
                    
                    // For factory users, only show production-related statuses
                    if (auth()->user()->isFactory()) {
                        $productionStatuses = ['approved', 'in_production', 'finishing', 'ready_for_delivery'];
                        $filteredHistory = $filteredHistory->filter(function($history) use ($productionStatuses) {
                            return in_array($history->status, $productionStatuses);
                        });
                    }
                @endphp
                
                @if($filteredHistory->count() > 0)
                    <hr>
                    <h6 class="text-muted">Status History</h6>
                    <div class="timeline">
                        @foreach($filteredHistory as $history)
                            <div class="d-flex mb-3">
                                <div class="flex-shrink-0">
                                    <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center" 
                                         style="width: 40px; height: 40px;">
                                        <i class="fas fa-clock text-white"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <div class="d-flex justify-content-between">
                                        <h6 class="mb-1">
                                            @php
                                                $tempOrder = new \App\Models\Order();
                                                $tempOrder->order_status = $history->status;
                                            @endphp
                                            {{ $tempOrder->getOrderStatusLabel() }}
                                        </h6>
                                        <small class="text-muted">{{ $history->created_at->format('M d, Y H:i') }}</small>
                                    </div>
                                    <p class="mb-1 text-muted">Changed by: {{ $history->changedBy->name }}</p>
                                    @if($history->notes)
                                        <p class="mb-0">{{ $history->notes }}</p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @elseif(auth()->user()->isFactory())
                    <hr>
                    <h6 class="text-muted">Status History</h6>
                    <div class="text-center py-3">
                        <i class="fas fa-industry fa-2x text-muted mb-2"></i>
                        <p class="text-muted mb-0">No production status history available</p>
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
                        <select class="form-select" id="order_status" name="order_status" required>
                            <!-- Options will be populated dynamically via JavaScript -->
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="status_notes" class="form-label">Notes (Optional)</label>
                        <textarea class="form-control" id="status_notes" name="notes" rows="3" placeholder="Add any notes about this status change"></textarea>
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

@push('scripts')
<script>
function openStatusModal(orderId, currentStatus) {
    const modal = new bootstrap.Modal(document.getElementById('statusModal'));
    const form = document.getElementById('statusForm');
    const statusSelect = document.getElementById('order_status');
    
    form.action = `/orders/${orderId}/status`;
    
    // Clear existing options
    statusSelect.innerHTML = '';
    
    // Get available statuses based on current status and user role
    const availableStatuses = getAvailableStatuses(currentStatus);
    
    // Populate options
    availableStatuses.forEach(status => {
        const option = document.createElement('option');
        option.value = status.value;
        option.textContent = status.label;
        statusSelect.appendChild(option);
    });
    
    // Set current status as selected if it's still available
    if (availableStatuses.some(status => status.value === currentStatus)) {
        statusSelect.value = currentStatus;
    }
    
    modal.show();
}

function getAvailableStatuses(currentStatus) {
    const userRole = '{{ auth()->user()->role }}';
    
    if (userRole === 'admin') {
        // Admin can move through the entire workflow
        const adminFlow = {
            'pending_payment': [
                { value: 'approved', label: 'Approved' },
                { value: 'cancelled', label: 'Cancelled' }
            ],
            'approved': [
                { value: 'in_production', label: 'In Production' },
                { value: 'cancelled', label: 'Cancelled' }
            ],
            'in_production': [
                { value: 'finishing', label: 'Finishing' },
                { value: 'cancelled', label: 'Cancelled' }
            ],
            'finishing': [
                { value: 'ready_for_delivery', label: 'Ready for Delivery' },
                { value: 'cancelled', label: 'Cancelled' }
            ],
            'ready_for_delivery': [
                { value: 'delivered_to_brayne', label: 'Delivered to Brayne Jewelry' },
                { value: 'cancelled', label: 'Cancelled' }
            ],
            'delivered_to_brayne': [
                { value: 'delivered_to_client', label: 'Delivered to Client' }
            ]
        };
        return adminFlow[currentStatus] || [];
    } else if (userRole === 'factory') {
        // Factory can only move forward in production
        const factoryFlow = {
            'approved': [
                { value: 'in_production', label: 'In Production' }
            ],
            'in_production': [
                { value: 'finishing', label: 'Finishing' }
            ],
            'finishing': [
                { value: 'ready_for_delivery', label: 'Ready for Delivery' }
            ]
        };
        return factoryFlow[currentStatus] || [];
    }
    
    return [];
}
</script>
@endpush
@endsection 