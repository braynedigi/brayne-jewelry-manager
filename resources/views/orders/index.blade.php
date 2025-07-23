@extends('layouts.app')

@section('title', 'Orders')

@section('page-title', 'Orders')

@section('content')
<div class="card shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Order Management</h5>
        <div>
            @if(auth()->user()->isDistributor())
                <a href="{{ route('order-templates.index') }}" class="btn btn-outline-primary me-2">
                    <i class="fas fa-clipboard-list me-1"></i>Templates
                </a>
            @endif
            @if(auth()->user()->isAdmin() || auth()->user()->isDistributor())
                <a href="{{ route('orders.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Create Order
                </a>
            @endif
        </div>
    </div>
    <div class="card-body">
        <!-- Advanced Search & Filter Form -->
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="fas fa-search me-2"></i>Advanced Search & Filters
                    <button class="btn btn-sm btn-outline-secondary float-end" type="button" data-bs-toggle="collapse" data-bs-target="#filterForm">
                        <i class="fas fa-chevron-down"></i>
                    </button>
                </h6>
            </div>
            <div class="collapse show" id="filterForm">
                <div class="card-body">
                    <form method="GET" action="{{ route('orders.index') }}" id="filterForm">
                        <div class="row">
                            <!-- Search -->
                            <div class="col-md-6 mb-3">
                                <label for="search" class="form-label">Search</label>
                                <input type="text" class="form-control" id="search" name="search" 
                                       value="{{ request('search') }}" 
                                       placeholder="Order #, customer, distributor...">
                            </div>

                            <!-- Status Filter -->
                            <div class="col-md-3 mb-3">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select" id="status" name="status">
                                    <option value="">All Statuses</option>
                                    @foreach($filterOptions['statuses'] as $value => $label)
                                        <option value="{{ $value }}" {{ request('status') == $value ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Payment Status Filter -->
                            @if(!auth()->user()->isFactory())
                            <div class="col-md-3 mb-3">
                                <label for="payment_status" class="form-label">Payment Status</label>
                                <select class="form-select" id="payment_status" name="payment_status">
                                    <option value="">All Payment Statuses</option>
                                    @foreach($filterOptions['payment_statuses'] as $value => $label)
                                        <option value="{{ $value }}" {{ request('payment_status') == $value ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            @endif

                            <!-- Date Range -->
                            <div class="col-md-3 mb-3">
                                <label for="date_from" class="form-label">Date From</label>
                                <input type="date" class="form-control" id="date_from" name="date_from" 
                                       value="{{ request('date_from') }}">
                            </div>

                            <div class="col-md-3 mb-3">
                                <label for="date_to" class="form-label">Date To</label>
                                <input type="date" class="form-control" id="date_to" name="date_to" 
                                       value="{{ request('date_to') }}">
                            </div>

                            <!-- Amount Range -->
                            @if(!auth()->user()->isFactory())
                            <div class="col-md-3 mb-3">
                                <label for="amount_min" class="form-label">Min Amount</label>
                                <input type="number" class="form-control" id="amount_min" name="amount_min" 
                                       value="{{ request('amount_min') }}" step="0.01" min="0">
                            </div>

                            <div class="col-md-3 mb-3">
                                <label for="amount_max" class="form-label">Max Amount</label>
                                <input type="number" class="form-control" id="amount_max" name="amount_max" 
                                       value="{{ request('amount_max') }}" step="0.01" min="0">
                            </div>
                            @endif

                            <!-- Priority -->
                            <div class="col-md-3 mb-3">
                                <label for="priority" class="form-label">Priority</label>
                                <select class="form-select" id="priority" name="priority">
                                    <option value="">All Priorities</option>
                                    @foreach($filterOptions['priorities'] as $value => $label)
                                        <option value="{{ $value }}" {{ request('priority') == $value ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Customer Filter -->
                            @if(isset($filterOptions['customers']))
                            <div class="col-md-3 mb-3">
                                <label for="customer_id" class="form-label">Customer</label>
                                <select class="form-select" id="customer_id" name="customer_id">
                                    <option value="">All Customers</option>
                                    @foreach($filterOptions['customers'] as $id => $name)
                                        <option value="{{ $id }}" {{ request('customer_id') == $id ? 'selected' : '' }}>
                                            {{ $name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            @endif

                            <!-- Distributor Filter (Admin only) -->
                            @if(auth()->user()->isAdmin() && isset($filterOptions['distributors']))
                            <div class="col-md-3 mb-3">
                                <label for="distributor_id" class="form-label">Distributor</label>
                                <select class="form-select" id="distributor_id" name="distributor_id">
                                    <option value="">All Distributors</option>
                                    @foreach($filterOptions['distributors'] as $id => $name)
                                        <option value="{{ $id }}" {{ request('distributor_id') == $id ? 'selected' : '' }}>
                                            {{ $name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            @endif

                            <!-- Template Filter (Distributor only) -->
                            @if(auth()->user()->isDistributor() && isset($filterOptions['templates']))
                            <div class="col-md-3 mb-3">
                                <label for="template_id" class="form-label">Template</label>
                                <select class="form-select" id="template_id" name="template_id">
                                    <option value="">All Templates</option>
                                    @foreach($filterOptions['templates'] as $id => $name)
                                        <option value="{{ $id }}" {{ request('template_id') == $id ? 'selected' : '' }}>
                                            {{ $name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            @endif

                            <!-- Special Filters -->
                            <div class="col-md-6 mb-3">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" id="overdue" name="overdue" value="1" 
                                           {{ request('overdue') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="overdue">Overdue Orders</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" id="due_today" name="due_today" value="1" 
                                           {{ request('due_today') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="due_today">Due Today</label>
                                </div>
                            </div>

                            <!-- Sort Options -->
                            <div class="col-md-3 mb-3">
                                <label for="sort_by" class="form-label">Sort By</label>
                                <select class="form-select" id="sort_by" name="sort_by">
                                    @foreach($filterOptions['sort_fields'] as $value => $label)
                                        <option value="{{ $value }}" {{ request('sort_by', 'created_at') == $value ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-3 mb-3">
                                <label for="sort_order" class="form-label">Sort Order</label>
                                <select class="form-select" id="sort_order" name="sort_order">
                                    <option value="desc" {{ request('sort_order', 'desc') == 'desc' ? 'selected' : '' }}>Descending</option>
                                    <option value="asc" {{ request('sort_order') == 'asc' ? 'selected' : '' }}>Ascending</option>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary me-2">
                                    <i class="fas fa-search me-1"></i>Apply Filters
                                </button>
                                <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary me-2">
                                    <i class="fas fa-times me-1"></i>Clear All
                                </a>
                                <button type="button" class="btn btn-outline-info" onclick="exportOrders()">
                                    <i class="fas fa-download me-1"></i>Export
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Results Summary -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <span class="text-muted">
                    Showing {{ $orders->firstItem() ?? 0 }} to {{ $orders->lastItem() ?? 0 }} 
                    of {{ $orders->total() }} orders
                </span>
                @if(request()->hasAny(['search', 'status', 'priority', 'date_from', 'date_to', 'customer_id', 'distributor_id', 'template_id', 'overdue', 'due_today']) || 
                    (!auth()->user()->isFactory() && request()->hasAny(['payment_status', 'amount_min', 'amount_max'])))
                    <span class="badge bg-info ms-2">Filtered</span>
                @endif
            </div>
            @if(!auth()->user()->isFactory())
            <div>
                <span class="text-muted">Total Amount: 
                    <strong>{{ $orders->sum('total_amount') ? '₱' . number_format($orders->sum('total_amount'), 2) : '₱0.00' }}</strong>
                </span>
            </div>
            @endif
        </div>

        @if($orders->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover" id="ordersTable">
                    <thead>
                        <tr>
                            <th>Order #</th>
                            @if(auth()->user()->isAdmin())
                                <th>Distributor</th>
                            @endif
                            <th>Customer</th>
                            <th>Items</th>
                            @if(!auth()->user()->isFactory())
                                <th>Total Amount</th>
                            @endif
                            <th>Status</th>
                            @if(!auth()->user()->isFactory())
                                <th>Payment</th>
                            @endif
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($orders as $order)
                            <tr class="order-row" 
                                data-order-id="{{ $order->id }}"
                                data-order-number="{{ strtolower($order->order_number) }}"
                                data-customer="{{ strtolower($order->customer->name) }}"
                                data-customer-email="{{ strtolower($order->customer->email ?? '') }}"
                                data-distributor="{{ auth()->user()->isAdmin() ? strtolower($order->distributor->user->name ?? '') : '' }}"
                                data-distributor-company="{{ auth()->user()->isAdmin() ? strtolower($order->distributor->company_name ?? '') : '' }}"
                                data-status="{{ $order->order_status }}">
                                <td>
                                    <strong>{{ $order->order_number }}</strong>
                                </td>
                                @if(auth()->user()->isAdmin())
                                    <td>
                                        @if($order->distributor)
                                            <div class="d-flex align-items-center">
                                                @if($order->distributor->user->logo)
                                                    <img src="{{ asset('storage/' . $order->distributor->user->logo) }}" 
                                                         alt="{{ $order->distributor->user->name }}" 
                                                         class="img-thumbnail me-2" 
                                                         style="width: 30px; height: 30px; object-fit: cover;">
                                                @else
                                                    <div class="bg-light d-flex align-items-center justify-content-center me-2" 
                                                         style="width: 30px; height: 30px;">
                                                        <i class="fas fa-user text-muted"></i>
                                                    </div>
                                                @endif
                                                <div>
                                                    <div class="fw-bold">{{ $order->distributor->user->name }}</div>
                                                    <small class="text-muted">{{ $order->distributor->company_name }}</small>
                                                </div>
                                            </div>
                                        @else
                                            <span class="text-muted">No distributor</span>
                                        @endif
                                    </td>
                                @endif
                                <td>
                                    <div>
                                        <div class="fw-bold">{{ $order->customer->name }}</div>
                                        <small class="text-muted">{{ $order->customer->email ?? 'No email' }}</small>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-info">{{ $order->products->count() }} item(s)</span>
                                </td>
                                @if(!auth()->user()->isFactory())
                                    <td>
                                        <span class="fw-bold text-primary">
                                            @if($order->distributor && $order->distributor->is_international)
                                                ${{ number_format($order->total_amount, 2) }}
                                            @else
                                                ₱{{ number_format($order->total_amount, 2) }}
                                            @endif
                                        </span>
                                    </td>
                                @endif
                                <td>
                                    <span class="badge bg-{{ $order->getOrderStatusColor() }} status-badge">
                                        {{ $order->getOrderStatusLabel() }}
                                    </span>
                                </td>
                                @if(!auth()->user()->isFactory())
                                    <td>
                                        <span class="badge bg-{{ $order->getPaymentStatusColor() }}">
                                            {{ $order->getPaymentStatusLabel() }}
                                        </span>
                                    </td>
                                @endif
                                <td>{{ $order->created_at->format('M d, Y') }}</td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('orders.show', $order) }}" class="btn btn-sm btn-outline-primary" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if(auth()->user()->isAdmin() || (auth()->user()->isDistributor() && $order->distributor_id === auth()->user()->distributor->id))
                                            <a href="{{ route('orders.edit', $order) }}" class="btn btn-sm btn-outline-warning" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        @endif
                                        @if(auth()->user()->isAdmin() || auth()->user()->isFactory())
                                            <button type="button" class="btn btn-sm btn-outline-info" 
                                                    onclick="openStatusModal({{ $order->id }}, '{{ $order->order_status }}')" title="Update Status">
                                                <i class="fas fa-sync-alt"></i>
                                            </button>
                                        @endif
                                        @if(auth()->user()->isDistributor() && $order->distributor_id === auth()->user()->distributor->id)
                                            <form action="{{ route('orders.quick-reorder', $order) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-outline-success" 
                                                        onclick="return confirm('Create a new order with the same products?')" title="Quick Reorder">
                                                    <i class="fas fa-redo"></i>
                                                </button>
                                            </form>
                                            <a href="{{ route('order-templates.create-from-order', $order) }}" class="btn btn-sm btn-outline-secondary" title="Save as Template">
                                                <i class="fas fa-save"></i>
                                            </a>
                                        @endif
                                        @if(auth()->user()->isAdmin() || (auth()->user()->isDistributor() && $order->distributor_id === auth()->user()->distributor->id))
                                            <form action="{{ route('orders.destroy', $order) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" 
                                                        onclick="return confirm('Are you sure you want to delete this order?')" title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-4">
                <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No orders found</h5>
                <p class="text-muted">
                    @if(auth()->user()->isAdmin() || auth()->user()->isDistributor())
                        Start by creating your first order.
                    @else
                        No orders have been created yet.
                    @endif
                </p>
                @if(auth()->user()->isAdmin() || auth()->user()->isDistributor())
                    <a href="{{ route('orders.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Create First Order
                    </a>
                @endif
            </div>
        @endif
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
                            <option value="pending">Pending</option>
                            <option value="confirmed">Confirmed</option>
                            <option value="in_production">In Production</option>
                            <option value="ready_for_delivery">Ready for Delivery</option>
                            <option value="shipped">Shipped</option>
                            <option value="delivered">Delivered</option>
                            <option value="cancelled">Cancelled</option>
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
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('orderSearch');
    const orderRows = document.querySelectorAll('.order-row');
    const orderCount = document.getElementById('orderCount');
    const totalOrders = orderRows.length;

    function filterOrders() {
        const searchTerm = searchInput.value.toLowerCase().trim();
        let visibleCount = 0;

        orderRows.forEach(row => {
            const orderNumber = row.getAttribute('data-order-number');
            const customer = row.getAttribute('data-customer');
            const customerEmail = row.getAttribute('data-customer-email');
            const distributor = row.getAttribute('data-distributor');
            const distributorCompany = row.getAttribute('data-distributor-company');

            const matches = orderNumber.includes(searchTerm) || 
                           customer.includes(searchTerm) || 
                           customerEmail.includes(searchTerm) || 
                           distributor.includes(searchTerm) || 
                           distributorCompany.includes(searchTerm);

            if (matches) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });

        // Update count display
        if (searchTerm === '') {
            orderCount.textContent = `Showing ${totalOrders} of ${totalOrders} orders`;
        } else {
            orderCount.textContent = `Showing ${visibleCount} of ${totalOrders} orders`;
        }
    }

    // Add event listener for search input
    searchInput.addEventListener('input', filterOrders);

    // Clear search function
    window.clearOrderSearch = function() {
        searchInput.value = '';
        filterOrders();
        searchInput.focus();
    };
});

function openStatusModal(orderId, currentStatus) {
    const modal = new bootstrap.Modal(document.getElementById('statusModal'));
    const form = document.getElementById('statusForm');
    const statusSelect = document.getElementById('order_status');
    
    form.action = `/orders/${orderId}/status`;
    statusSelect.value = currentStatus;
    
    modal.show();
}
</script>
@endpush
@endsection 