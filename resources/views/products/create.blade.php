@extends('layouts.app')

@section('title', 'Add Product')

@section('page-title', 'Add Product')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-10">
        <div class="card shadow-sm">
            <div class="card-header">
                <h5 class="mb-0">Add New Product</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('products.store') }}" id="productForm" enctype="multipart/form-data">
                    @csrf

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="name" class="form-label">Product Name *</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                       id="name" name="name" value="{{ old('name') }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="image" class="form-label">Product Image</label>
                                <input type="file" class="form-control @error('image') is-invalid @enderror" 
                                       id="image" name="image" accept="image/*">
                                @error('image')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">Upload a product image (JPEG, PNG, JPG, GIF, max 2MB)</small>
                            </div>

                            <div class="mb-3">
                                <label for="sku" class="form-label">SKU *</label>
                                <input type="text" class="form-control @error('sku') is-invalid @enderror" 
                                       id="sku" name="sku" value="{{ old('sku') }}" required>
                                @error('sku')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">Stock Keeping Unit - unique identifier for the product</small>
                            </div>

                            <div class="mb-3">
                                <label for="category" class="form-label">Category *</label>
                                <select class="form-select @error('category') is-invalid @enderror" id="category" name="category" required>
                                    <option value="">Select Category</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category }}" {{ old('category') === $category ? 'selected' : '' }}>
                                            {{ $category }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('category')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="sub_category" class="form-label">Sub Category *</label>
                                <select class="form-select @error('sub_category') is-invalid @enderror" id="sub_category" name="sub_category" required>
                                    <option value="">Select Sub Category</option>
                                </select>
                                @error('sub_category')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3" id="customSubCategoryDiv" style="display: none;">
                                <label for="custom_sub_category" class="form-label">Custom Sub Category *</label>
                                <input type="text" class="form-control @error('custom_sub_category') is-invalid @enderror" 
                                       id="custom_sub_category" name="custom_sub_category" value="{{ old('custom_sub_category') }}">
                                @error('custom_sub_category')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Available Metals *</label>
                                <div class="border rounded p-3">
                                    @foreach($metals as $metal)
                                        <div class="form-check">
                                            <input class="form-check-input metal-checkbox" type="checkbox" id="metal_{{ $loop->index }}" 
                                                   name="metals[]" value="{{ $metal }}" 
                                                   {{ in_array($metal, old('metals', [])) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="metal_{{ $loop->index }}">
                                                {{ $metal }}
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                                @error('metals')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1"
                                           {{ old('is_active', true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">
                                        Active Product
                                    </label>
                                </div>
                                <small class="form-text text-muted">Inactive products won't appear in order forms</small>
                                @error('is_active')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mb-3" id="metalPricingSection">
                        <label class="form-label">Metal Pricing *</label>
                        <div class="border rounded p-3">
                            <p class="text-muted small mb-3">Set pricing for each selected metal type for both local (PHP) and international (USD) markets.</p>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <h6 class="text-primary">Local Pricing (PHP)</h6>
                                    <div id="localPricingContainer">
                                        <!-- Local pricing fields will be dynamically added here -->
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <h6 class="text-success">International Pricing (USD)</h6>
                                    <div id="internationalPricingContainer">
                                        <!-- International pricing fields will be dynamically added here -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                                                <div class="mb-3" id="fontsSection">
                                <label class="form-label">Available Fonts</label>
                                <div class="border rounded p-3">
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label for="font_requirement" class="form-label">Font Requirement *</label>
                                            <select class="form-select" id="font_requirement" name="font_requirement">
                                                <option value="0">No Fonts Required</option>
                                                <option value="1">Font 1 (Only one font)</option>
                                                <option value="2">Font 2 (Only two fonts)</option>
                                                <option value="3">Font 3 (Only three fonts)</option>
                                                <option value="4">Font 4 (Only four fonts)</option>
                                                <option value="5">Font 5 (Only five fonts)</option>
                                                <option value="6">Font 6 (Only six fonts)</option>
                                                <option value="7">Font 7 (Only seven fonts)</option>
                                                <option value="8">Font 8 (Only eight fonts)</option>
                                                <option value="9">Font 9 (Only nine fonts)</option>
                                                <option value="10">Font 10 (Only ten fonts)</option>
                                            </select>
                                            <small class="form-text text-muted">Select how many fonts distributors can choose from when ordering this product</small>
                                        </div>
                                    </div>
                                    
                                    <p class="text-muted small mb-3">Select all available fonts for this product. Distributors will be restricted to choosing only the number of fonts specified in the font requirement above.</p>
                                    <div id="fontsContainer">
                                        @foreach($fonts as $font)
                                            <div class="form-check">
                                                <input class="form-check-input font-checkbox" type="checkbox" 
                                                       id="font_{{ $font->id }}" name="fonts[]" value="{{ $font->name }}">
                                                <label class="form-check-label" for="font_{{ $font->id }}">
                                                    {{ $font->name }}
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('products.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Back to List
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Add Product
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
    const categorySelect = document.getElementById('category');
    const subCategorySelect = document.getElementById('sub_category');
    const customSubCategoryDiv = document.getElementById('customSubCategoryDiv');
    const customSubCategoryInput = document.getElementById('custom_sub_category');
    const fontsSection = document.getElementById('fontsSection');
    const localPricingContainer = document.getElementById('localPricingContainer');
    const internationalPricingContainer = document.getElementById('internationalPricingContainer');
    const metalCheckboxes = document.querySelectorAll('.metal-checkbox');
    
    const metals = @json($metals);

    // Handle category change
    categorySelect.addEventListener('change', function() {
        const category = this.value;
        subCategorySelect.innerHTML = '<option value="">Select Sub Category</option>';
        customSubCategoryDiv.style.display = 'none';
        customSubCategoryInput.required = false;
        
        if (category) {
            // Fetch subcategories from API
            fetch(`/api/products/subcategories?category=${encodeURIComponent(category)}`)
                .then(response => response.json())
                .then(subCategories => {
                    subCategories.forEach(subCat => {
                        const option = document.createElement('option');
                        option.value = subCat;
                        option.textContent = subCat;
                        subCategorySelect.appendChild(option);
                    });
                })
                .catch(error => {
                    console.error('Error fetching subcategories:', error);
                });
        }
    });

    // Handle sub-category change
    subCategorySelect.addEventListener('change', function() {
        const subCategory = this.value;
        if (subCategory === 'Others') {
            customSubCategoryDiv.style.display = 'block';
            customSubCategoryInput.required = true;
        } else {
            customSubCategoryDiv.style.display = 'none';
            customSubCategoryInput.required = false;
        }
    });

    // Handle metal checkbox changes
    metalCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateMetalPricing);
    });

    function updateMetalPricing() {
        localPricingContainer.innerHTML = '';
        internationalPricingContainer.innerHTML = '';
        
        const selectedMetals = Array.from(metalCheckboxes)
            .filter(cb => cb.checked)
            .map(cb => cb.value);

        if (selectedMetals.length === 0) {
            return;
        }

        selectedMetals.forEach(metal => {
            const localPriceKey = 'local_price_' + metal.replace(/\s+/g, '_').toLowerCase();
            const internationalPriceKey = 'international_price_' + metal.replace(/\s+/g, '_').toLowerCase();
            
            // Local pricing field
            const localPricingDiv = document.createElement('div');
            localPricingDiv.className = 'row mb-2';
            localPricingDiv.innerHTML = `
                <div class="col-md-4">
                    <label class="form-label">${metal}</label>
                </div>
                <div class="col-md-8">
                    <div class="input-group">
                        <span class="input-group-text">₱</span>
                        <input type="number" step="0.01" min="0" class="form-control" 
                               name="${localPriceKey}" placeholder="0.00" required>
                    </div>
                </div>
            `;
            localPricingContainer.appendChild(localPricingDiv);
            
            // International pricing field
            const internationalPricingDiv = document.createElement('div');
            internationalPricingDiv.className = 'row mb-2';
            internationalPricingDiv.innerHTML = `
                <div class="col-md-4">
                    <label class="form-label">${metal}</label>
                </div>
                <div class="col-md-8">
                    <div class="input-group">
                        <span class="input-group-text">$</span>
                        <input type="number" step="0.01" min="0" class="form-control" 
                               name="${internationalPriceKey}" placeholder="0.00" required>
                    </div>
                </div>
            `;
            internationalPricingContainer.appendChild(internationalPricingDiv);
        });
    }

    // Handle font requirement change
    const fontRequirementSelect = document.getElementById('font_requirement');
    const fontCheckboxes = document.querySelectorAll('.font-checkbox');
    
    fontRequirementSelect.addEventListener('change', function() {
        const requirement = parseInt(this.value);
        
        // Enable/disable checkboxes based on requirement
        if (requirement > 0) {
            fontCheckboxes.forEach(checkbox => {
                checkbox.disabled = false;
            });
        } else {
            fontCheckboxes.forEach(checkbox => {
                checkbox.disabled = true;
                checkbox.checked = false;
            });
        }
    });

    // Set initial state
    if (categorySelect.value) {
        categorySelect.dispatchEvent(new Event('change'));
    }
    if (subCategorySelect.value) {
        subCategorySelect.dispatchEvent(new Event('change'));
    }
    updateMetalPricing();
    
    // Initialize font requirement
    fontRequirementSelect.dispatchEvent(new Event('change'));
});
</script>
@endpush
@endsection 