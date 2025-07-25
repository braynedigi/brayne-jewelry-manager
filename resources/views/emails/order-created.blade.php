@extends('emails.layout')

@section('content')
    <h2>New Order Created</h2>
    
    <p>Hello,</p>
    
    <p>A new order <strong>#{{ $order->order_number }}</strong> has been created in the system.</p>
    
    <div class="order-details">
        <h3>Order Information</h3>
        
        <div class="info-row">
            <span class="info-label">Order Number:</span>
            <span class="info-value">#{{ $order->order_number }}</span>
        </div>
        
        <div class="info-row">
            <span class="info-label">Customer:</span>
            <span class="info-value">{{ $order->customer->name ?? 'N/A' }}</span>
        </div>
        
        <div class="info-row">
            <span class="info-label">Distributor:</span>
            <span class="info-value">{{ $order->distributor->name ?? 'N/A' }}</span>
        </div>
        
        <div class="info-row">
            <span class="info-label">Status:</span>
            <span class="info-value">
                <span class="status-badge status-{{ str_replace('_', '-', $order->order_status) }}">
                    {{ ucwords(str_replace('_', ' ', $order->order_status)) }}
                </span>
            </span>
        </div>
        
        <div class="info-row">
            <span class="info-label">Priority:</span>
            <span class="info-value">{{ ucfirst($order->priority) }}</span>
        </div>
        
        @if($order->total_amount)
        <div class="info-row">
            <span class="info-label">Total Amount:</span>
            <span class="info-value">${{ number_format($order->total_amount, 2) }}</span>
        </div>
        @endif
        
        @if($order->estimated_delivery_ready)
        <div class="info-row">
            <span class="info-label">Estimated Delivery:</span>
            <span class="info-value">{{ $order->estimated_delivery_ready->format('M d, Y') }}</span>
        </div>
        @endif
    </div>
    
    @if($order->products->count() > 0)
    <div class="order-details">
        <h3>Products</h3>
        @foreach($order->products as $product)
        <div class="info-row">
            <span class="info-label">{{ $product->name }}</span>
            <span class="info-value">
                Qty: {{ $product->pivot->quantity }} | 
                Metal: {{ $product->pivot->metal }}
            </span>
        </div>
        @endforeach
    </div>
    @endif
    
    <p>You can view the complete order details by clicking the button below:</p>
    
    <a href="{{ url('/orders/' . $order->id) }}" class="btn">View Order Details</a>
    
    <p>Please review and process this order according to your workflow.</p>
    
    <p>Best regards,<br>
    Jewelry Manager Team</p>
@endsection 