@extends('layouts.app')

@section('title', 'Products')

@section('page-title', 'Products')

@section('content')
<div class="card shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Product Catalog</h5>
        <a href="{{ route('products.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Add Product
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
                    <input type="text" id="productSearch" class="form-control" placeholder="Search products by name, SKU, category...">
                    <button class="btn btn-outline-secondary" type="button" onclick="clearProductSearch()">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            <div class="col-md-6 text-end">
                <span id="productCount" class="text-muted">
                    Showing {{ $products->count() }} of {{ $products->count() }} products
                </span>
            </div>
        </div>

        @if($products->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover" id="productsTable">
                    <thead>
                        <tr>
                            <th>Image</th>
                            <th>SKU</th>
                            <th>Name</th>
                            <th>Category</th>
                            <th>Sub Category</th>
                            <th>Price Range</th>
                            <th>Metals</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($products as $product)
                            <tr class="product-row" 
                                data-name="{{ strtolower($product->name) }}"
                                data-sku="{{ strtolower($product->sku) }}"
                                data-category="{{ strtolower($product->category) }}"
                                data-subcategory="{{ strtolower($product->sub_category) }}">
                                <td>
                                    @if($product->hasImage())
                                        <div class="image-container" style="width: 50px; height: 50px;">
                                            <img src="{{ $product->getImageUrl() }}" 
                                                 alt="{{ $product->name }}" 
                                                 class="product-image" 
                                                 style="width: 100%; height: 100%; object-fit: cover;">
                                            <div class="image-overlay">
                                                <i class="fas fa-eye"></i>
                                            </div>
                                        </div>
                                    @else
                                        <div class="image-placeholder" 
                                             style="width: 50px; height: 50px;">
                                            <i class="fas fa-image"></i>
                                        </div>
                                    @endif
                                </td>
                                <td>{{ $product->sku }}</td>
                                <td>{{ $product->name }}</td>
                                <td>{{ $product->category }}</td>
                                <td>{{ $product->sub_category }}</td>
                                <td>
                                    @if($product->metals && count($product->metals) > 0)
                                        <div class="small">
                                            <div class="text-primary">
                                                <strong>Local:</strong> 
                                                @php
                                                    $localPrices = array_filter($product->local_pricing ?? []);
                                                    if (!empty($localPrices)) {
                                                        $minLocal = min($localPrices);
                                                        $maxLocal = max($localPrices);
                                                        echo '₱' . number_format($minLocal, 2);
                                                        if ($minLocal !== $maxLocal) {
                                                            echo ' - ₱' . number_format($maxLocal, 2);
                                                        }
                                                    } else {
                                                        echo 'Not set';
                                                    }
                                                @endphp
                                            </div>
                                            <div class="text-success">
                                                <strong>International:</strong> 
                                                @php
                                                    $internationalPrices = array_filter($product->international_pricing ?? []);
                                                    if (!empty($internationalPrices)) {
                                                        $minInt = min($internationalPrices);
                                                        $maxInt = max($internationalPrices);
                                                        echo '$' . number_format($minInt, 2);
                                                        if ($minInt !== $maxInt) {
                                                            echo ' - $' . number_format($maxInt, 2);
                                                        }
                                                    } else {
                                                        echo 'Not set';
                                                    }
                                                @endphp
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-muted">No pricing</span>
                                    @endif
                                </td>
                                <td>
                                    @if($product->metals)
                                        <div class="d-flex flex-wrap gap-1">
                                            @foreach(array_slice($product->metals, 0, 2) as $metal)
                                                <span class="badge bg-secondary">{{ $metal }}</span>
                                            @endforeach
                                            @if(count($product->metals) > 2)
                                                <span class="badge bg-secondary">+{{ count($product->metals) - 2 }} more</span>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-muted">No metals</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-{{ $product->is_active ? 'success' : 'danger' }}">
                                        {{ $product->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('products.show', $product) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('products.edit', $product) }}" class="btn btn-sm btn-outline-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('products.destroy', $product) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this product?')">
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
                <i class="fas fa-gem fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No products found</h5>
                <p class="text-muted">Start by adding your first product to the catalog.</p>
                <a href="{{ route('products.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Add First Product
                </a>
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('productSearch');
    const productRows = document.querySelectorAll('.product-row');
    const productCount = document.getElementById('productCount');
    const totalProducts = productRows.length;

    function filterProducts() {
        const searchTerm = searchInput.value.toLowerCase().trim();
        let visibleCount = 0;

        productRows.forEach(row => {
            const name = row.getAttribute('data-name');
            const sku = row.getAttribute('data-sku');
            const category = row.getAttribute('data-category');
            const subcategory = row.getAttribute('data-subcategory');

            const matches = name.includes(searchTerm) || 
                           sku.includes(searchTerm) || 
                           category.includes(searchTerm) || 
                           subcategory.includes(searchTerm);

            if (matches) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });

        // Update count display
        if (searchTerm === '') {
            productCount.textContent = `Showing ${totalProducts} of ${totalProducts} products`;
        } else {
            productCount.textContent = `Showing ${visibleCount} of ${totalProducts} products`;
        }
    }

    // Add event listener for search input
    searchInput.addEventListener('input', filterProducts);

    // Clear search function
    window.clearProductSearch = function() {
        searchInput.value = '';
        filterProducts();
        searchInput.focus();
    };
});
</script>
@endpush
@endsection 