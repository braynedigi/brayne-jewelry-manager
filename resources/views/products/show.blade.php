@extends('layouts.app')

@section('title', 'Product Details')

@section('page-title', 'Product Details')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-10">
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Product Information</h5>
                <div>
                    <a href="{{ route('products.edit', $product) }}" class="btn btn-warning btn-sm">
                        <i class="fas fa-edit me-1"></i>Edit
                    </a>
                    <a href="{{ route('products.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left me-1"></i>Back
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        @if($product->hasImage())
                            <div class="text-center mb-3">
                                <div class="image-container">
                                    <img src="{{ $product->getImageUrl() }}" 
                                         alt="{{ $product->name }}" 
                                         class="product-image img-fluid" 
                                         style="max-height: 300px; object-fit: contain;">
                                    <div class="image-overlay">
                                        <i class="fas fa-expand"></i>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="text-center mb-3">
                                <div class="image-placeholder" 
                                     style="height: 300px;">
                                    <div>
                                        <i class="fas fa-image fa-3x mb-2"></i>
                                        <p>No image available</p>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                    <div class="col-md-8">
                        <h6 class="text-muted">Basic Information</h6>
                        <table class="table table-borderless">
                            <tr>
                                <td><strong>Name:</strong></td>
                                <td>{{ $product->name }}</td>
                            </tr>
                            <tr>
                                <td><strong>SKU:</strong></td>
                                <td>{{ $product->sku }}</td>
                            </tr>
                            <tr>
                                <td><strong>Category:</strong></td>
                                <td>{{ $product->category }}</td>
                            </tr>
                            <tr>
                                <td><strong>Sub Category:</strong></td>
                                <td>{{ $product->sub_category }}</td>
                            </tr>
                            <tr>
                                <td><strong>Pricing:</strong></td>
                                <td>
                                    @if($product->metals && count($product->metals) > 0)
                                        <div class="row">
                                            <div class="col-md-6">
                                                <h6 class="text-primary">Local Pricing (PHP)</h6>
                                                @foreach($product->metals as $metal)
                                                    @if(isset($product->local_pricing[$metal]))
                                                        <div class="mb-1">
                                                            <strong>{{ $metal }}:</strong> ₱{{ number_format($product->local_pricing[$metal], 2) }}
                                                        </div>
                                                    @endif
                                                @endforeach
                                            </div>
                                            <div class="col-md-6">
                                                <h6 class="text-success">International Pricing (USD)</h6>
                                                @foreach($product->metals as $metal)
                                                    @if(isset($product->international_pricing[$metal]))
                                                        <div class="mb-1">
                                                            <strong>{{ $metal }}:</strong> ${{ number_format($product->international_pricing[$metal], 2) }}
                                                        </div>
                                                    @endif
                                                @endforeach
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-muted">No pricing set</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Status:</strong></td>
                                <td>
                                    <span class="badge bg-{{ $product->is_active ? 'success' : 'danger' }}">
                                        {{ $product->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Currency:</strong></td>
                                <td>{{ $product->currency === 'PHP' ? 'Philippine Peso (₱)' : 'US Dollar ($)' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Created:</strong></td>
                                <td>{{ $product->created_at->format('M d, Y H:i') }}</td>
                            </tr>
                            <tr>
                                <td><strong>Last Updated:</strong></td>
                                <td>{{ $product->updated_at->format('M d, Y H:i') }}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                @if($product->metals)
                    <hr>
                    <h6 class="text-muted">Available Metals & Pricing</h6>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Metal</th>
                                    <th>Price</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($product->metals as $metal)
                                    <tr>
                                        <td>{{ $metal }}</td>
                                        <td>
                                            @if($product->getFormattedPriceForMetal($metal))
                                                <span class="text-primary fw-bold">{{ $product->getFormattedPriceForMetal($metal) }}</span>
                                            @else
                                                <span class="text-muted">No price set</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif

                @if($product->fonts && count($product->fonts) > 0)
                    <hr>
                    <h6 class="text-muted">Available Fonts</h6>
                    <div class="d-flex flex-wrap gap-2">
                        @foreach($product->fonts as $font)
                            <span class="badge bg-info">{{ $font }}</span>
                        @endforeach
                    </div>
                @endif

                @if($product->orders->count() > 0)
                    <hr>
                    <h6 class="text-muted">Order History</h6>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Order #</th>
                                    <th>Customer</th>
                                    <th>Metal</th>
                                    <th>Quantity</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($product->orders->take(5) as $order)
                                    <tr>
                                        <td>{{ $order->order_number }}</td>
                                        <td>{{ $order->customer->name }}</td>
                                        <td>{{ $order->pivot->metal ?? 'N/A' }}</td>
                                        <td>{{ $order->pivot->quantity }}</td>
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
@endsection 