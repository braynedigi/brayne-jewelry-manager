@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('Real-time notifications initialized');
    
    
    
    // Function to handle status updates
    function handleStatusUpdate(orderId, newStatus) {
        console.log('Handling status update for order:', orderId, 'to status:', newStatus);
        
        // Update orders table if on orders page
        updateOrdersTableRow(orderId, newStatus);
        
        // Update factory dashboard if on factory dashboard
        updateFactoryDashboard(orderId, newStatus);
        
        // Show notification
        showStatusChangeNotification(orderId, newStatus);
    }

    // Update orders table row
    function updateOrdersTableRow(orderId, newStatus) {
        const tableRow = document.querySelector(`tr[data-order-id="${orderId}"]`);
        if (tableRow) {
            console.log('Updating table row for order:', orderId);
            
            // Update status badge
            const statusBadge = tableRow.querySelector('.status-badge');
            if (statusBadge) {
                const statusColors = {
                    'pending_payment': 'warning',
                    'approved': 'info',
                    'in_production': 'primary',
                    'finishing': 'secondary',
                    'ready_for_delivery': 'success',
                    'delivered_to_brayne': 'info',
                    'delivered_to_client': 'success',
                    'cancelled': 'danger'
                };
                const color = statusColors[newStatus] || 'secondary';
                statusBadge.className = `badge bg-${color} status-badge`;
                
                const statusLabels = {
                    'pending_payment': 'Pending Payment',
                    'approved': 'Approved',
                    'in_production': 'In Production',
                    'finishing': 'Finishing',
                    'ready_for_delivery': 'Ready for Delivery',
                    'delivered_to_brayne': 'Delivered to Brayne',
                    'delivered_to_client': 'Delivered to Client',
                    'cancelled': 'Cancelled'
                };
                statusBadge.textContent = statusLabels[newStatus] || newStatus;
            }

            // Add visual feedback
            tableRow.style.backgroundColor = '#fff3cd';
            setTimeout(() => {
                tableRow.style.backgroundColor = '';
            }, 3000);
        }
    }

    // Update factory dashboard
    function updateFactoryDashboard(orderId, newStatus) {
        // Check if we're on the factory dashboard
        if (window.location.pathname.includes('/factory/dashboard')) {
            console.log('Updating factory dashboard for order:', orderId);
            
            // Find the order card
            const orderCard = document.querySelector(`[data-order-id="${orderId}"]`);
            if (orderCard) {
                // Move to appropriate column
                const columnMap = {
                    'approved': '.queue-column',
                    'in_production': '.in-production-column',
                    'finishing': '.finishing-column',
                    'ready_for_delivery': '.ready-column',
                    'delivered_to_brayne': '.delivered-column'
                };

                const targetColumn = document.querySelector(columnMap[newStatus]);
                if (targetColumn && orderCard.parentElement !== targetColumn) {
                    targetColumn.appendChild(orderCard);
                }

                // Update column counts
                updateColumnCounts();

                // Add visual feedback
                orderCard.style.border = '2px solid #28a745';
                orderCard.style.boxShadow = '0 0 10px rgba(40, 167, 69, 0.3)';
                setTimeout(() => {
                    orderCard.style.border = '';
                    orderCard.style.boxShadow = '';
                }, 3000);
            }
        }
    }

    // Update column counts
    function updateColumnCounts() {
        const columns = {
            'approved': '.queue-column',
            'in_production': '.in-production-column',
            'finishing': '.finishing-column',
            'ready_for_delivery': '.ready-column',
            'delivered_to_brayne': '.delivered-column'
        };

        Object.entries(columns).forEach(([status, selector]) => {
            const column = document.querySelector(selector);
            if (column) {
                const count = column.querySelectorAll('[data-order-id]').length;
                const badge = document.querySelector(`.${status.replace('_', '-')}-count`);
                if (badge) {
                    badge.textContent = count;
                }
            }
        });
    }

    // Show status change notification
    function showStatusChangeNotification(orderId, newStatus) {
        const statusLabels = {
            'pending_payment': 'Pending Payment',
            'approved': 'Approved',
            'in_production': 'In Production',
            'finishing': 'Finishing',
            'ready_for_delivery': 'Ready for Delivery',
            'delivered_to_brayne': 'Delivered to Brayne',
            'delivered_to_client': 'Delivered to Client',
            'cancelled': 'Cancelled'
        };

        const statusColors = {
            'pending_payment': 'warning',
            'approved': 'info',
            'in_production': 'primary',
            'finishing': 'secondary',
            'ready_for_delivery': 'success',
            'delivered_to_brayne': 'info',
            'delivered_to_client': 'success',
            'cancelled': 'danger'
        };

        const notification = `
            <div class="toast show" role="alert" aria-live="assertive" aria-atomic="true" style="position: fixed; top: 20px; right: 20px; z-index: 9999; min-width: 300px;">
                <div class="toast-header bg-${statusColors[newStatus] || 'info'} text-white">
                    <i class="fas fa-bell me-2"></i>
                    <strong class="me-auto">Order Status Updated</strong>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast"></button>
                </div>
                <div class="toast-body">
                    <div class="fw-bold">Order #${orderId}</div>
                    <div class="small">
                        <span class="fw-bold text-${statusColors[newStatus] || 'info'}">${statusLabels[newStatus] || newStatus}</span>
                    </div>
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

        // Auto-remove after 5 seconds
        setTimeout(() => {
            const toast = document.querySelector('.toast');
            if (toast) {
                toast.remove();
            }
        }, 5000);
    }

    // Multiple detection methods for form submissions
    document.addEventListener('submit', function(e) {
        console.log('Form submitted:', e.target.action);
        
        // Check if this is a status update form
        if (e.target.action && (e.target.action.includes('/status') || e.target.action.includes('/factory/orders'))) {
            console.log('Status form detected');
            
            // Get the order ID from the form action
            const orderIdMatch = e.target.action.match(/\/orders\/(\d+)\/status/);
            const orderId = orderIdMatch ? orderIdMatch[1] : null;
            
            // Get the new status from the form
            const statusSelect = e.target.querySelector('select[name="order_status"]');
            const newStatus = statusSelect ? statusSelect.value : null;
            
            console.log('Order ID:', orderId, 'New Status:', newStatus);
            
            if (orderId && newStatus) {
                // Add a delay to allow the form to submit
                setTimeout(() => {
                    handleStatusUpdate(orderId, newStatus);
                }, 2000);
            }
        }
    });

    // Listen for button clicks in status modal
    document.addEventListener('click', function(e) {
        // Check for modal submit button
        if (e.target.matches('button[type="submit"]') && e.target.closest('#statusModal')) {
            console.log('Modal submit button clicked');
            
            const form = e.target.closest('form');
            if (form) {
                const orderIdMatch = form.action.match(/\/orders\/(\d+)\/status/);
                const orderId = orderIdMatch ? orderIdMatch[1] : null;
                const statusSelect = form.querySelector('select[name="order_status"]');
                const newStatus = statusSelect ? statusSelect.value : null;
                
                console.log('Modal form - Order ID:', orderId, 'New Status:', newStatus);
                
                if (orderId && newStatus) {
                    setTimeout(() => {
                        handleStatusUpdate(orderId, newStatus);
                    }, 2000);
                }
            }
        }
        
        // Check for quick update buttons
        if (e.target.matches('button[onclick*="quickUpdateStatus"]')) {
            const onclick = e.target.getAttribute('onclick');
            const match = onclick.match(/quickUpdateStatus\((\d+),\s*'([^']+)'\)/);
            if (match) {
                const orderId = match[1];
                const newStatus = match[2];
                
                setTimeout(() => {
                    handleStatusUpdate(orderId, newStatus);
                }, 2000);
            }
        }
    });

    // Listen for form changes to detect when status is selected
    document.addEventListener('change', function(e) {
        if (e.target.matches('select[name="order_status"]')) {
            console.log('Status selected:', e.target.value);
        }
    });

    
});
</script>
@endpush 