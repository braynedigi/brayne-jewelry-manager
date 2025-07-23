@extends('layouts.app')

@section('title', 'Edit User')

@section('page-title', 'Edit User')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header">
                <h5 class="mb-0">Edit User: {{ $user->name }}</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('users.update', $user) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="name" class="form-label">Name *</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                       id="name" name="name" value="{{ old('name', $user->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">Email *</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                       id="email" name="email" value="{{ old('email', $user->email) }}" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="logo" class="form-label">Logo/Image</label>
                                @if($user->logo)
                                    <div class="mb-2">
                                        <img src="{{ asset('storage/' . $user->logo) }}" alt="Current Logo"
                                             class="img-thumbnail" style="max-width: 150px; max-height: 150px;">
                                        <small class="d-block text-muted">Current logo</small>
                                    </div>
                                @endif
                                <input type="file" class="form-control @error('logo') is-invalid @enderror"
                                       id="logo" name="logo" accept="image/*">
                                @error('logo')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">Upload a new logo or profile image (JPEG, PNG, JPG, GIF, max 2MB)</small>
                            </div>

                            <div class="mb-3">
                                <label for="role" class="form-label">Role *</label>
                                <select class="form-select @error('role') is-invalid @enderror" id="role" name="role" required>
                                    <option value="admin" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>Admin</option>
                                    <option value="distributor" {{ old('role', $user->role) === 'distributor' ? 'selected' : '' }}>Distributor</option>
                                    <option value="factory" {{ old('role', $user->role) === 'factory' ? 'selected' : '' }}>Factory</option>
                                </select>
                                @error('role')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                       id="password" name="password">
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">Leave blank to keep current password</small>
                            </div>

                            <div class="mb-3">
                                <label for="password_confirmation" class="form-label">Confirm Password</label>
                                <input type="password" class="form-control" 
                                       id="password_confirmation" name="password_confirmation">
                            </div>

                            <!-- Distributor-specific fields -->
                            <div id="distributor-fields" style="display: {{ $user->role === 'distributor' ? 'block' : 'none' }};">
                                <div class="mb-3">
                                    <label for="company_name" class="form-label">Company Name</label>
                                    <input type="text" class="form-control @error('company_name') is-invalid @enderror" 
                                           id="company_name" name="company_name" 
                                           value="{{ old('company_name', $user->distributor->company_name ?? '') }}">
                                    @error('company_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="phone" class="form-label">Phone</label>
                                    <input type="text" class="form-control @error('phone') is-invalid @enderror" 
                                           id="phone" name="phone" 
                                           value="{{ old('phone', $user->distributor->phone ?? '') }}">
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="street" class="form-label">Street *</label>
                                    <input type="text" class="form-control @error('street') is-invalid @enderror" 
                                           id="street" name="street" 
                                           value="{{ old('street', $user->distributor->street ?? '') }}">
                                    @error('street')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="barangay" class="form-label">Barangay/County *</label>
                                    <input type="text" class="form-control @error('barangay') is-invalid @enderror" 
                                           id="barangay" name="barangay" 
                                           value="{{ old('barangay', $user->distributor->barangay ?? '') }}">
                                    @error('barangay')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="city" class="form-label">City *</label>
                                    <input type="text" class="form-control @error('city') is-invalid @enderror" 
                                           id="city" name="city" 
                                           value="{{ old('city', $user->distributor->city ?? '') }}">
                                    @error('city')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="province" class="form-label">Province/State *</label>
                                    <input type="text" class="form-control @error('province') is-invalid @enderror" 
                                           id="province" name="province" 
                                           value="{{ old('province', $user->distributor->province ?? '') }}">
                                    @error('province')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="country" class="form-label">Country *</label>
                                    <input type="text" class="form-control @error('country') is-invalid @enderror" 
                                           id="country" name="country" 
                                           value="{{ old('country', $user->distributor->country ?? '') }}">
                                    @error('country')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                @if($user->distributor)
                                    <div class="mb-3">
                                        <label class="form-label">International Status</label>
                                        <div class="form-control-plaintext">
                                            @if($user->distributor->is_international)
                                                <span class="badge bg-warning">International</span>
                                                <small class="text-muted d-block">Currency: USD ($)</small>
                                            @else
                                                <span class="badge bg-success">Local</span>
                                                <small class="text-muted d-block">Currency: PHP (₱)</small>
                                            @endif
                                        </div>
                                        <small class="form-text text-muted">This is automatically determined based on the country field</small>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('users.show', $user) }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Back to Details
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Update User
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const roleSelect = document.getElementById('role');
    const distributorFields = document.getElementById('distributor-fields');
    const addressFields = ['street', 'barangay', 'city', 'province', 'country'];

    function toggleDistributorFields() {
        if (roleSelect.value === 'distributor') {
            distributorFields.style.display = 'block';
            // Make address fields required when distributor is selected
            addressFields.forEach(field => {
                const input = document.getElementById(field);
                if (input) {
                    input.required = true;
                }
            });
        } else {
            distributorFields.style.display = 'none';
            // Remove required attribute when distributor is not selected
            addressFields.forEach(field => {
                const input = document.getElementById(field);
                if (input) {
                    input.required = false;
                }
            });
        }
    }

    roleSelect.addEventListener('change', toggleDistributorFields);
    
    // Set initial state based on current role selection
    if (roleSelect.value === 'distributor') {
        toggleDistributorFields();
    }
});
</script>
@endpush
@endsection 