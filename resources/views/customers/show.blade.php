@extends('layouts.app')

@section('title', 'Customer Details')

@section('page-title', 'Customer Details')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Customer: {{ $customer->name }}</h5>
                <div>
                    <a href="{{ route('customers.edit', $customer) }}" class="btn btn-primary">
                        <i class="fas fa-edit me-2"></i>Edit
                    </a>
                    <a href="{{ route('customers.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Back to List
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td><strong>Name:</strong></td>
                                <td>{{ $customer->name }}</td>
                            </tr>
                            <tr>
                                <td><strong>Email:</strong></td>
                                <td>
                                    @if($customer->email)
                                        <a href="mailto:{{ $customer->email }}">{{ $customer->email }}</a>
                                    @else
                                        <span class="text-muted">Not provided</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Phone:</strong></td>
                                <td>
                                    @if($customer->phone)
                                        <a href="tel:{{ $customer->phone }}">{{ $customer->phone }}</a>
                                    @else
                                        <span class="text-muted">Not provided</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Address:</strong></td>
                                <td>
                                    @if($customer->hasAddress())
                                        <div class="address-details">
                                            @if($customer->street)
                                                <div><strong>Street:</strong> {{ $customer->street }}</div>
                                            @endif
                                            @if($customer->barangay)
                                                <div><strong>Barangay/County:</strong> {{ $customer->barangay }}</div>
                                            @endif
                                            @if($customer->city)
                                                <div><strong>City:</strong> {{ $customer->city }}</div>
                                            @endif
                                            @if($customer->province)
                                                <div><strong>Province/State:</strong> {{ $customer->province }}</div>
                                            @endif
                                            @if($customer->country)
                                                <div><strong>Country:</strong> {{ $customer->country }}</div>
                                            @endif
                                            <hr class="my-2">
                                            <div class="text-muted small">
                                                <strong>Full Address:</strong> {{ $customer->full_address }}
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-muted">No address provided</span>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        @if(auth()->user()->isAdmin())
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Assigned Distributor:</strong></td>
                                    <td>
                                        @if($customer->distributor)
                                            <div class="d-flex align-items-center">
                                                @if($customer->distributor->user->logo)
                                                    <img src="{{ asset('storage/' . $customer->distributor->user->logo) }}" 
                                                         alt="Logo" class="rounded-circle me-2" 
                                                         style="width: 40px; height: 40px; object-fit: cover;">
                                                @endif
                                                <div>
                                                    <div class="fw-bold">{{ $customer->distributor->user->name }}</div>
                                                    <div class="text-muted">{{ $customer->distributor->company_name }}</div>
                                                    @if($customer->distributor->is_international)
                                                        <span class="badge bg-warning">International</span>
                                                        <small class="text-muted d-block">Currency: USD ($)</small>
                                                    @else
                                                        <span class="badge bg-success">Local</span>
                                                        <small class="text-muted d-block">Currency: PHP (₱)</small>
                                                    @endif
                                                </div>
                                            </div>
                                        @else
                                            <span class="text-muted">No distributor assigned</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Distributor Address:</strong></td>
                                    <td>
                                        @if($customer->distributor)
                                            {{ $customer->distributor->full_address }}
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        @endif
                    </div>
                </div>

                @if($customer->orders->count() > 0)
                    <hr>
                    <h6 class="text-muted">Order History</h6>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Order #</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>Total</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($customer->orders->take(5) as $order)
                                    <tr>
                                        <td>{{ $order->order_number }}</td>
                                        <td>{{ $order->created_at->format('M d, Y') }}</td>
                                        <td>
                                            <span class="badge bg-{{ $order->getStatusColor() }}">
                                                {{ $order->getStatusLabel() }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($order->distributor && $order->distributor->is_international)
                                                ${{ number_format($order->total_amount, 2) }}
                                            @else
                                                ₱{{ number_format($order->total_amount, 2) }}
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('orders.show', $order) }}" 
                                               class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        @if($customer->orders->count() > 5)
                            <div class="text-center mt-2">
                                <small class="text-muted">Showing 5 of {{ $customer->orders->count() }} orders</small>
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection 