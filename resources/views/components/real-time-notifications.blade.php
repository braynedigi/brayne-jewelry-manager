@push('scripts')
<script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Pusher
    const pusher = new Pusher('{{ config('broadcasting.connections.pusher.key') }}', {
        cluster: '{{ config('broadcasting.connections.pusher.options.cluster') }}',
        encrypted: true
    });

    // Get user role for channel subscription
    const userRole = '{{ auth()->user()->role }}';
    const userId = '{{ auth()->user()->id }}';
    const distributorId = '{{ auth()->user()->distributor_id ?? "" }}';

    // Subscribe to role-based channels
    const channels = [];

    // Admin channel
    if (userRole === 'admin') {
        channels.push(pusher.subscribe('private-admin'));
    }

    // Factory channel
    if (userRole === 'factory') {
        channels.push(pusher.subscribe('private-factory'));
    }

    // Distributor channel
    if (userRole === 'distributor' && distributorId) {
        channels.push(pusher.subscribe('private-distributor.' + distributorId));
    }

    // User-specific notifications
    channels.push(pusher.subscribe('private-user.' + userId));

    // General notifications
    channels.push(pusher.subscribe('private-notifications'));

    // Listen for order status changes
    channels.forEach(channel => {
        channel.bind('order.status.changed', function(data) {
            showStatusChangeNotification(data);
            updateOrderStatus(data);
        });

        channel.bind('notification.new', function(data) {
            showGeneralNotification(data);
        });
    });

    // Show status change notification
    function showStatusChangeNotification(data) {
        const notification = `
            <div class="toast show" role="alert" aria-live="assertive" aria-atomic="true" style="position: fixed; top: 20px; right: 20px; z-index: 9999; min-width: 300px;">
                <div class="toast-header bg-${getStatusColor(data.new_status)} text-white">
                    <i class="fas fa-bell me-2"></i>
                    <strong class="me-auto">Order Status Updated</strong>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast"></button>
                </div>
                <div class="toast-body">
                    <div class="fw-bold">Order #${data.order_number}</div>
                    <div class="small">
                        <span class="text-muted">${data.previous_status} → </span>
                        <span class="fw-bold text-${getStatusColor(data.new_status)}">${data.status_label}</span>
                    </div>
                    <div class="small text-muted mt-1">
                        Changed by: ${data.changed_by} (${data.changed_by_role})
                    </div>
                    ${data.notes ? `<div class="small mt-1"><em>"${data.notes}"</em></div>` : ''}
                    <div class="mt-2">
                        <a href="/orders/${data.order_id}" class="btn btn-sm btn-primary">View Order</a>
                        <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="toast">Dismiss</button>
                    </div>
                </div>
            </div>
        `;

        // Remove existing notifications
        document.querySelectorAll('.toast').forEach(toast => toast.remove());

        // Add new notification
        document.body.insertAdjacentHTML('beforeend', notification);

        // Auto-remove after 10 seconds
        setTimeout(() => {
            const toast = document.querySelector('.toast');
            if (toast) {
                toast.remove();
            }
        }, 10000);
    }

    // Show general notification
    function showGeneralNotification(data) {
        const notification = `
            <div class="toast show" role="alert" aria-live="assertive" aria-atomic="true" style="position: fixed; top: 20px; right: 20px; z-index: 9999; min-width: 300px;">
                <div class="toast-header bg-${getNotificationColor(data.type)} text-white">
                    <i class="fas fa-${getNotificationIcon(data.type)} me-2"></i>
                    <strong class="me-auto">${data.title}</strong>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast"></button>
                </div>
                <div class="toast-body">
                    <div>${data.message}</div>
                    <div class="mt-2">
                        <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="toast">Dismiss</button>
                    </div>
                </div>
            </div>
        `;

        // Remove existing notifications
        document.querySelectorAll('.toast').forEach(toast => toast.remove());

        // Add new notification
        document.body.insertAdjacentHTML('beforeend', notification);

        // Auto-remove after 8 seconds
        setTimeout(() => {
            const toast = document.querySelector('.toast');
            if (toast) {
                toast.remove();
            }
        }, 8000);
    }

    // Update order status in real-time on the page
    function updateOrderStatus(data) {
        // Update status badge if on order page
        const statusBadge = document.querySelector(`[data-order-id="${data.order_id}"] .status-badge`);
        if (statusBadge) {
            statusBadge.className = `badge bg-${getStatusColor(data.new_status)} status-badge`;
            statusBadge.textContent = data.status_label;
        }

        // Update status in order cards
        const orderCard = document.querySelector(`[data-order-id="${data.order_id}"]`);
        if (orderCard) {
            const cardStatus = orderCard.querySelector('.order-status');
            if (cardStatus) {
                cardStatus.className = `order-status badge bg-${getStatusColor(data.new_status)}`;
                cardStatus.textContent = data.status_label;
            }
        }

        // Update dashboard counters if on dashboard
        updateDashboardCounters(data);
    }

    // Update dashboard counters
    function updateDashboardCounters(data) {
        const counters = {
            'pending_payment': '.pending-payment-count',
            'approved': '.approved-count',
            'in_production': '.in-production-count',
            'finishing': '.finishing-count',
            'ready_for_delivery': '.ready-count',
            'delivered_to_brayne': '.delivered-brayne-count',
            'delivered_to_client': '.delivered-client-count'
        };

        // Decrease previous status counter
        if (counters[data.previous_status]) {
            const prevCounter = document.querySelector(counters[data.previous_status]);
            if (prevCounter) {
                const currentCount = parseInt(prevCounter.textContent) || 0;
                prevCounter.textContent = Math.max(0, currentCount - 1);
            }
        }

        // Increase new status counter
        if (counters[data.new_status]) {
            const newCounter = document.querySelector(counters[data.new_status]);
            if (newCounter) {
                const currentCount = parseInt(newCounter.textContent) || 0;
                newCounter.textContent = currentCount + 1;
            }
        }
    }

    // Helper functions
    function getStatusColor(status) {
        const colors = {
            'pending_payment': 'warning',
            'approved': 'info',
            'in_production': 'primary',
            'finishing': 'secondary',
            'ready_for_delivery': 'success',
            'delivered_to_brayne': 'info',
            'delivered_to_client': 'success',
            'cancelled': 'danger'
        };
        return colors[status] || 'secondary';
    }

    function getNotificationColor(type) {
        const colors = {
            'success': 'success',
            'error': 'danger',
            'warning': 'warning',
            'info': 'info'
        };
        return colors[type] || 'info';
    }

    function getNotificationIcon(type) {
        const icons = {
            'success': 'check-circle',
            'error': 'exclamation-triangle',
            'warning': 'exclamation-circle',
            'info': 'info-circle'
        };
        return icons[type] || 'bell';
    }
});
</script>
@endpush 