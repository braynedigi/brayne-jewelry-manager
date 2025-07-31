@extends('layouts.app')

@section('title', 'Factory Dashboard')

@section('page-title', 'Factory Dashboard')
@section('page-subtitle', 'Production Workflow Management')

@section('content')
<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stats-card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="stats-label">Total Orders</div>
                    <div class="stats-number">{{ $queueOrders->count() + $inProductionOrders->count() + $finishingOrders->count() + $readyOrders->count() + $deliveredOrders->count() }}</div>
                </div>
                <div class="stats-icon">
                    <i class="fas fa-clipboard-list"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stats-card" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="stats-label">Urgent Orders</div>
                    <div class="stats-number">{{ $urgentOrders }}</div>
                </div>
                <div class="stats-icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stats-card" style="background: linear-gradient(135deg, #ffecd2 0%, #fcb69f 100%);">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="stats-label">Due Today</div>
                    <div class="stats-number">{{ $dueToday }}</div>
                </div>
                <div class="stats-icon">
                    <i class="fas fa-calendar-day"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stats-card" style="background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%);">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="stats-label">Total Hours</div>
                    <div class="stats-number">{{ $totalEstimatedHours }}</div>
                </div>
                <div class="stats-icon">
                    <i class="fas fa-clock"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Priority Breakdown -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-exclamation-triangle text-danger me-2"></i>Urgent ({{ $priorityStats['urgent'] }})
                </h5>
            </div>
            <div class="card-body">
                <div class="progress mb-3" style="height: 8px;">
                    <div class="progress-bar bg-danger" style="width: {{ $priorityStats['urgent'] > 0 ? ($priorityStats['urgent'] / ($priorityStats['urgent'] + $priorityStats['normal'] + $priorityStats['low'])) * 100 : 0 }}%"></div>
                </div>
                <small class="text-muted">High priority orders requiring immediate attention</small>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-minus text-info me-2"></i>Normal ({{ $priorityStats['normal'] }})
                </h5>
            </div>
            <div class="card-body">
                <div class="progress mb-3" style="height: 8px;">
                    <div class="progress-bar bg-info" style="width: {{ $priorityStats['normal'] > 0 ? ($priorityStats['normal'] / ($priorityStats['urgent'] + $priorityStats['normal'] + $priorityStats['low'])) * 100 : 0 }}%"></div>
                </div>
                <small class="text-muted">Standard priority orders</small>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-arrow-down text-secondary me-2"></i>Low ({{ $priorityStats['low'] }})
                </h5>
            </div>
            <div class="card-body">
                <div class="progress mb-3" style="height: 8px;">
                    <div class="progress-bar bg-secondary" style="width: {{ $priorityStats['low'] > 0 ? ($priorityStats['low'] / ($priorityStats['urgent'] + $priorityStats['normal'] + $priorityStats['low'])) * 100 : 0 }}%"></div>
                </div>
                <small class="text-muted">Low priority orders</small>
            </div>
        </div>
    </div>
</div>

