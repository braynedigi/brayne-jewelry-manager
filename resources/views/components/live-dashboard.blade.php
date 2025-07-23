@push('scripts')
<script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Pusher for dashboard updates
    const pusher = new Pusher('{{ config('broadcasting.connections.pusher.key') }}', {
        cluster: '{{ config('broadcasting.connections.pusher.options.cluster') }}',
        encrypted: true
    });

    const userRole = '{{ auth()->user()->role }}';
    const userId = '{{ auth()->user()->id }}';

    // Subscribe to appropriate channels
    const channels = [];
    
    if (userRole === 'admin') {
        channels.push(pusher.subscribe('private-admin'));
    } elseif (userRole === 'factory') {
        channels.push(pusher.subscribe('private-factory'));
    } elseif (userRole === 'distributor') {
        const distributorId = '{{ auth()->user()->distributor_id ?? "" }}';
        if (distributorId) {
            channels.push(pusher.subscribe('private-distributor.' + distributorId));
        }
    }

    // Listen for order status changes
    channels.forEach(channel => {
        channel.bind('order.status.changed', function(data) {
            updateDashboardStats(data);
            updateOrderLists(data);
            updateCharts(data);
        });
    });

    // Update dashboard statistics
    function updateDashboardStats(data) {
        // Update order count cards
        const statusCounters = {
            'pending_payment': '.pending-payment-count',
            'approved': '.approved-count',
            'in_production': '.in-production-count',
            'finishing': '.finishing-count',
            'ready_for_delivery': '.ready-count',
            'delivered_to_brayne': '.delivered-brayne-count',
            'delivered_to_client': '.delivered-client-count'
        };

        // Decrease previous status counter
        if (statusCounters[data.previous_status]) {
            const prevCounter = document.querySelector(statusCounters[data.previous_status]);
            if (prevCounter) {
                const currentCount = parseInt(prevCounter.textContent) || 0;
                prevCounter.textContent = Math.max(0, currentCount - 1);
                
                // Add animation
                prevCounter.style.transition = 'all 0.3s ease';
                prevCounter.style.transform = 'scale(0.9)';
                setTimeout(() => {
                    prevCounter.style.transform = 'scale(1)';
                }, 300);
            }
        }

        // Increase new status counter
        if (statusCounters[data.new_status]) {
            const newCounter = document.querySelector(statusCounters[data.new_status]);
            if (newCounter) {
                const currentCount = parseInt(newCounter.textContent) || 0;
                newCounter.textContent = currentCount + 1;
                
                // Add animation
                newCounter.style.transition = 'all 0.3s ease';
                newCounter.style.transform = 'scale(1.1)';
                newCounter.style.backgroundColor = '#28a745';
                setTimeout(() => {
                    newCounter.style.transform = 'scale(1)';
                    newCounter.style.backgroundColor = '';
                }, 300);
            }
        }

        // Update total orders count
        updateTotalOrdersCount();
    }

    // Update order lists in real-time
    function updateOrderLists(data) {
        const orderId = data.order_id;
        const newStatus = data.new_status;
        const previousStatus = data.previous_status;

        // Remove order from previous status list
        const previousList = document.querySelector(`[data-status="${previousStatus}"]`);
        if (previousList) {
            const orderElement = previousList.querySelector(`[data-order-id="${orderId}"]`);
            if (orderElement) {
                orderElement.style.transition = 'all 0.5s ease';
                orderElement.style.opacity = '0';
                orderElement.style.transform = 'translateX(-100%)';
                setTimeout(() => {
                    orderElement.remove();
                }, 500);
            }
        }

        // Add order to new status list
        const newList = document.querySelector(`[data-status="${newStatus}"]`);
        if (newList) {
            // Create new order element
            const orderElement = createOrderElement(data);
            if (orderElement) {
                orderElement.style.opacity = '0';
                orderElement.style.transform = 'translateX(100%)';
                newList.appendChild(orderElement);
                
                // Animate in
                setTimeout(() => {
                    orderElement.style.transition = 'all 0.5s ease';
                    orderElement.style.opacity = '1';
                    orderElement.style.transform = 'translateX(0)';
                }, 100);
            }
        }
    }

    // Create order element for real-time updates
    function createOrderElement(data) {
        const template = `
            <div class="order-card mb-3" data-order-id="${data.order_id}" style="border-left: 4px solid ${getStatusColor(data.new_status)};">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="mb-1">Order #${data.order_number}</h6>
                            <small class="text-muted">${data.customer_name}</small>
                        </div>
                        <span class="badge bg-${getStatusColor(data.new_status)}">${data.status_label}</span>
                    </div>
                    <div class="mt-2">
                        <small class="text-muted">
                            <i class="fas fa-clock me-1"></i>
                            Updated ${formatTime(data.timestamp)}
                        </small>
                    </div>
                    <div class="mt-2">
                        <a href="/orders/${data.order_id}" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-eye me-1"></i>View
                        </a>
                    </div>
                </div>
            </div>
        `;

        const div = document.createElement('div');
        div.innerHTML = template;
        return div.firstElementChild;
    }

    // Update charts if they exist
    function updateCharts(data) {
        // Update status distribution chart
        updateStatusChart(data);
        
        // Update timeline chart
        updateTimelineChart(data);
    }

    // Update status distribution chart
    function updateStatusChart(data) {
        const chartElement = document.getElementById('statusChart');
        if (chartElement && window.statusChart) {
            // Update chart data
            const chart = window.statusChart;
            const statusIndex = chart.data.labels.indexOf(data.status_label);
            
            if (statusIndex !== -1) {
                // Decrease previous status count
                const prevStatusIndex = chart.data.labels.findIndex(label => 
                    label.toLowerCase().includes(data.previous_status.replace('_', ' '))
                );
                if (prevStatusIndex !== -1) {
                    chart.data.datasets[0].data[prevStatusIndex] = Math.max(0, chart.data.datasets[0].data[prevStatusIndex] - 1);
                }
                
                // Increase new status count
                chart.data.datasets[0].data[statusIndex]++;
                
                // Update chart
                chart.update('none');
            }
        }
    }

    // Update timeline chart
    function updateTimelineChart(data) {
        const chartElement = document.getElementById('timelineChart');
        if (chartElement && window.timelineChart) {
            // Add new data point to timeline
            const chart = window.timelineChart;
            const timestamp = new Date(data.timestamp);
            const timeLabel = timestamp.toLocaleTimeString();
            
            // Add new data point
            chart.data.labels.push(timeLabel);
            chart.data.datasets[0].data.push(1);
            
            // Keep only last 20 data points
            if (chart.data.labels.length > 20) {
                chart.data.labels.shift();
                chart.data.datasets[0].data.shift();
            }
            
            // Update chart
            chart.update('none');
        }
    }

    // Update total orders count
    function updateTotalOrdersCount() {
        const totalElement = document.querySelector('.total-orders-count');
        if (totalElement) {
            const statusCounters = document.querySelectorAll('[class*="-count"]');
            let total = 0;
            
            statusCounters.forEach(counter => {
                const count = parseInt(counter.textContent) || 0;
                total += count;
            });
            
            totalElement.textContent = total;
        }
    }

    // Helper functions
    function getStatusColor(status) {
        const colors = {
            'pending_payment': '#ffc107',
            'approved': '#17a2b8',
            'in_production': '#007bff',
            'finishing': '#6c757d',
            'ready_for_delivery': '#28a745',
            'delivered_to_brayne': '#17a2b8',
            'delivered_to_client': '#28a745',
            'cancelled': '#dc3545'
        };
        return colors[status] || '#6c757d';
    }

    function formatTime(timestamp) {
        const date = new Date(timestamp);
        const now = new Date();
        const diff = now - date;
        
        if (diff < 60000) { // Less than 1 minute
            return 'Just now';
        } else if (diff < 3600000) { // Less than 1 hour
            const minutes = Math.floor(diff / 60000);
            return `${minutes} minute${minutes > 1 ? 's' : ''} ago`;
        } else if (diff < 86400000) { // Less than 1 day
            const hours = Math.floor(diff / 3600000);
            return `${hours} hour${hours > 1 ? 's' : ''} ago`;
        } else {
            return date.toLocaleDateString();
        }
    }

    // Auto-refresh dashboard data every 30 seconds
    setInterval(() => {
        refreshDashboardData();
    }, 30000);

    // Refresh dashboard data
    function refreshDashboardData() {
        fetch('/api/dashboard/stats')
            .then(response => response.json())
            .then(data => {
                updateDashboardFromAPI(data);
            })
            .catch(error => {
                console.log('Dashboard refresh failed:', error);
            });
    }

    // Update dashboard from API data
    function updateDashboardFromAPI(data) {
        // Update counters
        Object.keys(data.counters).forEach(status => {
            const counter = document.querySelector(`.${status}-count`);
            if (counter) {
                counter.textContent = data.counters[status];
            }
        });

        // Update charts if they exist
        if (window.statusChart && data.chartData) {
            window.statusChart.data.datasets[0].data = data.chartData;
            window.statusChart.update('none');
        }
    }
});
</script>
@endpush 