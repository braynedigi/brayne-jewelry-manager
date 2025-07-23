<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Product;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::latest()->get();
        return view('products.index', compact('products'));
    }

    public function create()
    {
        $categories = Product::getCategories();
        $metals = Product::getMetals();
        $fonts = \App\Models\ProductFont::active()->ordered()->get();
        
        return view('products.create', compact('categories', 'metals', 'fonts'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'sku' => 'required|string|max:255|unique:products,sku',
            'category' => 'required|string|max:255',
            'sub_category' => 'required|string|max:255',
            'custom_sub_category' => 'nullable|string|max:255',
            'metals' => 'required|array|min:1',
            'metals.*' => 'in:' . implode(',', Product::getMetals()),
            'fonts' => 'nullable|array',
            'font_requirement' => 'nullable|integer|min:0|max:10',
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
            $validated['image'] = $imagePath;
        }

        // Handle custom sub category
        if ($validated['sub_category'] === 'Others' && $request->filled('custom_sub_category')) {
            $validated['sub_category'] = $request->input('custom_sub_category');
        }

        // Handle local pricing
        $localPricing = [];
        foreach ($validated['metals'] as $metal) {
            $priceKey = 'local_price_' . str_replace(' ', '_', strtolower($metal));
            if ($request->filled($priceKey)) {
                $localPricing[$metal] = (float) $request->input($priceKey);
            }
        }
        $validated['local_pricing'] = $localPricing;

        // Handle international pricing
        $internationalPricing = [];
        foreach ($validated['metals'] as $metal) {
            $priceKey = 'international_price_' . str_replace(' ', '_', strtolower($metal));
            if ($request->filled($priceKey)) {
                $internationalPricing[$metal] = (float) $request->input($priceKey);
            }
        }
        $validated['international_pricing'] = $internationalPricing;

        // Handle fonts
        if ($request->filled('fonts')) {
            $fonts = $request->input('fonts');
            if (is_array($fonts)) {
                // Filter out empty values and trim
                $validated['fonts'] = array_filter(array_map('trim', $fonts));
            } else {
                // Handle as string (fallback)
                $fonts = array_filter(explode(',', $fonts));
                $validated['fonts'] = array_map('trim', $fonts);
            }
        }

        // Handle font requirement
        $validated['font_requirement'] = $request->input('font_requirement', 0);

        $validated['is_active'] = $request->has('is_active');

        Product::create($validated);

        return redirect()->route('products.index')->with('success', 'Product created successfully!');
    }

    public function show(Product $product)
    {
        return view('products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        $categories = Product::getCategories();
        $subCategories = Product::getSubCategories($product->category);
        $metals = Product::getMetals();
        $fonts = \App\Models\ProductFont::active()->ordered()->get();
        
        return view('products.edit', compact('product', 'categories', 'subCategories', 'metals', 'fonts'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'sku' => 'required|string|max:255|unique:products,sku,' . $product->id,
            'category' => 'required|in:Necklaces,Rings',
            'sub_category' => 'required|string|max:255',
            'custom_sub_category' => 'nullable|string|max:255',
            'metals' => 'required|array|min:1',
            'metals.*' => 'in:' . implode(',', Product::getMetals()),
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $imagePath = $request->file('image')->store('products', 'public');
            $validated['image'] = $imagePath;
        }

        // Handle custom sub category
        if ($validated['sub_category'] === 'Others' && $request->filled('custom_sub_category')) {
            $validated['sub_category'] = $request->input('custom_sub_category');
        }

        // Handle local pricing
        $localPricing = [];
        foreach ($validated['metals'] as $metal) {
            $priceKey = 'local_price_' . str_replace(' ', '_', strtolower($metal));
            if ($request->filled($priceKey)) {
                $localPricing[$metal] = (float) $request->input($priceKey);
            }
        }
        $validated['local_pricing'] = $localPricing;

        // Handle international pricing
        $internationalPricing = [];
        foreach ($validated['metals'] as $metal) {
            $priceKey = 'international_price_' . str_replace(' ', '_', strtolower($metal));
            if ($request->filled($priceKey)) {
                $internationalPricing[$metal] = (float) $request->input($priceKey);
            }
        }
        $validated['international_pricing'] = $internationalPricing;

        // Handle fonts
        if ($request->filled('fonts')) {
            $fonts = $request->input('fonts');
            if (is_array($fonts)) {
                // Filter out empty values and trim
                $validated['fonts'] = array_filter(array_map('trim', $fonts));
            } else {
                // Handle as string (fallback)
                $fonts = array_filter(explode(',', $fonts));
                $validated['fonts'] = array_map('trim', $fonts);
            }
        }

        $validated['is_active'] = $request->has('is_active');

        $product->update($validated);

        return redirect()->route('products.index')->with('success', 'Product updated successfully!');
    }

    public function destroy(Product $product)
    {
        // Delete image if exists
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();

        return redirect()->route('products.index')->with('success', 'Product deleted successfully!');
    }

    public function getPricing(Product $product)
    {
        return response()->json([
            'local_pricing' => $product->local_pricing,
            'international_pricing' => $product->international_pricing,
            'fonts' => $product->fonts,
            'has_fonts' => $product->hasFonts()
        ]);
    }

    public function getSubCategories(Request $request)
    {
        $category = $request->input('category');
        $subCategories = Product::getSubCategories($category);
        
        return response()->json($subCategories);
    }
}
