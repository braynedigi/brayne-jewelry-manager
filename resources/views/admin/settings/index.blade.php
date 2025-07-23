@extends('layouts.app')

@php
use App\Models\Setting;
@endphp

@section('title', 'Admin Settings')

@section('page-title', 'Application Settings')
@section('page-subtitle', 'Configure your jewelry management system')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <!-- Simple tab navigation -->
                <div class="tab-navigation mb-3">
                    <button type="button" class="tab-button active" onclick="switchTab('appearance')">
                        <i class="fas fa-palette me-2"></i>Appearance
                    </button>
                    <button type="button" class="tab-button" onclick="switchTab('notifications')">
                        <i class="fas fa-bell me-2"></i>Notifications
                    </button>
                    <button type="button" class="tab-button" onclick="switchTab('general')">
                        <i class="fas fa-cog me-2"></i>General
                    </button>
                    <button type="button" class="tab-button" onclick="switchTab('couriers')">
                        <i class="fas fa-truck me-2"></i>Couriers
                    </button>
                </div>
                
                <style>
                    .tab-navigation {
                        display: flex;
                        gap: 10px;
                        border-bottom: 2px solid #e9ecef;
                        padding-bottom: 10px;
                    }
                    .tab-button {
                        background: none;
                        border: none;
                        padding: 10px 20px;
                        border-radius: 5px 5px 0 0;
                        cursor: pointer;
                        font-weight: 500;
                        color: #6c757d;
                        transition: all 0.3s ease;
                    }
                    .tab-button:hover {
                        background-color: #f8f9fa;
                        color: #495057;
                    }
                    .tab-button.active {
                        background-color: #D4AF37;
                        color: white;
                        border-bottom: 2px solid #D4AF37;
                        margin-bottom: -2px;
                    }
                    .tab-section {
                        display: none;
                    }
                    .tab-section.active {
                        display: block;
                    }
                </style>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <div class="tab-section active" id="appearance">
                        <h5 class="mb-4">
                            <i class="fas fa-palette me-2"></i>Login Page Appearance
                        </h5>
                        
                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label for="login_logo" class="form-label">Login Page Logo</label>
                                <input type="file" class="form-control" id="login_logo" name="settings[login_logo]" accept="image/*">
                                <div class="form-text">Upload a logo to display on the login page (recommended: 200x80px)</div>
                                
                                @if(Setting::getValue('login_logo'))
                                    <div class="mt-2">
                                        <img src="{{ asset('storage/' . Setting::getValue('login_logo')) }}" 
                                             alt="Current Logo" class="img-thumbnail" style="max-height: 80px;">
                                    </div>
                                @endif
                            </div>
                            
                            <div class="col-md-6 mb-4">
                                <label for="login_background_color" class="form-label">Background Color</label>
                                <input type="color" class="form-control form-control-color" 
                                       id="login_background_color" name="settings[login_background_color]"
                                       value="{{ Setting::getValue('login_background_color', '#f8fafc') }}">
                                <div class="form-text">Choose the background color for the login page</div>
                            </div>
                            
                            <div class="col-md-6 mb-4">
                                <label for="login_background_image" class="form-label">Background Image</label>
                                <input type="file" class="form-control" id="login_background_image" 
                                       name="settings[login_background_image]" accept="image/*">
                                <div class="form-text">Upload a background image for the login page</div>
                                
                                @if(Setting::getValue('login_background_image'))
                                    <div class="mt-2">
                                        <img src="{{ asset('storage/' . Setting::getValue('login_background_image')) }}" 
                                             alt="Current Background" class="img-thumbnail" style="max-height: 100px;">
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="tab-section" id="notifications">
                        <h5 class="mb-4">
                            <i class="fas fa-bell me-2"></i>Notification Preferences
                        </h5>
                        
                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="email_notifications" 
                                           name="settings[email_notifications]" value="1"
                                           {{ Setting::getValue('email_notifications', true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="email_notifications">
                                        <strong>Email Notifications</strong>
                                    </label>
                                    <div class="form-text">Send notifications via email</div>
                                </div>
                            </div>
                            
                            <div class="col-md-6 mb-4">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="in_app_notifications" 
                                           name="settings[in_app_notifications]" value="1"
                                           {{ Setting::getValue('in_app_notifications', true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="in_app_notifications">
                                        <strong>In-App Notifications</strong>
                                    </label>
                                    <div class="form-text">Show notifications within the application</div>
                                </div>
                            </div>
                            
                            <div class="col-md-6 mb-4">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="order_notifications" 
                                           name="settings[order_notifications]" value="1"
                                           {{ Setting::getValue('order_notifications', true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="order_notifications">
                                        <strong>Order Notifications</strong>
                                    </label>
                                    <div class="form-text">Send notifications for order-related actions</div>
                                </div>
                            </div>
                            
                            <div class="col-md-6 mb-4">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="customer_notifications" 
                                           name="settings[customer_notifications]" value="1"
                                           {{ Setting::getValue('customer_notifications', true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="customer_notifications">
                                        <strong>Customer Notifications</strong>
                                    </label>
                                    <div class="form-text">Send notifications for customer-related actions</div>
                                </div>
                            </div>
                            
                            <div class="col-md-6 mb-4">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="product_notifications" 
                                           name="settings[product_notifications]" value="1"
                                           {{ Setting::getValue('product_notifications', true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="product_notifications">
                                        <strong>Product Notifications</strong>
                                    </label>
                                    <div class="form-text">Send notifications for product-related actions</div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Note:</strong> These settings control notifications for all users. Individual user preferences can be managed separately.
                        </div>
                    </div>

                    <div class="tab-section" id="general">
                        <h5 class="mb-4">
                            <i class="fas fa-cog me-2"></i>General Settings
                        </h5>
                        

                        
                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label for="company_name" class="form-label">Company Name</label>
                                <input type="text" class="form-control" id="company_name" 
                                       name="settings[company_name]" 
                                       value="{{ Setting::getValue('company_name', 'Jewelry Manager') }}">
                                <div class="form-text">The name of your company</div>
                            </div>
                            
                            <div class="col-md-6 mb-4">
                                <label for="app_title" class="form-label">Application Title</label>
                                <input type="text" class="form-control" id="app_title" 
                                       name="settings[app_title]" 
                                       value="{{ Setting::getValue('app_title', 'Jewelry Manager') }}">
                                <div class="form-text">The title displayed in browser tabs and throughout the application</div>
                            </div>
                            
                            <div class="col-md-6 mb-4">
                                <label for="company_email" class="form-label">Company Email</label>
                                <input type="email" class="form-control" id="company_email" 
                                       name="settings[company_email]" 
                                       value="{{ Setting::getValue('company_email', 'admin@jewelrymanager.com') }}">
                                <div class="form-text">Primary email address for the company</div>
                            </div>
                        </div>
                    </div>

                    <div class="tab-section" id="couriers">
                        <h5 class="mb-4">
                            <i class="fas fa-truck me-2"></i>Courier Management
                        </h5>
                        
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div>
                                <h6 class="mb-1">Manage Delivery Couriers</h6>
                                <p class="text-muted mb-0">Add, edit, and manage courier services available for order delivery</p>
                            </div>
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCourierModal">
                                <i class="fas fa-plus me-2"></i>Add New Courier
                            </button>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Courier Name</th>
                                        <th>Phone</th>
                                        <th>Email</th>
                                        <th>Status</th>
                                        <th>Orders</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse(\App\Models\Courier::all() as $courier)
                                    <tr>
                                        <td>
                                            <div class="fw-bold">{{ $courier->name }}</div>
                                        </td>
                                        <td>{{ $courier->phone }}</td>
                                        <td>{{ $courier->email ?: 'N/A' }}</td>
                                        <td>
                                            <span class="badge bg-{{ $courier->is_active ? 'success' : 'secondary' }}">
                                                {{ $courier->is_active ? 'Active' : 'Inactive' }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-info">{{ $courier->orders->count() }}</span>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <button type="button" class="btn btn-sm btn-outline-primary" 
                                                        onclick="editCourier({{ $courier->id }}, '{{ $courier->name }}', '{{ $courier->phone }}', '{{ $courier->email }}', {{ $courier->is_active ? 'true' : 'false' }})">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-danger" 
                                                        onclick="deleteCourier({{ $courier->id }}, '{{ $courier->name }}')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4">
                                            <i class="fas fa-truck fa-2x text-muted mb-2"></i>
                                            <p class="text-muted mb-0">No couriers found. Add your first courier to get started.</p>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between mt-4">
                        <a href="{{ route('admin.settings.refresh') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-sync-alt me-2"></i>Refresh Cache
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Save Settings
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('Settings page loaded');
    
    // Function to show a specific tab
    window.switchTab = function(tabId) {
        console.log('Switching to tab:', tabId);
        
        // Hide all tab sections
        document.querySelectorAll('.tab-section').forEach(function(section) {
            section.classList.remove('active');
            section.style.display = 'none';
        });
        
        // Remove active class from all tab buttons
        document.querySelectorAll('.tab-button').forEach(function(button) {
            button.classList.remove('active');
        });
        
        // Show target tab section
        var targetSection = document.getElementById(tabId);
        if (targetSection) {
            targetSection.classList.add('active');
            targetSection.style.display = 'block';
            console.log('Tab section shown:', tabId);
        } else {
            console.error('Tab section not found:', tabId);
        }
        
        // Add active class to clicked tab button
        var activeButton = document.querySelector(`.tab-button[onclick="switchTab('${tabId}')"]`);
        if (activeButton) {
            activeButton.classList.add('active');
            console.log('Tab button activated:', tabId);
        } else {
            console.error('Tab button not found for:', tabId);
        }
    }
    
    // Show the default active tab on page load
    switchTab('appearance');
    
    // Log all available tabs for debugging
    console.log('Available tabs: appearance, notifications, general, couriers');
    });
</script>

<!-- Add Courier Modal -->
<div class="modal fade" id="addCourierModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-plus me-2"></i>Add New Courier
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="addCourierForm" method="POST" action="{{ route('admin.couriers.store') }}">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="courier_name" class="form-label">Courier Name *</label>
                        <input type="text" class="form-control" id="courier_name" name="name" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="courier_phone" class="form-label">Phone Number *</label>
                        <input type="text" class="form-control" id="courier_phone" name="phone" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="courier_email" class="form-label">Email Address</label>
                        <input type="email" class="form-control" id="courier_email" name="email">
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="courier_is_active" name="is_active" value="1" checked>
                            <label class="form-check-label" for="courier_is_active">
                                Active Courier
                            </label>
                        </div>
                        <small class="form-text text-muted">Inactive couriers won't appear in order forms</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Add Courier
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Courier Modal -->
<div class="modal fade" id="editCourierModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-edit me-2"></i>Edit Courier
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editCourierForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_courier_name" class="form-label">Courier Name *</label>
                        <input type="text" class="form-control" id="edit_courier_name" name="name" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_courier_phone" class="form-label">Phone Number *</label>
                        <input type="text" class="form-control" id="edit_courier_phone" name="phone" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_courier_email" class="form-label">Email Address</label>
                        <input type="email" class="form-control" id="edit_courier_email" name="email">
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="edit_courier_is_active" name="is_active" value="1">
                            <label class="form-check-label" for="edit_courier_is_active">
                                Active Courier
                            </label>
                        </div>
                        <small class="form-text text-muted">Inactive couriers won't appear in order forms</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Update Courier
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Courier Modal -->
<div class="modal fade" id="deleteCourierModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-trash me-2"></i>Delete Courier
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete <strong id="deleteCourierName"></strong>?</p>
                <p class="text-danger small">This action cannot be undone. If this courier has associated orders, they will be affected.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteCourierForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash me-2"></i>Delete Courier
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Courier management functions
function editCourier(id, name, phone, email, isActive) {
    document.getElementById('edit_courier_name').value = name;
    document.getElementById('edit_courier_phone').value = phone;
    document.getElementById('edit_courier_email').value = email || '';
    document.getElementById('edit_courier_is_active').checked = isActive;
    
    document.getElementById('editCourierForm').action = `/admin/couriers/${id}`;
    
    new bootstrap.Modal(document.getElementById('editCourierModal')).show();
}

function deleteCourier(id, name) {
    document.getElementById('deleteCourierName').textContent = name;
    document.getElementById('deleteCourierForm').action = `/admin/couriers/${id}`;
    
    new bootstrap.Modal(document.getElementById('deleteCourierModal')).show();
}

// Handle form submissions
document.getElementById('addCourierForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    fetch(this.action, {
        method: 'POST',
        body: new FormData(this),
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while adding the courier.');
    });
});

document.getElementById('editCourierForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    fetch(this.action, {
        method: 'POST',
        body: new FormData(this),
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while updating the courier.');
    });
});

document.getElementById('deleteCourierForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    fetch(this.action, {
        method: 'POST',
        body: new FormData(this),
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while deleting the courier.');
    });
});
</script>
@endpush 