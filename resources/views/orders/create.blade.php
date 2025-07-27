@extends('layouts.app')

@section('title', 'Create Order')

@section('page-title', 'Create Order')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-10">
        <div class="card shadow-sm">
            <div class="card-header">
                <h5 class="mb-0">Create New Order</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('orders.store') }}" id="orderForm">
                    @csrf

                    <div class="row">
                        <div class="col-md-6">
                            @if(auth()->user()->isAdmin())
                                <div class="mb-3">
                                    <label for="distributor_id" class="form-label">Distributor *</label>
                                    <select class="form-select @error('distributor_id') is-invalid @enderror" id="distributor_id" name="distributor_id" required>
                                        <option value="">Select Distributor</option>
                                        @foreach($distributors as $distributor)
                                            <option value="{{ $distributor->id }}" 
                                                data-international="{{ $distributor->is_international ? 'true' : 'false' }}"
                                                {{ old('distributor_id') == $distributor->id ? 'selected' : '' }}>
                                                {{ $distributor->user->name }} - {{ $distributor->company_name }}
                                                @if($distributor->is_international)
                                                    (International)
                                                @endif
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('distributor_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            @elseif(auth()->user()->isDistributor())
                                <input type="hidden" name="distributor_id" value="{{ auth()->user()->distributor->id }}">
                            @endif

                            <div class="mb-3">
                                <label for="customer_id" class="form-label">Customer *</label>
                                <select class="form-select @error('customer_id') is-invalid @enderror" id="customer_id" name="customer_id" required>
                                    <option value="">Select Customer</option>
                                    @foreach($customers as $customer)
                                        <option value="{{ $customer->id }}" 
                                                data-distributor="{{ $customer->distributor_id }}"
                                                {{ old('customer_id') == $customer->id ? 'selected' : '' }}>
                                            {{ $customer->name }} - {{ $customer->email ?? 'No Email' }}
                                            @if(auth()->user()->isAdmin())
                                                ({{ $customer->distributor->user->name }})
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                                @error('customer_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="courier_id" class="form-label">Courier (Optional)</label>
                                <select class="form-select @error('courier_id') is-invalid @enderror" id="courier_id" name="courier_id">
                                    <option value="">Select Courier</option>
                                    @foreach($couriers as $courier)
                                        <option value="{{ $courier->id }}" {{ old('courier_id') == $courier->id ? 'selected' : '' }}>
                                            {{ $courier->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('courier_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="notes" class="form-label">Notes</label>
                                <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="3" placeholder="Additional notes for this order">{{ old('notes') }}</textarea>
                                @error('notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="payment_status" class="form-label">Payment Status *</label>
                                <select class="form-select @error('payment_status') is-invalid @enderror" id="payment_status" name="payment_status" required>
                                    <option value="">Select Payment Status</option>
                                    <option value="unpaid" {{ old('payment_status') === 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                                    <option value="partially_paid" {{ old('payment_status') === 'partially_paid' ? 'selected' : '' }}>50% Downpayment</option>
                                    <option value="fully_paid" {{ old('payment_status') === 'fully_paid' ? 'selected' : '' }}>Fully Paid</option>
                                </select>
                                @error('payment_status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">Select the current payment status for this order</small>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="mb-0">Order Items</h6>
                            <button type="button" class="btn btn-sm btn-primary" id="addProduct">
                                <i class="fas fa-plus me-1"></i>Add Product
                            </button>
                        </div>
                        
                        <div id="productsContainer">
                            <!-- Product items will be added here -->
                        </div>
                        
                        @error('products')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6 offset-md-6">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="card-title">Order Summary</h6>
                                    <div class="d-flex justify-content-between">
                                        <span>Total Amount:</span>
                                        <span class="fw-bold" id="totalAmount">$0.00</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between mt-4">
                        <a href="{{ route('orders.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Back to List
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Create Order
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
    const distributorSelect = document.getElementById('distributor_id');
    const customerSelect = document.getElementById('customer_id');
    const productsContainer = document.getElementById('productsContainer');
    const addProductBtn = document.getElementById('addProduct');
    const totalAmountSpan = document.getElementById('totalAmount');
    
    let productIndex = 0;

    // Handle distributor selection (for admin)
    if (distributorSelect) {
        distributorSelect.addEventListener('change', function() {
            const selectedDistributorId = this.value;
            
            // Show/hide customers based on distributor
            Array.from(customerSelect.options).forEach(option => {
                if (option.value === '') return; // Skip placeholder
                
                const customerDistributorId = option.dataset.distributor;
                if (selectedDistributorId === '' || customerDistributorId === selectedDistributorId) {
                    option.style.display = '';
                } else {
                    option.style.display = 'none';
                }
            });
            
            // Reset customer selection
            customerSelect.value = '';
            
            // Update all product prices when distributor changes
            document.querySelectorAll('.product-item').forEach(item => {
                const productSelect = item.querySelector('.product-select');
                const quantityInput = item.querySelector('.product-quantity');
                if (productSelect.value && quantityInput.value) {
                    updateProductPrice(productSelect, quantityInput);
                }
            });
        });
    }

    // Add product function
    function addProduct() {
        const productDiv = document.createElement('div');
        productDiv.className = 'card mb-3 product-item';
        productDiv.dataset.index = productIndex;
        productDiv.innerHTML = `
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-6 col-md-3 col-lg-2">
                        <label class="form-label small fw-semibold">Product</label>
                        <select class="form-select product-select" name="products[${productIndex}][product_id]" required>
                            <option value="">Select Product</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}" 
                                        data-metals='@json($product->metals ?? [])'
                                        data-has-fonts="{{ $product->hasFonts() ? 'true' : 'false' }}"
                                        data-fonts='@json($product->fonts ?? [])'
                                        data-font-requirement="{{ $product->font_requirement ?? 0 }}"
                                        data-has-stones="{{ $product->hasStones() ? 'true' : 'false' }}"
                                        data-stones='@json($product->stones ?? [])'
                                        data-has-ring-size="{{ $product->hasRingSize() ? 'true' : 'false' }}"
                                        data-currency="{{ $product->currency ?? '₱' }}">
                                    {{ $product->name }} - {{ $product->sku }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-6 col-md-3 col-lg-2">
                        <label class="form-label small fw-semibold">Quantity</label>
                        <input type="number" class="form-control product-quantity" 
                               name="products[${productIndex}][quantity]" min="1" value="1" required>
                    </div>
                    <div class="col-6 col-md-3 col-lg-2">
                        <label class="form-label small fw-semibold">Metal</label>
                        <select class="form-select product-metal" name="products[${productIndex}][metal]" required>
                            <option value="">Select Metal</option>
                        </select>
                    </div>
                    <div class="col-12 col-lg-1">
                        <label class="form-label small fw-semibold">Price</label>
                        <div class="form-control-plaintext product-price fw-bold text-primary">$0.00</div>
                    </div>
                </div>
                
                <!-- Product Options Row - Stones, Fonts, and Ring Size -->
                <div class="row g-3 mt-2">
                    <div class="col-12">
                        <div class="product-stones-container" style="display: none;">
                            <label class="form-label small fw-semibold">Stones</label>
                            <div class="form-control-plaintext product-stones-display">-</div>
                            <div class="product-stones-selection" style="display: none;">
                                <!-- Stone selection will be dynamically added here -->
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="product-fonts-container" style="display: none;">
                            <label class="form-label small fw-semibold">Fonts</label>
                            <div class="form-control-plaintext product-fonts-display">-</div>
                            <div class="product-fonts-selection" style="display: none;">
                                <!-- Font selection will be dynamically added here -->
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="product-ring-size-container" style="display: none;">
                            <label class="form-label small fw-semibold">Ring Size</label>
                            <div class="form-control-plaintext product-ring-size-display">-</div>
                            <div class="product-ring-size-selection" style="display: none;">
                                <!-- Ring size selection will be dynamically added here -->
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mt-3 d-flex justify-content-end">
                    <button type="button" class="btn btn-sm btn-danger remove-product">
                        <i class="fas fa-trash me-1"></i>Remove
                    </button>
                </div>
            </div>
        `;

        productsContainer.appendChild(productDiv);
        productIndex++;

        // Add event listeners to new product
        const productSelect = productDiv.querySelector('.product-select');
        const metalSelect = productDiv.querySelector('.product-metal');
        const fontsContainer = productDiv.querySelector('.product-fonts-container');
        const fontsDisplay = productDiv.querySelector('.product-fonts-display');
        const fontsSelection = productDiv.querySelector('.product-fonts-selection');
        const stonesContainer = productDiv.querySelector('.product-stones-container');
        const stonesDisplay = productDiv.querySelector('.product-stones-display');
        const stonesSelection = productDiv.querySelector('.product-stones-selection');
        const ringSizeContainer = productDiv.querySelector('.product-ring-size-container');
        const ringSizeDisplay = productDiv.querySelector('.product-ring-size-display');
        const ringSizeSelection = productDiv.querySelector('.product-ring-size-selection');
        const quantityInput = productDiv.querySelector('.product-quantity');
        const removeBtn = productDiv.querySelector('.remove-product');

        productSelect.addEventListener('change', function() {
            updateMetalOptions(this, metalSelect);
            updateFontDisplay(this, fontsDisplay, fontsSelection);
            updateStonesDisplay(this, stonesDisplay, stonesSelection);
            updateRingSizeDisplay(this, ringSizeDisplay, ringSizeSelection);
            updateProductPrice(this, quantityInput);
        });

        metalSelect.addEventListener('change', function() {
            updateProductPrice(productSelect, quantityInput);
        });

        quantityInput.addEventListener('input', function() {
            updateProductPrice(productSelect, this);
        });

        removeBtn.addEventListener('click', function() {
            productDiv.remove();
            updateTotalAmount();
        });
    }

    function updateMetalOptions(productSelect, metalSelect) {
        const selectedOption = productSelect.options[productSelect.selectedIndex];
        const metals = JSON.parse(selectedOption.dataset.metals || '[]');

        // Clear and populate metal options
        metalSelect.innerHTML = '<option value="">Select Metal</option>';
        metals.forEach(metal => {
            const option = document.createElement('option');
            option.value = metal;
            option.textContent = metal;
            metalSelect.appendChild(option);
        });

        // Reset price
        const priceSpan = productSelect.closest('.product-item').querySelector('.product-price');
        priceSpan.textContent = '$0.00';
    }

    function updateFontDisplay(productSelect, fontsDisplay, fontsSelection) {
        const selectedOption = productSelect.options[productSelect.selectedIndex];
        const hasFonts = selectedOption.dataset.hasFonts === 'true';
        const fonts = JSON.parse(selectedOption.dataset.fonts || '[]');
        const fontRequirement = parseInt(selectedOption.dataset.fontRequirement || '0');
        const productIndex = productSelect.closest('.product-item').dataset.index;
        const fontsContainer = fontsDisplay.closest('.product-fonts-container');

        // Clear and populate font display
        fontsDisplay.innerHTML = '';
        fontsSelection.innerHTML = '';
        
        // Only show fonts if the product actually requires them
        if (hasFonts && fonts.length > 0 && fontRequirement > 0) {
            // Show font selection interface
            fontsDisplay.style.display = 'none';
            fontsSelection.style.display = 'block';
            fontsContainer.style.display = 'block';
            
            // Create font selection interface
            fontsSelection.innerHTML = `
                <div class="font-selection-compact">
                    <div class="d-flex align-items-center mb-2">
                        <small class="text-muted fw-semibold me-3">
                            <i class="fas fa-font me-1"></i>Select ${fontRequirement} font${fontRequirement > 1 ? 's' : ''}:
                        </small>
                        <div class="font-options">
                            ${fonts.map(font => `
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input font-checkbox" type="checkbox" 
                                           id="font_${productSelect.value}_${font.replace(/\s+/g, '_')}" 
                                           value="${font}" name="products[${productIndex}][fonts][]">
                                    <label class="form-check-label small" for="font_${productSelect.value}_${font.replace(/\s+/g, '_')}">
                                        ${font}
                                    </label>
                                </div>
                            `).join('')}
                        </div>
                    </div>
                </div>
            `;
            
            // Add event listeners to font checkboxes
            const fontCheckboxes = fontsSelection.querySelectorAll('.font-checkbox');
            fontCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    const checkedBoxes = fontsSelection.querySelectorAll('.font-checkbox:checked');
                    if (checkedBoxes.length > fontRequirement) {
                        this.checked = false;
                        alert(`You can only select up to ${fontRequirement} font${fontRequirement > 1 ? 's' : ''} for this product.`);
                    }
                });
            });
            
        } else {
            // Hide font selection
            fontsDisplay.style.display = 'block';
            fontsSelection.style.display = 'none';
            fontsDisplay.innerHTML = '-';
            fontsContainer.style.display = 'none';
        }
    }

    function updateStonesDisplay(productSelect, stonesDisplay, stonesSelection) {
        const selectedOption = productSelect.options[productSelect.selectedIndex];
        const hasStones = selectedOption.dataset.hasStones === 'true';
        const stones = JSON.parse(selectedOption.dataset.stones || '[]');
        const productIndex = productSelect.closest('.product-item').dataset.index;
        const stonesContainer = stonesDisplay.closest('.product-stones-container');



        // Clear and populate stone display
        stonesDisplay.innerHTML = '';
        stonesSelection.innerHTML = '';
        
        // Only show stones if the product actually requires them
        if (hasStones && stones.length > 0) {
            // Show stone selection interface
            stonesDisplay.style.display = 'none';
            stonesSelection.style.display = 'block';
            stonesContainer.style.display = 'block';
            
            // Create stone selection interface
            stonesSelection.innerHTML = `
                <div class="stone-selection-compact">
                    <div class="d-flex align-items-center mb-2">
                        <small class="text-muted fw-semibold me-3">
                            <i class="fas fa-gem me-1"></i>Select stones:
                        </small>
                        <div class="stone-options">
                            ${stones.map(stone => `
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input stone-checkbox" type="checkbox" 
                                           id="stone_${productSelect.value}_${stone.replace(/\s+/g, '_')}" 
                                           value="${stone}" name="products[${productIndex}][stones][]">
                                    <label class="form-check-label small" for="stone_${productSelect.value}_${stone.replace(/\s+/g, '_')}">
                                        ${stone}
                                    </label>
                                </div>
                            `).join('')}
                        </div>
                    </div>
                </div>
            `;
            
        } else {
            // Hide stone selection
            stonesDisplay.style.display = 'block';
            stonesSelection.style.display = 'none';
            stonesDisplay.innerHTML = '-';
            stonesContainer.style.display = 'none';
        }
    }

    function updateRingSizeDisplay(productSelect, ringSizeDisplay, ringSizeSelection) {
        const selectedOption = productSelect.options[productSelect.selectedIndex];
        const hasRingSize = selectedOption.dataset.hasRingSize === 'true';
        const productIndex = productSelect.closest('.product-item').dataset.index;
        const ringSizeContainer = ringSizeDisplay.closest('.product-ring-size-container');



        // Clear and populate ring size display
        ringSizeDisplay.innerHTML = '';
        ringSizeSelection.innerHTML = '';
        
        // Only show ring size if the product actually requires it
        if (hasRingSize) {
            // Show ring size selection interface
            ringSizeDisplay.style.display = 'none';
            ringSizeSelection.style.display = 'block';
            ringSizeContainer.style.display = 'block';
            
            // Create ring size selection interface
            ringSizeSelection.innerHTML = `
                <div class="ring-size-selection-compact">
                    <div class="d-flex align-items-center mb-2">
                        <small class="text-muted fw-semibold me-3">
                            <i class="fas fa-circle me-1"></i>Select ring size:
                        </small>
                        <div class="ring-size-options">
                            <select class="form-select form-select-sm" name="products[${productIndex}][ring_size]" required>
                                <option value="">Select Ring Size</option>
                                <option value="3">3</option>
                                <option value="3.5">3.5</option>
                                <option value="4">4</option>
                                <option value="4.5">4.5</option>
                                <option value="5">5</option>
                                <option value="5.5">5.5</option>
                                <option value="6">6</option>
                                <option value="6.5">6.5</option>
                                <option value="7">7</option>
                                <option value="7.5">7.5</option>
                                <option value="8">8</option>
                                <option value="8.5">8.5</option>
                                <option value="9">9</option>
                                <option value="9.5">9.5</option>
                                <option value="10">10</option>
                                <option value="10.5">10.5</option>
                                <option value="11">11</option>
                                <option value="11.5">11.5</option>
                                <option value="12">12</option>
                                <option value="12.5">12.5</option>
                                <option value="13">13</option>
                            </select>
                        </div>
                    </div>
                </div>
            `;
            
        } else {
            // Hide ring size selection
            ringSizeDisplay.style.display = 'block';
            ringSizeSelection.style.display = 'none';
            ringSizeDisplay.innerHTML = '-';
            ringSizeContainer.style.display = 'none';
        }
    }

    async function updateProductPrice(productSelect, quantityInput) {
        const selectedOption = productSelect.options[productSelect.selectedIndex];
        const metalSelect = productSelect.closest('.product-item').querySelector('.product-metal');
        const priceSpan = productSelect.closest('.product-item').querySelector('.product-price');
        
        // Get distributor's international status
        const distributorSelect = document.getElementById('distributor_id');
        const isInternational = distributorSelect ? 
            (distributorSelect.options[distributorSelect.selectedIndex]?.dataset.international === 'true') : 
            false;
        
        if (productSelect.value && metalSelect.value && quantityInput.value) {
            try {
                const response = await fetch(`/api/products/${productSelect.value}/pricing`);
                const data = await response.json();
                
                const pricing = isInternational ? data.international_pricing : data.local_pricing;
                const price = pricing[metalSelect.value];
                const quantity = parseInt(quantityInput.value) || 0;
                
                if (price !== undefined) {
                    const total = price * quantity;
                    const symbol = isInternational ? '$' : '₱';
                    priceSpan.textContent = `${symbol}${total.toFixed(2)}`;
                } else {
                    priceSpan.textContent = '$0.00';
                }
            } catch (error) {
                console.error('Error fetching pricing:', error);
                priceSpan.textContent = '$0.00';
            }
        } else {
            priceSpan.textContent = '$0.00';
        }
        
        updateTotalAmount();
    }

    function updateTotalAmount() {
        let total = 0;
        
        // Get distributor's international status
        const distributorSelect = document.getElementById('distributor_id');
        const isInternational = distributorSelect ? 
            (distributorSelect.options[distributorSelect.selectedIndex]?.dataset.international === 'true') : 
            false;
        
        document.querySelectorAll('.product-item').forEach(item => {
            const priceText = item.querySelector('.product-price').textContent;
            if (priceText && priceText !== '$0.00' && priceText !== '₱0.00') {
                const price = parseFloat(priceText.replace(/[₱$]/g, ''));
                if (!isNaN(price)) {
                    total += price;
                }
            }
        });
        
        const symbol = isInternational ? '$' : '₱';
        totalAmountSpan.textContent = `${symbol}${total.toFixed(2)}`;
    }

    // Add first product
    addProduct();

    // Add product button
    addProductBtn.addEventListener('click', addProduct);
});
</script>
@endpush
@endsection 