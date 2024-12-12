<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        $categories = Category::all()->pluck('category_name', 'id');
        $brands = Brand::all()->pluck('name', 'id');
        return view('product.list', compact('categories', 'brands'));
    }
    public function getProduct()
    {
        $products = Product::with('brand', 'category')->get();
        return response()->json($products);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'required|exists:brands,id',
            'product_code' => 'required|string|max:255|unique:products,product_code',
            'product_name' => 'required|string|max:255',
            'product_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'discount' => 'nullable|numeric|min:0|max:100',
            'stock' => 'required|integer|min:0'
        ]);

        try {
            Product::create($validated);

            return response()->json([
                'success' => 'Product created successfully.'
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to create product. Please try again later.'
            ], 500);
        }
    }

    public function edit($id)
    {
        $product = Product::findOrFail($id);
        return response()->json($product);
    }
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'required|exists:brands,id',
            'product_code' => 'required|string|max:255|unique:products,product_code,' . $id,
            'product_name' => 'required|string|max:255',
            'product_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'discount' => 'nullable|numeric|min:0|max:100',
            'stock' => 'required|integer|min:0'
        ]);
        $product = Product::findOrFail($id);
        $product->update($validated);
        return response()->json([
            'success' => "Product updated successfully"
        ], 200);
    }
    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();
        return response()->json([
            'success' => "Product updated successfully"
        ], 200);
    }
}
