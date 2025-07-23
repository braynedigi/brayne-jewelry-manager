@extends('layouts.app')

@section('title', 'Notifications')

@section('page-title', 'Notifications')
@section('page-subtitle', 'Stay updated with system activities')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-bell me-2"></i>All Notifications
                </h5>
                @if($notifications->where('read_at', null)->count() > 0)
                    <form action="{{ route('notifications.mark-all-read') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-check-double me-2"></i>Mark All as Read
                        </button>
                    </form>
                @endif
            </div>
            <div class="card-body">
                @if($notifications->count() > 0)
                    <div class="list-group list-group-flush">
                        @foreach($notifications as $notification)
                            <div class="list-group-item {{ $notification->read_at ? '' : 'bg-light' }}">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="flex-grow-1">
                                        <div class="d-flex align-items-start">
                                            <div class="flex-shrink-0 me-3">
                                                @if(!$notification->read_at)
                                                    <div class="bg-primary rounded-circle" style="width: 10px; height: 10px;"></div>
                                                @else
                                                    <div class="bg-secondary rounded-circle" style="width: 10px; height: 10px;"></div>
                                                @endif
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-1 {{ $notification->read_at ? 'text-muted' : 'fw-bold' }}">
                                                    {{ $notification->title }}
                                                </h6>
                                                <p class="mb-1 text-muted">{{ $notification->message }}</p>
                                                <small class="text-muted">
                                                    {{ $notification->created_at->format('M d, Y g:i A') }}
                                                    @if($notification->email_sent)
                                                        <i class="fas fa-envelope ms-2" title="Email sent"></i>
                                                    @endif
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex-shrink-0 ms-3">
                                        <div class="btn-group" role="group">
                                            @if(!$notification->read_at)
                                                <button type="button" class="btn btn-sm btn-outline-success" 
                                                        onclick="markNotificationAsRead({{ $notification->id }})">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            @endif
                                            <form action="{{ route('notifications.destroy', $notification) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" 
                                                        onclick="return confirm('Are you sure you want to delete this notification?')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    
                    <div class="d-flex justify-content-center mt-4">
                        {{ $notifications->links() }}
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-bell-slash fa-4x text-muted mb-4"></i>
                        <h5 class="text-muted">No notifications yet</h5>
                        <p class="text-muted">You'll see notifications here when there are system updates or activities.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function markNotificationAsRead(notificationId) {
    fetch(`/notifications/${notificationId}/mark-read`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        },
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    });
}
</script>
@endpush 