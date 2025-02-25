<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index()
    {
        $categories = Category::all()->pluck('category_name', 'id');
        $brands = Brand::all()->pluck('name', 'id');
        return view('product.list', compact('categories', 'brands'));
    }

    public function getProduct(Request $request)
    {
        $query = Product::with('brand', 'category');

        if ($request->has('search') && $request->search !== '') {
            $query->where('product_name', 'like', '%' . $request->search . '%')
                ->orWhere('product_code', 'like', '%' . $request->search . '%');
        }
        $products = $query->orderBy('created_at', 'desc')->paginate(15);

        return response()->json($products);
    }
    public function generateProductCode()
    {
        $lastProduct = Product::orderBy('id', 'desc')->first();
        $lastProductCode = $lastProduct ? $lastProduct->product_code : null;

        // Generate new product code (this is an example, customize it as needed)
        $newProductCode = $lastProductCode ? (intval(substr($lastProductCode, -4)) + 1) : 1001;
        $newProductCode = 'P' . str_pad($newProductCode, 4, '0', STR_PAD_LEFT);

        return response()->json(['product_code' => $newProductCode]);
    }

    public function store(Request $request)
    {
        // Validate the request data, including the image
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'required|exists:brands,id',
            'product_code' => 'required|string|max:255|unique:products,product_code',
            'product_name' => 'required|string|max:255',
            'product_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'discount' => 'nullable|numeric|min:0|max:100',
            'stock' => 'required|integer|min:0',
            'image' => 'required|image|mimes:jpg,png,jpeg,gif|max:2048',
        ]);

        try {
            $imagePath = null;

            // Check if there's an image uploaded and store it
            if ($request->hasFile('image')) {
                // Store the image and get the URL path in public storage
                $imagePath = $request->file('image')->store('images', 'public');
            }

            // Create the product record and save the image path
            $product = Product::create([
                'category_id' => $validated['category_id'],
                'brand_id' => $validated['brand_id'],
                'product_code' => $validated['product_code'],
                'product_name' => $validated['product_name'],
                'product_price' => $validated['product_price'],
                'selling_price' => $validated['selling_price'],
                'discount' => $validated['discount'],
                'stock' => $validated['stock'],
                'image' => $imagePath,  // Save the image path if it exists
            ]);

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
            'stock' => 'required|integer|min:0',
            'image' => 'nullable|image|mimes:jpg,png,jpeg,gif|max:2048',
        ]);

        try {
            $product = Product::findOrFail($id);
            $imagePath = $product->image; // Keep the old image path

            // Check if a new image is uploaded
            if ($request->hasFile('image')) {
                // Ensure the old image is deleted if it exists
                if ($product->image && Storage::disk('public')->exists($product->image)) {
                    Log::info("Deleting old image: " . $product->image);
                    Storage::disk('public')->delete($product->image);
                }

                // Store the new image
                $imagePath = $request->file('image')->store('images', 'public');
            }

            // Update the product with new data and possibly the new image
            $product->update([
                'category_id' => $validated['category_id'],
                'brand_id' => $validated['brand_id'],
                'product_code' => $validated['product_code'],
                'product_name' => $validated['product_name'],
                'product_price' => $validated['product_price'],
                'selling_price' => $validated['selling_price'],
                'discount' => $validated['discount'],
                'stock' => $validated['stock'],
                'image' => $imagePath, // Keep old image if no new image
            ]);

            return response()->json([
                'success' => 'Product updated successfully.'
            ], 200);
        } catch (\Exception $e) {
            Log::error("Error updating product: " . $e->getMessage());
            return response()->json([
                'error' => 'Failed to update product. Please try again later.'
            ], 500);
        }
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);

        // Check if there is an image and delete it
        if ($product->image) {
            $imagePath = $product->image; // Store the relative path to the image

            // Check if the image exists in storage
            if (Storage::disk('public')->exists($imagePath)) {
                Log::info('Deleting image: ' . $imagePath);
                Storage::disk('public')->delete($imagePath);
            } else {
                Log::warning('Image not found or does not exist: ' . $imagePath);
            }
        }

        // Delete the product record
        $product->delete();

        return response()->json([
            'success' => "Product deleted successfully"
        ], 200);
    }

    public function updateStatus(Request $request)
    {
        if ($request->ajax()) {
            $data = $request->all();

            // Ensure you're working with the correct status (0 = inactive, 1 = active)
            $status = $data['status'] == 1 ? 1 : 0;

            // Update the product status in the database
            Product::where('id', $data['product_id'])->update(['status' => $status]);

            // Return the updated status to the frontend
            return response()->json(['status' => $status, 'product_id' => $data['product_id']]);
        }
    }
}
