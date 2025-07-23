@extends('layouts.app')

@section('title', 'Product Categories')

@section('page-title', 'Product Categories')
@section('page-subtitle', 'Manage product categories, metals, stones, fonts, and ring sizes')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <!-- Tab Navigation -->
                <div class="tab-navigation mb-3">
                    <button type="button" class="tab-button active" onclick="switchTab('categories')">
                        <i class="fas fa-tags me-2"></i>Categories
                    </button>
                    <button type="button" class="tab-button" onclick="switchTab('metals')">
                        <i class="fas fa-gem me-2"></i>Metals
                    </button>
                    <button type="button" class="tab-button" onclick="switchTab('stones')">
                        <i class="fas fa-diamond me-2"></i>Stones
                    </button>
                    <button type="button" class="tab-button" onclick="switchTab('fonts')">
                        <i class="fas fa-font me-2"></i>Fonts
                    </button>
                    <button type="button" class="tab-button" onclick="switchTab('ring-sizes')">
                        <i class="fas fa-circle me-2"></i>Ring Sizes
                    </button>
                </div>
                
                <style>
                    .tab-navigation {
                        display: flex;
                        gap: 10px;
                        border-bottom: 2px solid #e9ecef;
                        padding-bottom: 10px;
                        flex-wrap: wrap;
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
                        white-space: nowrap;
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
                <!-- Categories Tab -->
                <div class="tab-section active" id="categories">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h5 class="mb-1">Product Categories</h5>
                            <p class="text-muted mb-0">Manage product categories and subcategories</p>
                        </div>
                        <div>
                            <button type="button" class="btn btn-success me-2" data-bs-toggle="modal" data-bs-target="#addSubcategoryModal">
                                <i class="fas fa-plus me-2"></i>Add Subcategory
                            </button>
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
                                <i class="fas fa-plus me-2"></i>Add Category
                            </button>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Category Name</th>
                                    <th>Full Path</th>
                                    <th>Description</th>
                                    <th>Status</th>
                                    <th>Products</th>
                                    <th>Subcategories</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($allCategories as $category)
                                <tr class="{{ $category->isChild() ? 'table-light' : '' }}">
                                    <td>
                                        <div class="fw-bold">
                                            @if($category->isChild())
                                                <i class="fas fa-level-down-alt me-2 text-muted"></i>
                                            @endif
                                            {{ $category->name }}
                                        </div>
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ $category->full_path }}</small>
                                    </td>
                                    <td>{{ $category->description ?: 'No description' }}</td>
                                    <td>
                                        <span class="badge bg-{{ $category->is_active ? 'success' : 'secondary' }}">
                                            {{ $category->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-info">{{ $category->products->count() }}</span>
                                    </td>
                                    <td>
                                        @if($category->hasChildren())
                                            <span class="badge bg-warning">{{ $category->children->count() }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-sm btn-outline-primary" 
                                                    onclick="editCategory({{ $category->id }}, '{{ $category->name }}', '{{ $category->description }}', {{ $category->is_active ? 'true' : 'false' }}, {{ $category->parent_id ?: 'null' }})">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-danger" 
                                                    onclick="deleteCategory({{ $category->id }}, '{{ $category->name }}')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4">
                                        <i class="fas fa-tags fa-2x text-muted mb-2"></i>
                                        <p class="text-muted mb-0">No categories found. Add your first category to get started.</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Metals Tab -->
                <div class="tab-section" id="metals">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h5 class="mb-1">Product Metals</h5>
                            <p class="text-muted mb-0">Manage metal types like Gold, Silver, Platinum, etc.</p>
                        </div>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addMetalModal">
                            <i class="fas fa-plus me-2"></i>Add Metal
                        </button>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Description</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($metals as $metal)
                                <tr>
                                    <td>
                                        <div class="fw-bold">{{ $metal->name }}</div>
                                    </td>
                                    <td>{{ $metal->description ?: 'No description' }}</td>
                                    <td>
                                        <span class="badge bg-{{ $metal->is_active ? 'success' : 'secondary' }}">
                                            {{ $metal->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-sm btn-outline-primary" 
                                                    onclick="editMetal({{ $metal->id }}, '{{ $metal->name }}', '{{ $metal->description }}', {{ $metal->is_active ? 'true' : 'false' }})">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-danger" 
                                                    onclick="deleteMetal({{ $metal->id }}, '{{ $metal->name }}')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4">
                                        <i class="fas fa-gem fa-2x text-muted mb-2"></i>
                                        <p class="text-muted mb-0">No metals found. Add your first metal to get started.</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Stones Tab -->
                <div class="tab-section" id="stones">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h5 class="mb-1">Product Stones</h5>
                            <p class="text-muted mb-0">Manage stone types like Diamond, Ruby, Sapphire, etc.</p>
                        </div>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addStoneModal">
                            <i class="fas fa-plus me-2"></i>Add Stone
                        </button>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Description</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($stones as $stone)
                                <tr>
                                    <td>
                                        <div class="fw-bold">{{ $stone->name }}</div>
                                    </td>
                                    <td>{{ $stone->description ?: 'No description' }}</td>
                                    <td>
                                        <span class="badge bg-{{ $stone->is_active ? 'success' : 'secondary' }}">
                                            {{ $stone->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-sm btn-outline-primary" 
                                                    onclick="editStone({{ $stone->id }}, '{{ $stone->name }}', '{{ $stone->description }}', {{ $stone->is_active ? 'true' : 'false' }})">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-danger" 
                                                    onclick="deleteStone({{ $stone->id }}, '{{ $stone->name }}')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4">
                                        <i class="fas fa-diamond fa-2x text-muted mb-2"></i>
                                        <p class="text-muted mb-0">No stones found. Add your first stone to get started.</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Fonts Tab -->
                <div class="tab-section" id="fonts">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h5 class="mb-1">Product Fonts</h5>
                            <p class="text-muted mb-0">Manage font styles for personalized jewelry</p>
                        </div>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addFontModal">
                            <i class="fas fa-plus me-2"></i>Add Font
                        </button>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Description</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($fonts as $font)
                                <tr>
                                    <td>
                                        <div class="fw-bold">{{ $font->name }}</div>
                                    </td>
                                    <td>{{ $font->description ?: 'No description' }}</td>
                                    <td>
                                        <span class="badge bg-{{ $font->is_active ? 'success' : 'secondary' }}">
                                            {{ $font->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-sm btn-outline-primary" 
                                                    onclick="editFont({{ $font->id }}, '{{ $font->name }}', '{{ $font->description }}', {{ $font->is_active ? 'true' : 'false' }})">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-danger" 
                                                    onclick="deleteFont({{ $font->id }}, '{{ $font->name }}')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4">
                                        <i class="fas fa-font fa-2x text-muted mb-2"></i>
                                        <p class="text-muted mb-0">No fonts found. Add your first font to get started.</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Ring Sizes Tab -->
                <div class="tab-section" id="ring-sizes">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h5 class="mb-1">Ring Sizes</h5>
                            <p class="text-muted mb-0">Manage ring size options</p>
                        </div>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addRingSizeModal">
                            <i class="fas fa-plus me-2"></i>Add Ring Size
                        </button>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Size</th>
                                    <th>Description</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($ringSizes as $ringSize)
                                <tr>
                                    <td>
                                        <div class="fw-bold">{{ $ringSize->size }}</div>
                                    </td>
                                    <td>{{ $ringSize->description ?: 'No description' }}</td>
                                    <td>
                                        <span class="badge bg-{{ $ringSize->is_active ? 'success' : 'secondary' }}">
                                            {{ $ringSize->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-sm btn-outline-primary" 
                                                    onclick="editRingSize({{ $ringSize->id }}, '{{ $ringSize->size }}', '{{ $ringSize->description }}', {{ $ringSize->is_active ? 'true' : 'false' }})">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-danger" 
                                                    onclick="deleteRingSize({{ $ringSize->id }}, '{{ $ringSize->size }}')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4">
                                        <i class="fas fa-circle fa-2x text-muted mb-2"></i>
                                        <p class="text-muted mb-0">No ring sizes found. Add your first ring size to get started.</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Category Modals -->
@include('products.categories.partials.category-modals')

<!-- Metal Modals -->
@include('products.categories.partials.metal-modals')

<!-- Stone Modals -->
@include('products.categories.partials.stone-modals')

<!-- Font Modals -->
@include('products.categories.partials.font-modals')

<!-- Ring Size Modals -->
@include('products.categories.partials.ring-size-modals')

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Function to show a specific tab
    window.switchTab = function(tabId) {
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
        }
        
        // Add active class to clicked tab button
        var activeButton = document.querySelector(`.tab-button[onclick="switchTab('${tabId}')"]`);
        if (activeButton) {
            activeButton.classList.add('active');
        }
    }
    
    // Show the default active tab on page load
    switchTab('categories');
});
</script>
@endpush 