<!-- Production Workflow -->
<div class="row">
    <!-- Queue (Approved Orders) -->
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="card">
            <div class="card-header bg-warning text-white">
                <h5 class="mb-0">
                    <i class="fas fa-clock me-2"></i>Queue
                    <span class="badge bg-light text-dark ms-2 approved-count">{{ $queueOrders->count() }}</span>
                </h5>
            </div>
            <div class="card-body p-0">
                <div class="production-column queue-column" style="max-height: 500px; overflow-y: auto;">
                    @forelse($queueOrders as $order)
                        @include('factory.partials.order-card', ['order' => $order])
                    @empty
                        <div class="text-center py-4">
                            <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                            <p class="text-muted mb-0">No orders in queue</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- In Production -->
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <i class="fas fa-cogs me-2"></i>In Production
                    <span class="badge bg-light text-dark ms-2 in-production-count">{{ $inProductionOrders->count() }}</span>
                </h5>
            </div>
            <div class="card-body p-0">
                <div class="production-column in-production-column" style="max-height: 500px; overflow-y: auto;">
                    @forelse($inProductionOrders as $order)
                        @include('factory.partials.order-card', ['order' => $order])
                    @empty
                        <div class="text-center py-4">
                            <i class="fas fa-industry fa-2x text-muted mb-2"></i>
                            <p class="text-muted mb-0">No orders in production</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Finishing -->
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="card">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0">
                    <i class="fas fa-paint-brush me-2"></i>Finishing
                    <span class="badge bg-light text-dark ms-2 finishing-count">{{ $finishingOrders->count() }}</span>
                </h5>
            </div>
            <div class="card-body p-0">
                <div class="production-column finishing-column" style="max-height: 500px; overflow-y: auto;">
                    @forelse($finishingOrders as $order)
                        @include('factory.partials.order-card', ['order' => $order])
                    @empty
                        <div class="text-center py-4">
                            <i class="fas fa-sparkles fa-2x text-muted mb-2"></i>
                            <p class="text-muted mb-0">No orders in finishing</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Ready for Delivery -->
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="card">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0">
                    <i class="fas fa-check-circle me-2"></i>Ready
                    <span class="badge bg-light text-dark ms-2 ready-count">{{ $readyOrders->count() }}</span>
                </h5>
            </div>
            <div class="card-body p-0">
                <div class="production-column ready-column" style="max-height: 500px; overflow-y: auto;">
                    @forelse($readyOrders as $order)
                        @include('factory.partials.order-card', ['order' => $order])
                    @empty
                        <div class="text-center py-4">
                            <i class="fas fa-box fa-2x text-muted mb-2"></i>
                            <p class="text-muted mb-0">No orders ready</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Delivered to Brayne -->
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="card">
            <div class="card-header bg-secondary text-white">
                <h5 class="mb-0">
                    <i class="fas fa-truck me-2"></i>Delivered to Brayne
                    <span class="badge bg-light text-dark ms-2 delivered-brayne-count">{{ $deliveredOrders->count() }}</span>
                </h5>
            </div>
            <div class="card-body p-0">
                <div class="production-column delivered-column" style="max-height: 500px; overflow-y: auto;">
                    @forelse($deliveredOrders as $order)
                        @include('factory.partials.order-card', ['order' => $order])
                    @empty
                        <div class="text-center py-4">
                            <i class="fas fa-truck fa-2x text-muted mb-2"></i>
                            <p class="text-muted mb-0">No orders delivered</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-bolt me-2"></i>Quick Actions
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <a href="{{ route('factory.queue') }}" class="btn btn-primary w-100 p-3">
                            <i class="fas fa-list fa-2x mb-2"></i>
                            <div>Production Queue</div>
                            <small class="opacity-75">View all orders with filters</small>
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="{{ route('factory.workload') }}" class="btn btn-info w-100 p-3">
                            <i class="fas fa-chart-line fa-2x mb-2"></i>
                            <div>Workload Analysis</div>
                            <small class="opacity-75">Capacity and timeline planning</small>
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="{{ route('orders.index') }}" class="btn btn-success w-100 p-3">
                            <i class="fas fa-eye fa-2x mb-2"></i>
                            <div>All Orders</div>
                            <small class="opacity-75">View complete order list</small>
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <button class="btn btn-warning w-100 p-3" onclick="refreshDashboard()">
                            <i class="fas fa-sync-alt fa-2x mb-2"></i>
                            <div>Refresh</div>
                            <small class="opacity-75">Update dashboard data</small>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Weekly Workload Preview -->
@if(!empty($weeklyWorkload))
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-calendar-week me-2"></i>This Week's Workload
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    @foreach($weeklyWorkload as $day => $data)
                    <div class="col-md-1 col-4 mb-3">
                        <div class="text-center">
                            <div class="fw-bold">{{ $data['date'] }}</div>
                            <div class="small text-muted">{{ $day }}</div>
                            <div class="badge bg-{{ $data['orders_count'] > 5 ? 'danger' : ($data['orders_count'] > 2 ? 'warning' : 'success') }}">
                                {{ $data['orders_count'] }} orders
                            </div>
                            @if($data['urgent_count'] > 0)
                                <div class="small text-danger mt-1">
                                    <i class="fas fa-exclamation-triangle"></i> {{ $data['urgent_count'] }} urgent
                                </div>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Priority Update Modal -->
<div class="modal fade" id="priorityModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Order Priority</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="priorityForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="priority" class="form-label">Priority Level</label>
                        <select class="form-select" name="priority" required>
                            <option value="low">Low</option>
                            <option value="normal">Normal</option>
                            <option value="urgent">Urgent</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Priority</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
.production-column {
    min-height: 200px;
}

.order-card {
    border: 1px solid #e9ecef;
    border-radius: 8px;
    margin: 8px;
    background: white;
    transition: all 0.3s ease;
}

.order-card:hover {
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    transform: translateY(-2px);
}

.order-card.urgent {
    border-left: 4px solid #dc3545;
}

.order-card.overdue {
    border-left: 4px solid #fd7e14;
}

.progress {
    border-radius: 10px;
}

.stats-card {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 15px;
    padding: 20px;
    color: white;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.stats-label {
    font-size: 14px;
    opacity: 0.9;
    margin-bottom: 5px;
}

.stats-number {
    font-size: 28px;
    font-weight: bold;
    margin-bottom: 5px;
}

.stats-icon {
    font-size: 24px;
    opacity: 0.8;
}
</style>
@endpush

@push('scripts')
<script>
function refreshDashboard() {
    location.reload();
}

// Auto-refresh every 5 minutes
setTimeout(function() {
    refreshDashboard();
}, 300000);
</script>
@endpush 