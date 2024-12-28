<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        return view('category.list');
    }

    public function getCategory()
    {
        $categories = Category::all();
        return response()->json(['categories' => $categories]);
    }
    public function store(Request $request)
    {
        $request->validate([
            'category_name' => 'required|string|max:255',
        ]);
        $category = new Category();
        $category->category_name = $request->category_name;
        $category->save();
        return response()->json([
            'success' => 'Category created'
        ]);
    }
    public function edit($id)
    {
        $category = Category::findOrFail($id);
        return response()->json($category);
    }
    public function update(Request $request, $id)
    {
        $request->validate([
            'category_name' => 'required|string|max:255'
        ]);
        $category = Category::findOrFail($id);
        $category->category_name = $request->category_name;
        $category->save();
        return response()->json([
            'success' => 'category updated'
        ]);
    }
    public function destroy($id)
    {
        $category = Category::findOrFail($id);
        $category->delete();
        return response()->json([
            'success' => 'category deleted'
        ]);
    }
    //show
    public function showPos()
    {
        $categories = Category::all(); // Fetch all categories for the sidebar
        return view('pos.view', compact('categories'));
    }
    public function showCategory($slug)
    {
        $category = Category::where('slug', $slug)->with('products')->firstOrFail();
        $categories = Category::all();
        return view('pos.category', compact('categories', 'category'));
    }
}
