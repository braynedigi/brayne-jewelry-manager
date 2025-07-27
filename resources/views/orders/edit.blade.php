@extends('layouts.app')

@section('title', 'Edit Order')

@section('page-title', 'Edit Order')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-10">
        <div class="card shadow-sm">
            <div class="card-header">
                <h5 class="mb-0">Edit Order #{{ $order->order_number }}</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('orders.update', $order) }}" id="orderForm">
                    @csrf
                    @method('PUT')

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
                                                    {{ old('distributor_id', $order->distributor_id) == $distributor->id ? 'selected' : '' }}>
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
                            @endif

                            <div class="mb-3">
                                <label for="customer_id" class="form-label">Customer *</label>
                                <select class="form-select @error('customer_id') is-invalid @enderror" id="customer_id" name="customer_id" required>
                                    <option value="">Select Customer</option>
                                    @foreach($customers as $customer)
                                        <option value="{{ $customer->id }}" 
                                                data-distributor="{{ $customer->distributor_id }}"
                                                {{ old('customer_id', $order->customer_id) == $customer->id ? 'selected' : '' }}>
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
                                        <option value="{{ $courier->id }}" {{ old('courier_id', $order->courier_id) == $courier->id ? 'selected' : '' }}>
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
                                <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="3" placeholder="Additional notes for this order">{{ old('notes', $order->notes) }}</textarea>
                                @error('notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="payment_status" class="form-label">Payment Status *</label>
                                <select class="form-select @error('payment_status') is-invalid @enderror" id="payment_status" name="payment_status" required>
                                    <option value="">Select Payment Status</option>
                                    <option value="unpaid" {{ old('payment_status', $order->payment_status) === 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                                    <option value="partially_paid" {{ old('payment_status', $order->payment_status) === 'partially_paid' ? 'selected' : '' }}>50% Downpayment</option>
                                    <option value="fully_paid" {{ old('payment_status', $order->payment_status) === 'fully_paid' ? 'selected' : '' }}>Fully Paid</option>
                                </select>
                                @error('payment_status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">Select the current payment status for this order</small>
                            </div>

                            @if($order->canUpdateStatus(auth()->user()))
                                <div class="mb-3">
                                    <label for="order_status" class="form-label">Order Status</label>
                                    <select class="form-select @error('order_status') is-invalid @enderror" id="order_status" name="order_status">
                                        @foreach($order->getNextAvailableStatuses(auth()->user()) as $status => $label)
                                            <option value="{{ $status }}" {{ old('order_status', $order->order_status) === $status ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('order_status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">
                                        @if(auth()->user()->isAdmin())
                                            Update the order status to control the workflow
                                        @elseif(auth()->user()->isFactory())
                                            Move the order through the production process
                                        @endif
                                    </small>
                                </div>
                            @else
                                <div class="mb-3">
                                    <label class="form-label">Current Order Status</label>
                                    <div class="form-control-plaintext">
                                        <span class="badge bg-{{ $order->getOrderStatusColor() }}">
                                            {{ $order->getOrderStatusLabel() }}
                                        </span>
                                    </div>
                                    <small class="form-text text-muted">You don't have permission to update the order status</small>
                                </div>
                            @endif
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
                        <a href="{{ route('orders.show', $order) }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Back to Details
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Update Order
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
    function addProduct(existingProduct = null) {
        const productDiv = document.createElement('div');
        productDiv.className = 'card mb-3 product-item';
        productDiv.innerHTML = `
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <label class="form-label">Product *</label>
                        <select class="form-select product-select" name="products[${productIndex}][product_id]" required>
                            <option value="">Select Product</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}" 
                                        data-metals='@json($product->metals)'
                                        data-has-fonts="{{ $product->hasFonts() ? 'true' : 'false' }}"
                                        data-fonts='@json($product->fonts)'
                                        data-currency="{{ $product->currency }}"
                                        data-international="{{ $product->international_pricing ? 'true' : 'false' }}"
                                        ${existingProduct && existingProduct.product_id == {{ $product->id }} ? 'selected' : ''}>
                                    {{ $product->name }} - {{ $product->sku }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Quantity *</label>
                        <input type="number" class="form-control product-quantity" 
                               name="products[${productIndex}][quantity]" min="1" 
                               value="${existingProduct ? existingProduct.quantity : '1'}" required>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Metal *</label>
                        <select class="form-select product-metal" name="products[${productIndex}][metal]" required>
                            <option value="">Select Metal</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Font</label>
                        <div class="form-control-plaintext product-fonts">-</div>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Names</label>
                        <div class="form-control-plaintext product-name">-</div>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Price</label>
                        <div class="form-control-plaintext product-price">$0.00</div>
                    </div>
                </div>
                <div class="mt-2">
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
        const fontsDiv = productDiv.querySelector('.product-fonts');
        const nameDiv = productDiv.querySelector('.product-name');
        const quantityInput = productDiv.querySelector('.product-quantity');
        const removeBtn = productDiv.querySelector('.remove-product');

        productSelect.addEventListener('change', function() {
            updateMetalOptions(this, metalSelect);
            updateFontDisplay(this, fontsDiv);
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

        // If this is an existing product, populate the fields
        if (existingProduct) {
            // Trigger product selection to populate metals and fonts
            productSelect.dispatchEvent(new Event('change'));
            
            // Set metal after a short delay to ensure metals are loaded
            setTimeout(() => {
                metalSelect.value = existingProduct.metal;
                metalSelect.dispatchEvent(new Event('change'));
                
                // Display names if they exist
                if (existingProduct.names && existingProduct.names.length > 0) {
                    const namesHtml = existingProduct.names.map((name, index) => 
                        `<span class="badge bg-success me-1 fs-6 px-2 py-1">
                            <i class="fas fa-font me-1"></i>${name}
                        </span>`
                    ).join('');
                    nameDiv.innerHTML = `
                        <div class="d-flex align-items-center flex-wrap">
                            <i class="fas fa-signature text-success me-2 fs-5"></i>
                            ${namesHtml}
                        </div>
                    `;
                } else {
                    nameDiv.innerHTML = '<span class="text-muted"><i class="fas fa-minus me-1"></i>No names</span>';
                }
            }, 100);
        }
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

        // Reset price with correct currency
        const priceSpan = productSelect.closest('.product-item').querySelector('.product-price');
        const distributorSelect = document.getElementById('distributor_id');
        const isInternational = distributorSelect ? 
            (distributorSelect.options[distributorSelect.selectedIndex]?.dataset.international === 'true') : 
            false;
        const symbol = isInternational ? '$' : '₱';
        priceSpan.textContent = `${symbol}0.00`;
    }

    function updateFontDisplay(productSelect, fontsDiv) {
        const selectedOption = productSelect.options[productSelect.selectedIndex];
        const hasFonts = selectedOption.dataset.hasFonts === 'true';
        const fonts = JSON.parse(selectedOption.dataset.fonts || '[]');

        // Clear and populate font display
        fontsDiv.innerHTML = '';
        
        if (hasFonts && fonts.length > 0) {
            const fontList = fonts.map(font => `<span class="badge bg-info me-1">${font}</span>`).join('');
            fontsDiv.innerHTML = fontList;
            fontsDiv.parentElement.style.display = '';
        } else {
            fontsDiv.innerHTML = '-';
            fontsDiv.parentElement.style.display = 'none';
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
                    const symbol = isInternational ? '$' : '₱';
                    priceSpan.textContent = `${symbol}0.00`;
                }
            } catch (error) {
                console.error('Error fetching pricing:', error);
                const symbol = isInternational ? '$' : '₱';
                priceSpan.textContent = `${symbol}0.00`;
            }
        } else {
            const symbol = isInternational ? '$' : '₱';
            priceSpan.textContent = `${symbol}0.00`;
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

    // Load existing products
    @foreach($order->products as $product)
        @php
            $names = $product->pivot->names;
            if (is_string($names)) {
                $names = json_decode($names, true);
            }
            $names = is_array($names) ? $names : [];
        @endphp
        addProduct({
            product_id: {{ $product->id }},
            quantity: {{ $product->pivot->quantity }},
            metal: '{{ $product->pivot->metal }}',
            font: '{{ $product->pivot->font ?? "" }}',
            names: @json($names)
        });
    @endforeach

    // Add product button
    addProductBtn.addEventListener('click', () => addProduct());
});
</script>
@endpush
@endsection 