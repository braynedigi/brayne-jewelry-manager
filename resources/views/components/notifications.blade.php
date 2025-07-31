@php
use App\Services\NotificationService;
use App\Models\Notification;

$unreadCount = NotificationService::getUnreadCount(auth()->id());
$recentNotifications = Notification::where('user_id', auth()->id())
    ->latest()
    ->take(5)
    ->get();

// Choose notification style: 'pulse', 'dot', 'minimal', 'modern'
$notificationStyle = 'modern'; // You can change this to switch styles
@endphp

<div class="dropdown notification-dropdown" style="position: relative; z-index: 99999;">
    <button class="btn btn-link text-white position-relative notification-btn" type="button" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="fas fa-bell fa-lg"></i>
        @if($unreadCount > 0)
            @if($notificationStyle === 'pulse')
                <div class="notification-indicator pulse-style">
                    <span class="notification-count">{{ $unreadCount > 99 ? '99+' : $unreadCount }}</span>
                    <div class="notification-pulse"></div>
                </div>
            @elseif($notificationStyle === 'dot')
                <div class="notification-indicator dot-style">
                    <span class="notification-dot"></span>
                    @if($unreadCount > 9)
                        <span class="notification-count">{{ $unreadCount > 99 ? '99+' : $unreadCount }}</span>
                    @endif
                </div>
            @elseif($notificationStyle === 'minimal')
                <div class="notification-indicator minimal-style">
                    <span class="notification-dot-minimal"></span>
                </div>
            @else
                <div class="notification-indicator modern-style">
                    <span class="notification-count">{{ $unreadCount > 99 ? '99+' : $unreadCount }}</span>
                    <div class="notification-pulse"></div>
                </div>
            @endif
        @endif
    </button>
    
    <div class="dropdown-menu dropdown-menu-end notification-menu" style="width: 480px; max-height: 450px; overflow-y: auto; z-index: 99999 !important; position: absolute !important;">
        <div class="dropdown-header notification-header">
            <div class="d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold">Notifications</h6>
                @if($unreadCount > 0)
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-primary rounded-pill">{{ $unreadCount }}</span>
                        <a href="{{ route('notifications.index') }}" class="text-decoration-none small text-primary">View All</a>
                    </div>
                @endif
            </div>
        </div>
        
        <div class="dropdown-divider"></div>
        
        @if($recentNotifications->count() > 0)
            @foreach($recentNotifications as $notification)
                <div class="dropdown-item notification-item {{ $notification->read_at ? 'read' : 'unread' }}" 
                     onclick="markNotificationAsRead({{ $notification->id }})">
                    <div class="d-flex align-items-start">
                        <div class="flex-shrink-0 notification-icon">
                            @if(!$notification->read_at)
                                <div class="unread-indicator"></div>
                            @endif
                            <i class="fas fa-info-circle text-primary"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <div class="notification-title">{{ $notification->title }}</div>
                            <div class="notification-message">{{ Str::limit($notification->message, 120) }}</div>
                            <div class="notification-time">{{ $notification->created_at->diffForHumans() }}</div>
                        </div>
                    </div>
                </div>
            @endforeach
        @else
            <div class="dropdown-item text-center text-muted py-4">
                <div class="empty-notifications">
                    <i class="fas fa-bell-slash fa-3x mb-3 text-muted"></i>
                    <div class="fw-bold">No notifications</div>
                    <div class="small">You're all caught up!</div>
                </div>
            </div>
        @endif
        
        @if($recentNotifications->count() > 0)
            <div class="dropdown-divider"></div>
            <div class="dropdown-item text-center">
                <a href="{{ route('notifications.index') }}" class="text-decoration-none text-primary fw-bold">
                    <i class="fas fa-external-link-alt me-1"></i>View All Notifications
                </a>
            </div>
        @endif
    </div>
</div>

<style>
.notification-dropdown {
    position: relative;
    z-index: 99999 !important;
}

.notification-btn {
    position: relative;
    padding: 0.5rem;
    border-radius: 50%;
    transition: all 0.3s ease;
    background: rgba(255, 255, 255, 0.1);
    border: none;
}

.notification-btn:hover {
    background: rgba(255, 255, 255, 0.2);
    transform: scale(1.05);
}

