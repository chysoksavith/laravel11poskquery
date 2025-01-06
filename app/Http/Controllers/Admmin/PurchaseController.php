<?php

namespace App\Http\Controllers\Admmin;

use App\Http\Controllers\Controller;
use App\Models\Purchase;
use App\Models\Supply;
use Illuminate\Http\Request;

class PurchaseController extends Controller
{

    public function index()
    {
        $supplier = Supply::all();
        return view('purchase.list', compact('supplier'));
    }
    public function getPurchase(Request $request)
    {
        $query = Purchase::with('supplier');

        if ($request->has('search') && $request->search !== null) {
            $query->whereHas('supplier', function ($q) use ($request) {
                $q->where('supplier_name', 'like', '%' . $request->search . '%');
            });
        }

        $purchases = $query->orderBy('created_at', 'desc')->paginate(1);
        return response()->json($purchases);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'supplier_id' => 'required|exists:supplier,id',
            'total_item' => 'required|integer',
            'total_price' => 'required|numeric',
            'discount' => 'nullable|integer',
        ]);

        $purchase = Purchase::create([
            'supplier_id' => $validatedData['supplier_id'],
            'total_item' => $validatedData['total_item'],
            'total_price' => $validatedData['total_price'],
            'discount' => $validatedData['discount'] ?? 0,
        ]);

        return response()->json([
            'success' => "Purchase created successfully",
            'data' => $purchase
        ]);
    }
    public function edit($id)
    {
        try {
            $supplier = Purchase::findOrFail($id);
            return response()->json($supplier);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['error' => 'Purchase not found'], 404);
        }
    }

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'supplier_id' => 'required|exists:supplier,id',
            'total_item' => 'required|integer',
            'total_price' => 'required|numeric',
            'discount' => 'nullable|integer',
        ]);
        $purchase = Purchase::findOrFail($id);
        $purchase->update([
            'supplier_id' => $validatedData['supplier_id'],
            'total_item' => $validatedData['total_item'],
            'total_price' => $validatedData['total_price'],
            'discount' => $validatedData['discount'] ?? 0,
        ]);
        return response()->json([
            'success' => "Purchase updated successfully"
        ]);
    }
    public function destroy($id)
    {
        $purchase = Purchase::findOrFail($id);
        $purchase->delete();
        return response()->json([
            'success' => "Purchase deleted successfully"
        ]);
    }
}
