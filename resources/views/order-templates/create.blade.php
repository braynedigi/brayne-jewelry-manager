@extends('layouts.app')

@section('title', 'Create Order Template')

@section('page-title', 'Create Order Template')

@section('content')
<div class="card shadow-sm">
    <div class="card-header">
        <h5 class="mb-0">Create New Order Template</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('order-templates.store') }}" method="POST" id="templateForm">
            @csrf
            
            <!-- Basic Information -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="name" class="form-label">Template Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                               id="name" name="name" value="{{ old('name') }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="priority" class="form-label">Priority <span class="text-danger">*</span></label>
                        <select class="form-select @error('priority') is-invalid @enderror" id="priority" name="priority" required>
                            <option value="">Select Priority</option>
                            @foreach($priorities as $value => $label)
                                <option value="{{ $value }}" {{ old('priority') == $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        @error('priority')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control @error('description') is-invalid @enderror" 
                          id="description" name="description" rows="3" 
                          placeholder="Optional description for this template">{{ old('description') }}</textarea>
                @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Products Section -->
            <div class="mb-4">
                <h6 class="mb-3">Products <span class="text-danger">*</span></h6>
                <div id="productsContainer">
                    <!-- Products will be added here dynamically -->
                </div>
                <button type="button" class="btn btn-outline-primary btn-sm" onclick="addProduct()">
                    <i class="fas fa-plus me-1"></i>Add Product
                </button>
            </div>

            <!-- Default Notes -->
            <div class="mb-4">
                <label for="default_notes" class="form-label">Default Notes</label>
                <textarea class="form-control @error('default_notes') is-invalid @enderror" 
                          id="default_notes" name="default_notes" rows="3" 
                          placeholder="Default notes to include with orders created from this template">{{ old('default_notes') }}</textarea>
                @error('default_notes')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Form Actions -->
            <div class="d-flex justify-content-between">
                <a href="{{ route('order-templates.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i>Back to Templates
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-1"></i>Create Template
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Product Template (Hidden) -->
<template id="productTemplate">
    <div class="product-item card mb-3">
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label">Product <span class="text-danger">*</span></label>
                        <select class="form-select product-select" name="products[INDEX][product_id]" required>
                            <option value="">Select Product</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}" 
                                        data-metals='@json($product->metals ?? [])'
                                        data-fonts='@json($product->fonts ?? [])'
                                        data-requires-font="{{ $product->font_requirement ? 'true' : 'false' }}">
                                    {{ $product->name }} ({{ $product->sku }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="mb-3">
                        <label class="form-label">Quantity <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" name="products[INDEX][quantity]" 
                               min="1" value="1" required>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="mb-3">
                        <label class="form-label">Metal <span class="text-danger">*</span></label>
                        <select class="form-select metal-select" name="products[INDEX][metal]" required>
                            <option value="">Select Metal</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="mb-3">
                        <label class="form-label">Font</label>
                        <select class="form-select font-select" name="products[INDEX][font]">
                            <option value="">Select Font</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-1">
                    <div class="mb-3">
                        <label class="form-label">&nbsp;</label>
                        <button type="button" class="btn btn-outline-danger btn-sm w-100" onclick="removeProduct(this)">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

@endsection

@push('scripts')
<script>
let productIndex = 0;

function addProduct() {
    const container = document.getElementById('productsContainer');
    const template = document.getElementById('productTemplate');
    const clone = template.content.cloneNode(true);
    
    // Update all INDEX placeholders
    clone.querySelectorAll('[name*="INDEX"]').forEach(element => {
        element.name = element.name.replace('INDEX', productIndex);
    });
    
    // Set up event listeners for the new product
    const productSelect = clone.querySelector('.product-select');
    const metalSelect = clone.querySelector('.metal-select');
    const fontSelect = clone.querySelector('.font-select');
    
    productSelect.addEventListener('change', function() {
        updateMetalOptions(this, metalSelect);
        updateFontOptions(this, fontSelect);
    });
    
    container.appendChild(clone);
    productIndex++;
}

function removeProduct(button) {
    button.closest('.product-item').remove();
}

function updateMetalOptions(productSelect, metalSelect) {
    const selectedOption = productSelect.options[productSelect.selectedIndex];
    const metals = JSON.parse(selectedOption.dataset.metals || '[]');
    
    metalSelect.innerHTML = '<option value="">Select Metal</option>';
    metals.forEach(metal => {
        const option = document.createElement('option');
        option.value = metal;
        option.textContent = metal;
        metalSelect.appendChild(option);
    });
}

function updateFontOptions(productSelect, fontSelect) {
    const selectedOption = productSelect.options[productSelect.selectedIndex];
    const fonts = JSON.parse(selectedOption.dataset.fonts || '[]');
    const requiresFont = selectedOption.dataset.requiresFont === 'true';
    
    fontSelect.innerHTML = '<option value="">Select Font</option>';
    if (requiresFont && fonts.length > 0) {
        fontSelect.required = true;
        fonts.forEach(font => {
            const option = document.createElement('option');
            option.value = font;
            option.textContent = font;
            fontSelect.appendChild(option);
        });
    } else {
        fontSelect.required = false;
    }
}

// Add initial product on page load
document.addEventListener('DOMContentLoaded', function() {
    addProduct();
});

// Form validation
document.getElementById('templateForm').addEventListener('submit', function(e) {
    const products = document.querySelectorAll('.product-item');
    if (products.length === 0) {
        e.preventDefault();
        alert('Please add at least one product to the template.');
        return false;
    }
    
    // Check if all required fields are filled
    let isValid = true;
    products.forEach(product => {
        const productSelect = product.querySelector('.product-select');
        const quantityInput = product.querySelector('input[name*="quantity"]');
        const metalSelect = product.querySelector('.metal-select');
        
        if (!productSelect.value || !quantityInput.value || !metalSelect.value) {
            isValid = false;
        }
    });
    
    if (!isValid) {
        e.preventDefault();
        alert('Please fill in all required fields for all products.');
        return false;
    }
});
</script>
@endpush

@push('styles')
<style>
.product-item {
    border: 1px solid #e9ecef;
    transition: all 0.2s ease;
}

.product-item:hover {
    border-color: #007bff;
    box-shadow: 0 2px 4px rgba(0,123,255,0.1);
}

.form-label {
    font-weight: 500;
    color: #495057;
}

.card-header {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-bottom: 1px solid #dee2e6;
}
</style>
@endpush 