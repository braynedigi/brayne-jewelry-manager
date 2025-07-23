<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProductCategory;
use App\Models\ProductMetal;
use App\Models\ProductStone;
use App\Models\ProductFont;
use App\Models\RingSize;

class ProductCategoryController extends Controller
{
    /**
     * Show the product categories management page
     */
    public function index()
    {
        $parentCategories = ProductCategory::parents()->with('children')->ordered()->get();
        $allCategories = ProductCategory::with('parent')->ordered()->get();
        $metals = ProductMetal::ordered()->get();
        $stones = ProductStone::ordered()->get();
        $fonts = ProductFont::ordered()->get();
        $ringSizes = RingSize::ordered()->get();

        return view('products.categories.index', compact('parentCategories', 'allCategories', 'metals', 'stones', 'fonts', 'ringSizes'));
    }

    // Product Categories
    public function storeCategory(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:product_categories,name',
            'description' => 'nullable|string|max:1000',
            'is_active' => 'nullable|boolean',
            'parent_id' => 'nullable|exists:product_categories,id',
        ]);

        $validated['is_active'] = $request->has('is_active');
        $validated['sort_order'] = ProductCategory::max('sort_order') + 1;

        ProductCategory::create($validated);

        return response()->json(['success' => true, 'message' => 'Category created successfully!']);
    }

    public function updateCategory(Request $request, ProductCategory $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:product_categories,name,' . $category->id,
            'description' => 'nullable|string|max:1000',
            'is_active' => 'nullable|boolean',
            'parent_id' => 'nullable|exists:product_categories,id',
        ]);

        // Prevent circular references (category cannot be its own parent)
        if ($validated['parent_id'] == $category->id) {
            return response()->json(['success' => false, 'message' => 'A category cannot be its own parent!']);
        }

        // Prevent setting a child as parent (would create circular reference)
        if ($validated['parent_id'] && $category->descendants()->where('id', $validated['parent_id'])->exists()) {
            return response()->json(['success' => false, 'message' => 'Cannot set a subcategory as parent!']);
        }

        $validated['is_active'] = $request->has('is_active');

        $category->update($validated);

        return response()->json(['success' => true, 'message' => 'Category updated successfully!']);
    }

    public function destroyCategory(ProductCategory $category)
    {
        if ($category->products()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete category with associated products. Please reassign products first.'
            ]);
        }

        // Check if category has children
        if ($category->hasChildren()) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete category with subcategories. Please delete subcategories first.'
            ]);
        }

        $category->delete();

        return response()->json(['success' => true, 'message' => 'Category deleted successfully!']);
    }

    // Product Metals
    public function storeMetal(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:product_metals,name',
            'description' => 'nullable|string|max:1000',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');
        $validated['sort_order'] = ProductMetal::max('sort_order') + 1;

        ProductMetal::create($validated);

        return response()->json(['success' => true, 'message' => 'Metal created successfully!']);
    }

    public function updateMetal(Request $request, ProductMetal $metal)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:product_metals,name,' . $metal->id,
            'description' => 'nullable|string|max:1000',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $metal->update($validated);

        return response()->json(['success' => true, 'message' => 'Metal updated successfully!']);
    }

    public function destroyMetal(ProductMetal $metal)
    {
        $metal->delete();

        return response()->json(['success' => true, 'message' => 'Metal deleted successfully!']);
    }

    // Product Stones
    public function storeStone(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:product_stones,name',
            'description' => 'nullable|string|max:1000',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');
        $validated['sort_order'] = ProductStone::max('sort_order') + 1;

        ProductStone::create($validated);

        return response()->json(['success' => true, 'message' => 'Stone created successfully!']);
    }

    public function updateStone(Request $request, ProductStone $stone)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:product_stones,name,' . $stone->id,
            'description' => 'nullable|string|max:1000',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $stone->update($validated);

        return response()->json(['success' => true, 'message' => 'Stone updated successfully!']);
    }

    public function destroyStone(ProductStone $stone)
    {
        $stone->delete();

        return response()->json(['success' => true, 'message' => 'Stone deleted successfully!']);
    }

    // Product Fonts
    public function storeFont(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:product_fonts,name',
            'description' => 'nullable|string|max:1000',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');
        $validated['sort_order'] = ProductFont::max('sort_order') + 1;

        ProductFont::create($validated);

        return response()->json(['success' => true, 'message' => 'Font created successfully!']);
    }

    public function updateFont(Request $request, ProductFont $font)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:product_fonts,name,' . $font->id,
            'description' => 'nullable|string|max:1000',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $font->update($validated);

        return response()->json(['success' => true, 'message' => 'Font updated successfully!']);
    }

    public function destroyFont(ProductFont $font)
    {
        $font->delete();

        return response()->json(['success' => true, 'message' => 'Font deleted successfully!']);
    }

    // Ring Sizes
    public function storeRingSize(Request $request)
    {
        $validated = $request->validate([
            'size' => 'required|string|max:50|unique:ring_sizes,size',
            'description' => 'nullable|string|max:1000',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');
        $validated['sort_order'] = RingSize::max('sort_order') + 1;

        RingSize::create($validated);

        return response()->json(['success' => true, 'message' => 'Ring size created successfully!']);
    }

    public function updateRingSize(Request $request, RingSize $ringSize)
    {
        $validated = $request->validate([
            'size' => 'required|string|max:50|unique:ring_sizes,size,' . $ringSize->id,
            'description' => 'nullable|string|max:1000',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $ringSize->update($validated);

        return response()->json(['success' => true, 'message' => 'Ring size updated successfully!']);
    }

    public function destroyRingSize(RingSize $ringSize)
    {
        $ringSize->delete();

        return response()->json(['success' => true, 'message' => 'Ring size deleted successfully!']);
    }

    // API endpoints for getting active options
    public function getActiveCategories()
    {
        $categories = ProductCategory::active()->with('children')->parents()->ordered()->get();
        return response()->json($categories);
    }

    public function getActiveMetals()
    {
        return response()->json(ProductMetal::active()->ordered()->get());
    }

    public function getActiveStones()
    {
        return response()->json(ProductStone::active()->ordered()->get());
    }

    public function getActiveFonts()
    {
        return response()->json(ProductFont::active()->ordered()->get());
    }

    public function getActiveRingSizes()
    {
        return response()->json(RingSize::active()->ordered()->get());
    }
}
