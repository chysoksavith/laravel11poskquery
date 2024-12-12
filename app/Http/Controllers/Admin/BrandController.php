<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    public function index()
    {
        return view('brand.list');
    }
    public function getBrand()
    {
        $brands = Brand::all();
        return response()->json($brands);
    }
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);
        $brand = new Brand();
        $brand->name = $request->name;
        $brand->save();
        return response()->json([
            'success' => 'Brand Created'
        ], 200);
    }
    public function edit($id)
    {
        $brand = Brand::findOrFail($id);
        return response()->json($brand);
    }
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);
        $brand = Brand::findOrFail($id);
        $brand->name = $request->name;
        $brand->save();
        return response()->json([
            'success' => 'Brand Updated'
        ], 200);
    }
    public function destroy($id)
    {
        $brand = Brand::findOrFail($id);
        $brand->delete();
        return response()->json([
            'success' => 'brand delted'
        ], 200);
    }
}
