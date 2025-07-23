@extends('layouts.app')

@section('title', 'Customers')

@section('page-title', 'Customers')

@section('content')
<div class="card shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Customer Management</h5>
        <a href="{{ route('customers.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Add Customer
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
                    <input type="text" id="customerSearch" class="form-control" placeholder="Search customers by name, email, phone, address...">
                    <button class="btn btn-outline-secondary" type="button" onclick="clearCustomerSearch()">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            <div class="col-md-6 text-end">
                <span id="customerCount" class="text-muted">
                    Showing {{ $customers->count() }} of {{ $customers->count() }} customers
                </span>
            </div>
        </div>

        @if($customers->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover" id="customersTable">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Address</th>
                            @if(auth()->user()->isAdmin())
                                <th>Assigned Distributor</th>
                            @endif
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($customers as $customer)
                            <tr class="customer-row" 
                                data-name="{{ strtolower($customer->name) }}"
                                data-email="{{ strtolower($customer->email ?? '') }}"
                                data-phone="{{ strtolower($customer->phone ?? '') }}"
                                data-address="{{ strtolower($customer->full_address ?? '') }}">
                                <td>
                                    <div class="fw-bold">{{ $customer->name }}</div>
                                </td>
                                <td>
                                    @if($customer->email)
                                        <a href="mailto:{{ $customer->email }}">{{ $customer->email }}</a>
                                    @else
                                        <span class="text-muted">Not provided</span>
                                    @endif
                                </td>
                                <td>
                                    @if($customer->phone)
                                        <a href="tel:{{ $customer->phone }}">{{ $customer->phone }}</a>
                                    @else
                                        <span class="text-muted">Not provided</span>
                                    @endif
                                </td>
                                <td>
                                    @if($customer->hasAddress())
                                        <span class="text-truncate d-inline-block" style="max-width: 200px;" 
                                              title="{{ $customer->full_address }}">
                                            {{ $customer->full_address }}
                                        </span>
                                    @else
                                        <span class="text-muted">No address</span>
                                    @endif
                                </td>
                                @if(auth()->user()->isAdmin())
                                    <td>
                                        @if($customer->distributor)
                                            <div class="d-flex align-items-center">
                                                @if($customer->distributor->user->logo)
                                                    <img src="{{ asset('storage/' . $customer->distributor->user->logo) }}" 
                                                         alt="Logo" class="rounded-circle me-2" 
                                                         style="width: 30px; height: 30px; object-fit: cover;">
                                                @endif
                                                <div>
                                                    <div class="fw-bold">{{ $customer->distributor->user->name }}</div>
                                                    <small class="text-muted">{{ $customer->distributor->company_name }}</small>
                                                    @if($customer->distributor->is_international)
                                                        <span class="badge bg-warning ms-1">International</span>
                                                    @else
                                                        <span class="badge bg-success ms-1">Local</span>
                                                    @endif
                                                </div>
                                            </div>
                                        @else
                                            <span class="text-muted">No distributor assigned</span>
                                        @endif
                                    </td>
                                @endif
                                <td>
                                    <a href="{{ route('customers.show', $customer) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('customers.edit', $customer) }}" class="btn btn-sm btn-outline-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('customers.destroy', $customer) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this customer?')">
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
                <i class="fas fa-user-friends fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No customers found</h5>
                <p class="text-muted">Start by adding your first customer.</p>
                <a href="{{ route('customers.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Add First Customer
                </a>
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('customerSearch');
    const customerRows = document.querySelectorAll('.customer-row');
    const customerCount = document.getElementById('customerCount');
    const totalCustomers = customerRows.length;

    function filterCustomers() {
        const searchTerm = searchInput.value.toLowerCase().trim();
        let visibleCount = 0;

        customerRows.forEach(row => {
            const name = row.getAttribute('data-name');
            const email = row.getAttribute('data-email');
            const phone = row.getAttribute('data-phone');
            const address = row.getAttribute('data-address');

            const matches = name.includes(searchTerm) || 
                           email.includes(searchTerm) || 
                           phone.includes(searchTerm) || 
                           address.includes(searchTerm);

            if (matches) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });

        // Update count display
        if (searchTerm === '') {
            customerCount.textContent = `Showing ${totalCustomers} of ${totalCustomers} customers`;
        } else {
            customerCount.textContent = `Showing ${visibleCount} of ${totalCustomers} customers`;
        }
    }

    // Add event listener for search input
    searchInput.addEventListener('input', filterCustomers);

    // Clear search function
    window.clearCustomerSearch = function() {
        searchInput.value = '';
        filterCustomers();
        searchInput.focus();
    };
});
</script>
@endpush
@endsection
