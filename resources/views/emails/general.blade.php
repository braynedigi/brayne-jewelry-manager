@extends('emails.layout')

@section('content')
    <h2>{{ $title }}</h2>
    
    <p>Hello,</p>
    
    <div class="order-details">
        <p>{{ $message }}</p>
    </div>
    
    @if(!empty($data))
    <div class="order-details">
        <h3>Additional Information</h3>
        @foreach($data as $key => $value)
        <div class="info-row">
            <span class="info-label">{{ ucwords(str_replace('_', ' ', $key)) }}:</span>
            <span class="info-value">{{ is_array($value) ? json_encode($value) : $value }}</span>
        </div>
        @endforeach
    </div>
    @endif
    
    <p>You can access the system by clicking the button below:</p>
    
    <a href="{{ url('/') }}" class="btn">Access System</a>
    
    <p>If you have any questions, please contact your administrator.</p>
    
    <p>Best regards,<br>
    Jewelry Manager Team</p>
@endsection 