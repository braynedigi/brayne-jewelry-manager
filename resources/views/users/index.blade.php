@extends('layouts.app')

@section('title', 'Users')

@section('page-title', 'Users')

@section('content')
<div class="card shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">User Management</h5>
        <a href="{{ route('users.create') }}" class="btn btn-primary">
            <i class="fas fa-user-plus me-2"></i>Add User
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
                    <input type="text" id="userSearch" class="form-control" placeholder="Search users by name, email, role, company...">
                    <button class="btn btn-outline-secondary" type="button" onclick="clearUserSearch()">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            <div class="col-md-6 text-end">
                <span id="userCount" class="text-muted">
                    Showing {{ $users->count() }} of {{ $users->count() }} users
                </span>
            </div>
        </div>

        @if($users->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover" id="usersTable">
                    <thead>
                        <tr>
                            <th>Logo</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Company</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                            <tr class="user-row" 
                                data-name="{{ strtolower($user->name) }}"
                                data-email="{{ strtolower($user->email) }}"
                                data-role="{{ strtolower($user->role) }}"
                                data-company="{{ $user->role === 'distributor' && $user->distributor ? strtolower($user->distributor->company_name) : '' }}">
                                <td>
                                    @if($user->hasLogo())
                                        <div class="image-container" style="width: 40px; height: 40px;">
                                            <img src="{{ $user->getLogoUrl() }}" 
                                                 alt="{{ $user->name }}" 
                                                 class="user-logo" 
                                                 style="width: 100%; height: 100%; object-fit: cover;">
                                            <div class="image-overlay">
                                                <i class="fas fa-user"></i>
                                            </div>
                                        </div>
                                    @else
                                        <div class="image-placeholder" 
                                             style="width: 40px; height: 40px;">
                                            <i class="fas fa-user"></i>
                                        </div>
                                    @endif
                                </td>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    <span class="badge bg-{{ $user->role === 'admin' ? 'danger' : ($user->role === 'distributor' ? 'primary' : 'success') }}">
                                        {{ ucfirst($user->role) }}
                                    </span>
                                </td>
                                <td>
                                    @if($user->role === 'distributor' && $user->distributor)
                                        <br>
                                        <small class="text-muted">
                                            {{ $user->distributor->company_name }}
                                            @if($user->distributor->is_international)
                                                <span class="badge bg-warning ms-1">International</span>
                                            @else
                                                <span class="badge bg-success ms-1">Local</span>
                                            @endif
                                        </small>
                                    @endif
                                </td>
                                <td>{{ $user->created_at->format('M d, Y') }}</td>
                                <td>
                                    <a href="{{ route('users.show', $user) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('users.edit', $user) }}" class="btn btn-sm btn-outline-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @if($user->id !== auth()->id())
                                        <form action="{{ route('users.destroy', $user) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this user?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-4">
                <i class="fas fa-users fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No users found</h5>
                <p class="text-muted">Start by adding your first user to the system.</p>
                <a href="{{ route('users.create') }}" class="btn btn-primary">
                    <i class="fas fa-user-plus me-2"></i>Add First User
                </a>
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('userSearch');
    const userRows = document.querySelectorAll('.user-row');
    const userCount = document.getElementById('userCount');
    const totalUsers = userRows.length;

    function filterUsers() {
        const searchTerm = searchInput.value.toLowerCase().trim();
        let visibleCount = 0;

        userRows.forEach(row => {
            const name = row.getAttribute('data-name');
            const email = row.getAttribute('data-email');
            const role = row.getAttribute('data-role');
            const company = row.getAttribute('data-company');

            const matches = name.includes(searchTerm) || 
                           email.includes(searchTerm) || 
                           role.includes(searchTerm) || 
                           company.includes(searchTerm);

            if (matches) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });

        // Update count display
        if (searchTerm === '') {
            userCount.textContent = `Showing ${totalUsers} of ${totalUsers} users`;
        } else {
            userCount.textContent = `Showing ${visibleCount} of ${totalUsers} users`;
        }
    }

    // Add event listener for search input
    searchInput.addEventListener('input', filterUsers);

    // Clear search function
    window.clearUserSearch = function() {
        searchInput.value = '';
        filterUsers();
        searchInput.focus();
    };
});
</script>
@endpush
@endsection 