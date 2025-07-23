@extends('layouts.app')

@section('title', 'User Details')

@section('page-title', 'User Details')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">User Information</h5>
                <div>
                    <a href="{{ route('users.edit', $user) }}" class="btn btn-warning btn-sm">
                        <i class="fas fa-edit me-1"></i>Edit
                    </a>
                    <a href="{{ route('users.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left me-1"></i>Back
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 text-center">
                        @if($user->logo)
                            <img src="{{ asset('storage/' . $user->logo) }}" 
                                 alt="{{ $user->name }}" 
                                 class="img-fluid rounded shadow-sm mb-3" 
                                 style="max-height: 200px; object-fit: contain;">
                        @else
                            <div class="bg-light rounded d-flex align-items-center justify-content-center mb-3" 
                                 style="height: 200px;">
                                <div class="text-muted">
                                    <i class="fas fa-user fa-3x mb-2"></i>
                                    <p>No logo available</p>
                                </div>
                            </div>
                        @endif
                    </div>
                    <div class="col-md-8">
                        <h6 class="text-muted">Basic Information</h6>
                        <table class="table table-borderless">
                            <tr>
                                <td><strong>Name:</strong></td>
                                <td>{{ $user->name }}</td>
                            </tr>
                            <tr>
                                <td><strong>Email:</strong></td>
                                <td>{{ $user->email }}</td>
                            </tr>
                            <tr>
                                <td><strong>Role:</strong></td>
                                <td>
                                    <span class="badge bg-{{ $user->role === 'admin' ? 'danger' : ($user->role === 'distributor' ? 'primary' : 'success') }}">
                                        {{ ucfirst($user->role) }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Created:</strong></td>
                                <td>{{ $user->created_at->format('M d, Y H:i') }}</td>
                            </tr>
                            <tr>
                                <td><strong>Last Updated:</strong></td>
                                <td>{{ $user->updated_at->format('M d, Y H:i') }}</td>
                            </tr>
                        </table>

                        @if($user->role === 'distributor' && $user->distributor)
                            <hr>
                            <h6 class="text-muted">Company Information</h6>
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Company Name:</strong></td>
                                    <td>{{ $user->distributor->company_name }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Phone:</strong></td>
                                    <td>{{ $user->distributor->phone }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Street:</strong></td>
                                    <td>{{ $user->distributor->street }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Barangay/County:</strong></td>
                                    <td>{{ $user->distributor->barangay }}</td>
                                </tr>
                                <tr>
                                    <td><strong>City:</strong></td>
                                    <td>{{ $user->distributor->city }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Province/State:</strong></td>
                                    <td>{{ $user->distributor->province }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Country:</strong></td>
                                    <td>{{ $user->distributor->country }}</td>
                                </tr>
                                <tr>
                                    <td><strong>International Status:</strong></td>
                                    <td>
                                        @if($user->distributor->is_international)
                                            <span class="badge bg-warning">International</span>
                                            <small class="text-muted d-block">Currency: USD ($)</small>
                                        @else
                                            <span class="badge bg-success">Local</span>
                                            <small class="text-muted d-block">Currency: PHP (₱)</small>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Full Address:</strong></td>
                                    <td>{{ $user->distributor->full_address }}</td>
                                </tr>
                            </table>
                        @endif

                        @if($user->orders->count() > 0)
                            <hr>
                            <h6 class="text-muted">Order History</h6>
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Order #</th>
                                            <th>Customer</th>
                                            <th>Total</th>
                                            <th>Status</th>
                                            <th>Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($user->orders->take(5) as $order)
                                            <tr>
                                                <td>{{ $order->order_number }}</td>
                                                <td>{{ $order->customer->name }}</td>
                                                <td>{{ $order->currency === 'PHP' ? '₱' : '$' }}{{ number_format($order->total_amount, 2) }}</td>
                                                <td>
                                                    <span class="badge bg-{{ $order->order_status === 'delivered' ? 'success' : ($order->order_status === 'cancelled' ? 'danger' : 'warning') }}">
                                                        {{ ucfirst(str_replace('_', ' ', $order->order_status)) }}
                                                    </span>
                                                </td>
                                                <td>{{ $order->created_at->format('M d, Y') }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 