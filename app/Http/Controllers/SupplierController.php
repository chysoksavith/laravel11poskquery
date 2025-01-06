<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Supply;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index(Request $request)
    {
        return view('supply.list',);
    }
    public function getSupplier(Request $request)
    {
        $query = Supply::query();

        if ($request->has('search') && $request->search !== '') {
            $query->where(function ($query) use ($request) {
                $query->where('supplier_name', 'like', '%' . $request->search . '%')
                    ->orWhere('supplier_telephone', 'like', '%' . $request->search . '%');
            });
        }

        $suppliers = $query->orderBy('created_at', 'desc')->paginate(1);
        return response()->json($suppliers);
    }
    public function store(Request $request)
    {
        $validated = $request->validate([
            'supplier_name' => 'required|string|max:255',
            'supplier_telephone' => 'required|string|regex:/^[0-9\-\+\(\)\s]*$/|max:15',
            'supplier_address' => 'required|string|max:255',
        ]);

        $supply = Supply::create($validated);

        return response()->json([
            'success' => 'Supply added successfully',
            'data' => $supply,
        ]);
    }
    public function edit($id)
    {
        $supply = Supply::findOrFail($id);
        return response()->json($supply);
    }
    public function update(Request $request, $id)
    {
        $supply = Supply::findOrFail($id);
        $validated = $request->validate([
            'supplier_name' => 'required|string|max:255',
            'supplier_telephone' => 'required|string|regex:/^[0-9\-\+\(\)\s]*$/|max:15',
            'supplier_address' => 'required|string|max:255',
        ]);

        $supply->update($validated);
        return response()->json([
            'success' => 'Supply updated successfully',
            'data' => $supply,
        ]);
    }

    public function destroy($id)
    {
        $supply = Supply::findOrFail($id);
        $supply->delete();
        return response()->json([
            'success' => 'Supply deleted successfully',
            'data' => $supply,
        ]);
    }
}