/* Pulse Style (Default) */
.notification-indicator.pulse-style {
    position: absolute;
    top: -5px;
    right: -5px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.notification-count {
    background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
    color: white;
    font-size: 0.7rem;
    font-weight: 700;
    padding: 0.2rem 0.4rem;
    border-radius: 10px;
    min-width: 18px;
    text-align: center;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
    z-index: 2;
    position: relative;
}

.notification-pulse {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 24px;
    height: 24px;
    background: rgba(239, 68, 68, 0.3);
    border-radius: 50%;
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0% {
        transform: translate(-50%, -50%) scale(0.8);
        opacity: 1;
    }
    100% {
        transform: translate(-50%, -50%) scale(1.5);
        opacity: 0;
    }
}

/* Dot Style */
.notification-indicator.dot-style {
    position: absolute;
    top: -2px;
    right: -2px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.notification-dot {
    width: 12px;
    height: 12px;
    background: #ef4444;
    border-radius: 50%;
    border: 2px solid white;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
    animation: blink 2s infinite;
}

/* Minimal Style */
.notification-indicator.minimal-style {
    position: absolute;
    top: 0;
    right: 0;
}

.notification-dot-minimal {
    width: 8px;
    height: 8px;
    background: #ef4444;
    border-radius: 50%;
    border: 1px solid white;
    animation: blink 2s infinite;
}

/* Modern Style */
.notification-indicator.modern-style {
    position: absolute;
    top: -5px;
    right: -5px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.notification-menu {
    border: none;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
    border-radius: 12px;
    padding: 0;
    margin-top: 0.5rem;
    z-index: 99999 !important;
    position: absolute !important;
    top: 100% !important;
    right: 0 !important;
    left: auto !important;
}

.notification-header {
    background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
    padding: 1rem 1.25rem;
    border-radius: 12px 12px 0 0;
    border-bottom: 1px solid #e2e8f0;
}

.notification-item {
    padding: 1rem 1.25rem;
    border: none;
    transition: all 0.2s ease;
    cursor: pointer;
}

.notification-item:hover {
    background: #f8fafc;
}

.notification-item.unread {
    background: rgba(59, 130, 246, 0.05);
    border-left: 3px solid #3b82f6;
}

.notification-item.read {
    opacity: 0.8;
}

.notification-icon {
    position: relative;
    width: 24px;
    height: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.unread-indicator {
    position: absolute;
    top: -2px;
    right: -2px;
    width: 8px;
    height: 8px;
    background: #3b82f6;
    border-radius: 50%;
    animation: blink 2s infinite;
}

@keyframes blink {
    0%, 50% { opacity: 1; }
    51%, 100% { opacity: 0.3; }
}

.notification-title {
    font-weight: 600;
    font-size: 0.875rem;
    color: #1e293b;
    margin-bottom: 0.25rem;
}

.notification-message {
    font-size: 0.8rem;
    color: #64748b;
    line-height: 1.4;
    margin-bottom: 0.25rem;
}

.notification-time {
    font-size: 0.75rem;
    color: #94a3b8;
}

.empty-notifications {
    padding: 1rem 0;
}

.empty-notifications i {
    opacity: 0.5;
}
</style>

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
            // Update the notification count without page reload
            updateNotificationCount();
        }
    });
}

function updateNotificationCount() {
    // Update the notification count via AJAX
    fetch('/api/notifications/count')
        .then(response => response.json())
        .then(data => {
            const countElement = document.querySelector('.notification-count');
            const indicator = document.querySelector('.notification-indicator');
            
            if (data.count > 0) {
                if (countElement) {
                    countElement.textContent = data.count > 99 ? '99+' : data.count;
                }
                if (!indicator) {
                    // Recreate indicator if it was removed
                    const btn = document.querySelector('.notification-btn');
                    const newIndicator = document.createElement('div');
                    newIndicator.className = 'notification-indicator modern-style';
                    newIndicator.innerHTML = `
                        <span class="notification-count">${data.count > 99 ? '99+' : data.count}</span>
                        <div class="notification-pulse"></div>
                    `;
                    btn.appendChild(newIndicator);
                }
            } else {
                // Remove indicator if no notifications
                if (indicator) {
                    indicator.remove();
                }
            }
        });
}
</script> 