@extends('layouts.app')

@php
use App\Models\Order;
@endphp

@section('title', 'Admin Dashboard')

@section('page-title', 'Admin Dashboard')
@section('page-subtitle', 'Manage your jewelry business operations')

@section('content')
<div class="row">
    <!-- Stats Cards -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stats-card">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="stats-label">Total Users</div>
                    <div class="stats-number">{{ $totalUsers }}</div>
                </div>
                <div class="stats-icon">
                    <i class="fas fa-users"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stats-card" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="stats-label">Distributors</div>
                    <div class="stats-number">{{ $totalDistributors }}</div>
                </div>
                <div class="stats-icon">
                    <i class="fas fa-building"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stats-card" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="stats-label">Total Orders</div>
                    <div class="stats-number">{{ $totalOrders }}</div>
                </div>
                <div class="stats-icon">
                    <i class="fas fa-shopping-cart"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stats-card" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="stats-label">Products</div>
                    <div class="stats-number">{{ $totalProducts }}</div>
                </div>
                <div class="stats-icon">
                    <i class="fas fa-gem"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stats-card" style="background: linear-gradient(135deg, #ffecd2 0%, #fcb69f 100%);">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="stats-label">Pending Orders</div>
                    <div class="stats-number">{{ $pendingOrders }}</div>
                </div>
                <div class="stats-icon">
                    <i class="fas fa-clock"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stats-card" style="background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%);">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="stats-label">Couriers</div>
                    <div class="stats-number">{{ $totalCouriers }}</div>
                </div>
                <div class="stats-icon">
                    <i class="fas fa-truck"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stats-card" style="background: linear-gradient(135deg, #ff9a9e 0%, #fecfef 100%);">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="stats-label">Customers</div>
                    <div class="stats-number">{{ $totalCustomers }}</div>
                </div>
                <div class="stats-icon">
                    <i class="fas fa-user-friends"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stats-card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="stats-label">Total Revenue</div>
                    <div class="stats-number">₱{{ number_format(Order::sum('total_amount'), 2) }}</div>
                </div>
                <div class="stats-icon">
                    <i class="fas fa-dollar-sign"></i>
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
                        <a href="{{ route('users.create') }}" class="btn btn-primary w-100 p-3">
                            <i class="fas fa-user-plus fa-2x mb-2"></i>
                            <div>Add New User</div>
                            <small class="opacity-75">Create distributor or factory account</small>
                        </a>
                    </div>
                    <div class="col-md-6 mb-3">
                        <a href="{{ route('products.create') }}" class="btn btn-success w-100 p-3">
                            <i class="fas fa-plus fa-2x mb-2"></i>
                            <div>Add New Product</div>
                            <small class="opacity-75">Create new jewelry product</small>
                        </a>
                    </div>
                    <div class="col-md-6 mb-3">
                        <a href="{{ route('orders.create') }}" class="btn btn-info w-100 p-3">
                            <i class="fas fa-shopping-cart fa-2x mb-2"></i>
                            <div>Create Order</div>
                            <small class="opacity-75">Place new order for customer</small>
                        </a>
                    </div>
                    <div class="col-md-6 mb-3">
                        <a href="{{ route('admin.approval.index') }}" class="btn btn-warning w-100 p-3">
                            <i class="fas fa-clipboard-check fa-2x mb-2"></i>
                            <div>Order Approval</div>
                            <small class="opacity-75">Review pending orders</small>
                        </a>
                    </div>
                    <div class="col-md-6 mb-3">
                        <a href="{{ route('customers.create') }}" class="btn btn-warning w-100 p-3">
                            <i class="fas fa-user-friends fa-2x mb-2"></i>
                            <div>Add Customer</div>
                            <small class="opacity-75">Register new customer</small>
                        </a>
                    </div>
                    <div class="col-md-6 mb-3">
                        <a href="{{ route('admin.settings.index') }}" class="btn btn-secondary w-100 p-3">
                            <i class="fas fa-cog fa-2x mb-2"></i>
                            <div>System Settings</div>
                            <small class="opacity-75">Manage couriers & configuration</small>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- System Overview -->
    <div class="col-lg-4 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-chart-pie me-2"></i>System Overview
                </h5>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span>Couriers</span>
                    <a href="{{ route('admin.settings.index') }}" class="badge bg-primary text-decoration-none">{{ $totalCouriers }}</a>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span>Customers</span>
                    <span class="badge bg-success">{{ $totalCustomers }}</span>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <span>Active Products</span>
                    <span class="badge bg-info">{{ $totalProducts }}</span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Recent Orders -->
    <div class="col-lg-6 mb-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-clock me-2"></i>Recent Orders
                </h5>
                <a href="{{ route('orders.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="card-body">
                @php
                    $recentOrders = \App\Models\Order::with(['customer', 'distributor.user'])->latest()->take(5)->get();
                @endphp
                
                @if($recentOrders->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Order #</th>
                                    <th>Customer</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentOrders as $order)
                                <tr>
                                    <td>
                                        <a href="{{ route('orders.show', $order) }}" class="text-decoration-none">
                                            {{ $order->order_number }}
                                        </a>
                                    </td>
                                    <td>{{ $order->customer->name }}</td>
                                    <td>
                                        <span class="badge bg-{{ $order->getOrderStatusColor() }}">
                                            {{ $order->getOrderStatusLabel() }}
                                        </span>
                                    </td>
                                    <td>{{ $order->created_at->format('M d') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                        <h6 class="text-muted">No orders yet</h6>
                        <p class="text-muted small">Orders will appear here once created.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Recent Products -->
    <div class="col-lg-6 mb-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-gem me-2"></i>Recent Products
                </h5>
                <a href="{{ route('products.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="card-body">
                @php
                    $recentProducts = \App\Models\Product::latest()->take(5)->get();
                @endphp
                
                @if($recentProducts->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Category</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentProducts as $product)
                                <tr>
                                    <td>
                                        <a href="{{ route('products.show', $product) }}" class="text-decoration-none">
                                            {{ $product->name }}
                                        </a>
                                    </td>
                                    <td>{{ $product->category }}</td>
                                    <td>
                                        <span class="badge bg-{{ $product->is_active ? 'success' : 'secondary' }}">
                                            {{ $product->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td>{{ $product->created_at->format('M d') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-gem fa-3x text-muted mb-3"></i>
                        <h6 class="text-muted">No products yet</h6>
                        <p class="text-muted small">Products will appear here once created.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection 