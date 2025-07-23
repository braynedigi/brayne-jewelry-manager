@extends('layouts.app')

@section('title', 'Workload Analysis')

@section('page-title', 'Workload Analysis')
@section('page-subtitle', 'Production Capacity & Timeline Planning')

@section('content')
<div class="row">
    <!-- Capacity Overview -->
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-chart-line me-2"></i>Production Capacity Overview
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <div class="text-center">
                            <div class="h4 text-primary">{{ $capacityData['total_hours'] ?? 0 }}</div>
                            <small class="text-muted">Total Hours</small>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="text-center">
                            <div class="h4 text-success">{{ $capacityData['completed_hours'] ?? 0 }}</div>
                            <small class="text-muted">Completed Hours</small>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="text-center">
                            <div class="h4 text-warning">{{ $capacityData['remaining_hours'] ?? 0 }}</div>
                            <small class="text-muted">Remaining Hours</small>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="text-center">
                            <div class="h4 text-info">{{ $capacityData['utilization_rate'] ?? 0 }}%</div>
                            <small class="text-muted">Utilization Rate</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Weekly Timeline -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-calendar-week me-2"></i>Weekly Production Timeline
                </h5>
            </div>
            <div class="card-body">
                @if(!empty($weeklyWorkload))
                    <div class="row">
                        @foreach($weeklyWorkload as $day => $data)
                        <div class="col-md-1 col-4 mb-3">
                            <div class="text-center">
                                <div class="fw-bold">{{ $data['date'] }}</div>
                                <div class="small text-muted">{{ $day }}</div>
                                <div class="badge bg-{{ $data['orders_count'] > 5 ? 'danger' : ($data['orders_count'] > 2 ? 'warning' : 'success') }}">
                                    {{ $data['orders_count'] }} orders
                                </div>
                                <div class="small text-muted mt-1">
                                    {{ $data['total_hours'] ?? 0 }}h
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
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-calendar fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No workload data available</h5>
                        <p class="text-muted">Workload data will appear here once orders are in production.</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Monthly Overview -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-calendar-alt me-2"></i>Monthly Production Overview
                </h5>
            </div>
            <div class="card-body">
                @if(!empty($monthlyWorkload))
                    <div class="row">
                        @foreach($monthlyWorkload as $month => $data)
                        <div class="col-md-2 col-4 mb-3">
                            <div class="text-center">
                                <div class="fw-bold">{{ $month }}</div>
                                <div class="badge bg-primary">{{ $data['orders_count'] }} orders</div>
                                <div class="small text-muted mt-1">
                                    {{ $data['total_hours'] ?? 0 }}h
                                </div>
                                @if($data['urgent_count'] > 0)
                                    <div class="small text-danger">
                                        {{ $data['urgent_count'] }} urgent
                                    </div>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-chart-bar fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No monthly data available</h5>
                        <p class="text-muted">Monthly production data will appear here.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="col-lg-4">
        <!-- Priority Distribution -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-flag me-2"></i>Priority Distribution
                </h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span>Urgent</span>
                        <span class="badge bg-danger">{{ $priorityStats['urgent'] ?? 0 }}</span>
                    </div>
                    <div class="progress mb-2" style="height: 8px;">
                        <div class="progress-bar bg-danger" style="width: {{ $priorityStats['urgent'] > 0 ? ($priorityStats['urgent'] / ($priorityStats['urgent'] + $priorityStats['normal'] + $priorityStats['low'])) * 100 : 0 }}%"></div>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span>Normal</span>
                        <span class="badge bg-info">{{ $priorityStats['normal'] ?? 0 }}</span>
                    </div>
                    <div class="progress mb-2" style="height: 8px;">
                        <div class="progress-bar bg-info" style="width: {{ $priorityStats['normal'] > 0 ? ($priorityStats['normal'] / ($priorityStats['urgent'] + $priorityStats['normal'] + $priorityStats['low'])) * 100 : 0 }}%"></div>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span>Low</span>
                        <span class="badge bg-secondary">{{ $priorityStats['low'] ?? 0 }}</span>
                    </div>
                    <div class="progress mb-2" style="height: 8px;">
                        <div class="progress-bar bg-secondary" style="width: {{ $priorityStats['low'] > 0 ? ($priorityStats['low'] / ($priorityStats['urgent'] + $priorityStats['normal'] + $priorityStats['low'])) * 100 : 0 }}%"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Status Distribution -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-tasks me-2"></i>Status Distribution
                </h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span>Queue</span>
                        <span class="badge bg-warning">{{ $statusStats['approved'] ?? 0 }}</span>
                    </div>
                    <div class="progress mb-2" style="height: 8px;">
                        <div class="progress-bar bg-warning" style="width: {{ $statusStats['approved'] > 0 ? ($statusStats['approved'] / array_sum($statusStats)) * 100 : 0 }}%"></div>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span>In Production</span>
                        <span class="badge bg-primary">{{ $statusStats['in_production'] ?? 0 }}</span>
                    </div>
                    <div class="progress mb-2" style="height: 8px;">
                        <div class="progress-bar bg-primary" style="width: {{ $statusStats['in_production'] > 0 ? ($statusStats['in_production'] / array_sum($statusStats)) * 100 : 0 }}%"></div>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span>Finishing</span>
                        <span class="badge bg-info">{{ $statusStats['finishing'] ?? 0 }}</span>
                    </div>
                    <div class="progress mb-2" style="height: 8px;">
                        <div class="progress-bar bg-info" style="width: {{ $statusStats['finishing'] > 0 ? ($statusStats['finishing'] / array_sum($statusStats)) * 100 : 0 }}%"></div>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span>Ready</span>
                        <span class="badge bg-success">{{ $statusStats['ready_for_delivery'] ?? 0 }}</span>
                    </div>
                    <div class="progress mb-2" style="height: 8px;">
                        <div class="progress-bar bg-success" style="width: {{ $statusStats['ready_for_delivery'] > 0 ? ($statusStats['ready_for_delivery'] / array_sum($statusStats)) * 100 : 0 }}%"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-bolt me-2"></i>Quick Actions
                </h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('factory.dashboard') }}" class="btn btn-primary">
                        <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                    </a>
                    <a href="{{ route('factory.queue') }}" class="btn btn-info">
                        <i class="fas fa-list me-2"></i>Production Queue
                    </a>
                    <a href="{{ route('orders.index') }}" class="btn btn-success">
                        <i class="fas fa-eye me-2"></i>All Orders
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.progress {
    border-radius: 10px;
}

.card {
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.card-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 10px 10px 0 0 !important;
}
</style>
@endpush 