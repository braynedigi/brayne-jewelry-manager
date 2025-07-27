@extends('layouts.app')

@section('title', 'Review Order for Approval')

@section('page-title', 'Review Order for Approval')

@section('content')
<div class="row">
    <div class="col-md-8">
        <!-- Order Details -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Order Details</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td><strong>Order Number:</strong></td>
                                <td>{{ $order->order_number }}</td>
                            </tr>
                            <tr>
                                <td><strong>Created Date:</strong></td>
                                <td>{{ $order->created_at->format('M d, Y h:i A') }}</td>
                            </tr>
                            <tr>
                                <td><strong>Total Amount:</strong></td>
                                <td><span class="fw-bold text-primary">{{ $order->distributor->currency_symbol }}{{ number_format($order->total_amount, 2) }}</span></td>
                            </tr>
                            <tr>
                                <td><strong>Payment Status:</strong></td>
                                <td>
                                    <span class="badge bg-{{ $order->getPaymentStatusColor() }}">
                                        {{ $order->getPaymentStatusLabel() }}
                                    </span>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td><strong>Distributor:</strong></td>
                                <td>{{ $order->distributor->user->name }}</td>
                            </tr>
                            <tr>
                                <td><strong>Company:</strong></td>
                                <td>{{ $order->distributor->company_name }}</td>
                            </tr>
                            <tr>
                                <td><strong>Customer:</strong></td>
                                <td>{{ $order->customer->name }}</td>
                            </tr>
                            <tr>
                                <td><strong>Courier:</strong></td>
                                <td>{{ $order->courier ? $order->courier->name : 'Not specified' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
                
                @if($order->notes)
                <div class="mt-3">
                    <strong>Order Notes:</strong>
                    <div class="alert alert-info mb-0">
                        {{ $order->notes }}
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Products -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Order Items</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Metal</th>
                                <th>Quantity</th>
                                <th>Unit Price</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->products as $product)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        @if($product->hasImage())
                                            <img src="{{ $product->getImageUrl() }}" 
                                                 alt="{{ $product->name }}" class="me-2" width="40" height="40" style="object-fit: cover;">
                                        @else
                                            <div class="bg-secondary me-2 d-flex align-items-center justify-content-center" 
                                                 style="width: 40px; height: 40px;">
                                                <i class="fas fa-gem text-white"></i>
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
                                <td>{{ $product->pivot->quantity }}</td>
                                <td>{{ $order->distributor->currency_symbol }}{{ number_format($product->pivot->price, 2) }}</td>
                                <td>{{ $order->distributor->currency_symbol }}{{ number_format($product->pivot->price * $product->pivot->quantity, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="table-primary">
                                <td colspan="4" class="text-end"><strong>Total:</strong></td>
                                <td><strong>{{ $order->distributor->currency_symbol }}{{ number_format($order->total_amount, 2) }}</strong></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <!-- Customer Details -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Customer Information</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td><strong>Name:</strong></td>
                                <td>{{ $order->customer->name }}</td>
                            </tr>
                            <tr>
                                <td><strong>Email:</strong></td>
                                <td>{{ $order->customer->email }}</td>
                            </tr>
                            <tr>
                                <td><strong>Phone:</strong></td>
                                <td>{{ $order->customer->phone }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td><strong>Address:</strong></td>
                                <td>{{ $order->customer->full_address }}</td>
                            </tr>
                            <tr>
                                <td><strong>City:</strong></td>
                                <td>{{ $order->customer->city }}</td>
                            </tr>
                            <tr>
                                <td><strong>Country:</strong></td>
                                <td>{{ $order->customer->country }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <!-- Approval Actions -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Approval Decision</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.approval.update', $order) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label class="form-label">Decision <span class="text-danger">*</span></label>
                        <div class="d-grid gap-2">
                            <button type="submit" name="action" value="approve" class="btn btn-success btn-lg">
                                <i class="fas fa-check me-2"></i>Approve Order
                            </button>
                            <button type="submit" name="action" value="reject" class="btn btn-danger btn-lg">
                                <i class="fas fa-times me-2"></i>Reject Order
                            </button>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="notes" class="form-label">Reason/Notes <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="notes" name="notes" rows="4" 
                                  placeholder="Please provide a reason for your decision..." required></textarea>
                        <div class="form-text">This note will be visible to the distributor and recorded in the order history.</div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Order Status History -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Status History</h5>
            </div>
            <div class="card-body">
                <div class="timeline">
                    @foreach($order->statusHistory()->with('changedBy')->latest()->get() as $history)
                    <div class="timeline-item">
                        <div class="timeline-marker bg-primary"></div>
                        <div class="timeline-content">
                            <div class="fw-bold">{{ $history->getStatusLabel() }}</div>
                            <div class="small text-muted">{{ $history->changedBy->name }}</div>
                            <div class="small text-muted">{{ $history->created_at->format('M d, Y h:i A') }}</div>
                            @if($history->notes)
                            <div class="mt-1">{{ $history->notes }}</div>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Back Button -->
<div class="row">
    <div class="col-12">
        <a href="{{ route('admin.approval.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i>Back to Approval Queue
        </a>
    </div>
</div>
@endsection

@push('styles')
<style>
.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline-item {
    position: relative;
    margin-bottom: 20px;
}

.timeline-marker {
    position: absolute;
    left: -35px;
    top: 5px;
    width: 12px;
    height: 12px;
    border-radius: 50%;
}

.timeline-content {
    padding-left: 10px;
    border-left: 2px solid #e9ecef;
    padding-left: 15px;
}

.timeline-item:last-child .timeline-content {
    border-left: none;
}
</style>
@endpush

@push('scripts')
<script>
// Form validation
document.querySelector('form').addEventListener('submit', function(e) {
    const notes = document.getElementById('notes').value.trim();
    if (!notes) {
        e.preventDefault();
        alert('Please provide a reason for your decision.');
        document.getElementById('notes').focus();
        return false;
    }
    
    // Confirm action
    const action = e.submitter.value;
    const confirmMessage = action === 'approve' 
        ? 'Are you sure you want to approve this order?' 
        : 'Are you sure you want to reject this order?';
    
    if (!confirm(confirmMessage)) {
        e.preventDefault();
        return false;
    }
});
</script>
@endpush 