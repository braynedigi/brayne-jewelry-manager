@extends('layouts.app')

@section('title', 'Couriers')

@section('page-title', 'Couriers')

@section('content')
<div class="card shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Courier Management</h5>
        <a href="{{ route('couriers.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Add Courier
        </a>
    </div>
    <div class="card-body">
        <!-- Search Bar -->
        <div class="row mb-3">
            <div class="col-md-6">
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="fas fa-search"></i>
                    </span>
                    <input type="text" id="courierSearch" class="form-control" placeholder="Search couriers by name, email, phone...">
                    <button class="btn btn-outline-secondary" type="button" onclick="clearCourierSearch()">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            <div class="col-md-6 text-end">
                <span id="courierCount" class="text-muted">
                    Showing {{ $couriers->count() }} of {{ $couriers->count() }} couriers
                </span>
            </div>
        </div>

        @if($couriers->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover" id="couriersTable">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Phone</th>
                            <th>Email</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($couriers as $courier)
                            <tr class="courier-row" 
                                data-name="{{ strtolower($courier->name) }}"
                                data-email="{{ strtolower($courier->email ?? '') }}"
                                data-phone="{{ strtolower($courier->phone ?? '') }}">
                                <td>{{ $courier->name }}</td>
                                <td>{{ $courier->phone }}</td>
                                <td>{{ $courier->email ?? 'N/A' }}</td>
                                <td>
                                    <span class="badge bg-{{ $courier->is_active ? 'success' : 'danger' }}">
                                        {{ $courier->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('couriers.show', $courier) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('couriers.edit', $courier) }}" class="btn btn-sm btn-outline-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('couriers.destroy', $courier) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this courier?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-4">
                <i class="fas fa-truck fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No couriers found</h5>
                <p class="text-muted">Start by adding your first courier service.</p>
                <a href="{{ route('couriers.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Add First Courier
                </a>
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('courierSearch');
    const courierRows = document.querySelectorAll('.courier-row');
    const courierCount = document.getElementById('courierCount');
    const totalCouriers = courierRows.length;

    function filterCouriers() {
        const searchTerm = searchInput.value.toLowerCase().trim();
        let visibleCount = 0;

        courierRows.forEach(row => {
            const name = row.getAttribute('data-name');
            const email = row.getAttribute('data-email');
            const phone = row.getAttribute('data-phone');

            const matches = name.includes(searchTerm) || 
                           email.includes(searchTerm) || 
                           phone.includes(searchTerm);

            if (matches) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });

        // Update count display
        if (searchTerm === '') {
            courierCount.textContent = `Showing ${totalCouriers} of ${totalCouriers} couriers`;
        } else {
            courierCount.textContent = `Showing ${visibleCount} of ${totalCouriers} couriers`;
        }
    }

    // Add event listener for search input
    searchInput.addEventListener('input', filterCouriers);

    // Clear search function
    window.clearCourierSearch = function() {
        searchInput.value = '';
        filterCouriers();
        searchInput.focus();
    };
});
</script>
@endpush
@endsection 