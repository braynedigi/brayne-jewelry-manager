@extends('emails.layout')

@section('content')
    <h2>Order Status Updated</h2>
    
    <p>Hello,</p>
    
    <p>The status of order <strong>#{{ $order->order_number }}</strong> has been updated.</p>
    
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
            <span class="info-label">Previous Status:</span>
            <span class="info-value">
                <span class="status-badge status-{{ str_replace('_', '-', $oldStatus) }}">
                    {{ ucwords(str_replace('_', ' ', $oldStatus)) }}
                </span>
            </span>
        </div>
        
        <div class="info-row">
            <span class="info-label">New Status:</span>
            <span class="info-value">
                <span class="status-badge status-{{ str_replace('_', '-', $newStatus) }}">
                    {{ ucwords(str_replace('_', ' ', $newStatus)) }}
                </span>
            </span>
        </div>
        
        @if($order->estimated_delivery_ready)
        <div class="info-row">
            <span class="info-label">Estimated Delivery:</span>
            <span class="info-value">{{ $order->estimated_delivery_ready->format('M d, Y') }}</span>
        </div>
        @endif
        
        @if($order->total_amount)
        <div class="info-row">
            <span class="info-label">Total Amount:</span>
            <span class="info-value">${{ number_format($order->total_amount, 2) }}</span>
        </div>
        @endif
    </div>
    
    @if($notes)
    <div class="order-details">
        <h3>Production Notes</h3>
        <p>{{ $notes }}</p>
    </div>
    @endif
    
    <p>You can view the complete order details by clicking the button below:</p>
    
    <a href="{{ url('/orders/' . $order->id) }}" class="btn">View Order Details</a>
    
    <p>If you have any questions, please contact your administrator.</p>
    
    <p>Best regards,<br>
    Jewelry Manager Team</p>
@endsection 