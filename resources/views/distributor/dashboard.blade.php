@extends('layouts.app')

@section('title', 'Distributor Dashboard')

@section('page-title', 'Distributor Dashboard')
@section('page-subtitle', 'Manage your customers and orders')

@section('content')
<div class="row">
    <!-- Stats Cards -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stats-card">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="stats-label">Total Customers</div>
                    <div class="stats-number">{{ $stats['total_customers'] ?? 0 }}</div>
                </div>
                <div class="stats-icon">
                    <i class="fas fa-user-friends"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stats-card" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="stats-label">Total Orders</div>
                    <div class="stats-number">{{ $stats['total_orders'] ?? 0 }}</div>
                </div>
                <div class="stats-icon">
                    <i class="fas fa-shopping-cart"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stats-card" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="stats-label">Pending Orders</div>
                    <div class="stats-number">{{ $stats['pending_orders'] ?? 0 }}</div>
                </div>
                <div class="stats-icon">
                    <i class="fas fa-clock"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stats-card" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="stats-label">Company Status</div>
                    <div class="stats-number">{{ $distributor->is_international ? 'International' : 'Local' }}</div>
                </div>
                <div class="stats-icon">
                    <i class="fas fa-globe"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Quick Actions -->
    <div class="col-lg-8 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-bolt me-2"></i>Quick Actions
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <a href="{{ route('customers.create') }}" class="btn btn-primary w-100 p-3">
                            <i class="fas fa-user-plus fa-2x mb-2"></i>
                            <div>Add Customer</div>
                            <small class="opacity-75">Register new customer</small>
                        </a>
                    </div>
                    <div class="col-md-6 mb-3">
                        <a href="{{ route('orders.create') }}" class="btn btn-success w-100 p-3">
                            <i class="fas fa-shopping-cart fa-2x mb-2"></i>
                            <div>Create Order</div>
                            <small class="opacity-75">Place new order</small>
                        </a>
                    </div>
                    <div class="col-md-6 mb-3">
                        <a href="{{ route('customers.index') }}" class="btn btn-info w-100 p-3">
                            <i class="fas fa-users fa-2x mb-2"></i>
                            <div>View Customers</div>
                            <small class="opacity-75">Manage customer list</small>
                        </a>
                    </div>
                    <div class="col-md-6 mb-3">
                        <a href="{{ route('orders.index') }}" class="btn btn-warning w-100 p-3">
                            <i class="fas fa-list fa-2x mb-2"></i>
                            <div>View Orders</div>
                            <small class="opacity-75">Track order status</small>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Company Info -->
    <div class="col-lg-4 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-building me-2"></i>Company Info
                </h5>
            </div>
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    @if($distributor->user->logo)
                        <img src="{{ asset('storage/' . $distributor->user->logo) }}" 
                             alt="{{ $distributor->user->name }}" 
                             class="img-thumbnail me-3" 
                             style="width: 60px; height: 60px; object-fit: cover;">
                    @else
                        <div class="bg-light d-flex align-items-center justify-content-center me-3" 
                             style="width: 60px; height: 60px; border-radius: 0.5rem;">
                            <i class="fas fa-building fa-2x text-muted"></i>
                        </div>
                    @endif
                    <div>
                        <h6 class="mb-1">{{ $distributor->user->name }}</h6>
                        <p class="text-muted mb-0">{{ $distributor->company_name }}</p>
                    </div>
                </div>
                
                <div class="mb-2">
                    <small class="text-muted">Status:</small>
                    <span class="badge bg-{{ $distributor->is_international ? 'info' : 'success' }}">
                        {{ $distributor->is_international ? 'International' : 'Local' }}
                    </span>
                </div>
                
                <div class="mb-2">
                    <small class="text-muted">Currency:</small>
                    <span class="fw-bold">{{ $distributor->is_international ? 'USD ($)' : 'PHP (₱)' }}</span>
                </div>
                
                <a href="{{ route('distributor.profile.edit') }}" class="btn btn-outline-primary btn-sm w-100">
                    <i class="fas fa-edit me-2"></i>Edit Profile
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Recent Orders -->
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-clock me-2"></i>Recent Orders
                </h5>
                <a href="{{ route('orders.index') }}" class="btn btn-sm btn-outline-primary">View All Orders</a>
            </div>
            <div class="card-body">
                @if($stats['recent_orders']->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Order #</th>
                                    <th>Customer</th>
                                    <th>Status</th>
                                    <th>Payment</th>
                                    <th>Total</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($stats['recent_orders'] as $order)
                                <tr>
                                    <td>
                                        <a href="{{ route('orders.show', $order) }}" class="text-decoration-none fw-bold">
                                            {{ $order->order_number }}
                                        </a>
                                    </td>
                                    <td>{{ $order->customer->name }}</td>
                                    <td>
                                        <span class="badge bg-{{ $order->getOrderStatusColor() }}">
                                            {{ $order->getOrderStatusLabel() }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $order->getPaymentStatusColor() }}">
                                            {{ $order->getPaymentStatusLabel() }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="fw-bold">
                                            @if($distributor->is_international)
                                                ${{ number_format($order->total_amount, 2) }}
                                            @else
                                                ₱{{ number_format($order->total_amount, 2) }}
                                            @endif
                                        </span>
                                    </td>
                                    <td>{{ $order->created_at->format('M d, Y') }}</td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('orders.show', $order) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('orders.edit', $order) }}" class="btn btn-sm btn-outline-warning">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-shopping-cart fa-4x text-muted mb-4"></i>
                        <h5 class="text-muted">No orders yet</h5>
                        <p class="text-muted">Start by creating your first order for a customer.</p>
                        <a href="{{ route('orders.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Create First Order
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection 