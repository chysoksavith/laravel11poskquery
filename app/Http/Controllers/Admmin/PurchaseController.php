<?php

namespace App\Http\Controllers\Admmin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseDetail;
use App\Models\Supply;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

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

        $purchases = $query->orderBy('created_at', 'desc')->paginate(15);
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
    public function purchase_detail($id)
    {
        $data['purchase_id'] = $id;
        $data['getRecord'] = PurchaseDetail::with('product', 'purchase')->where('purchase_details.purchase_id', '=', $id)->get();
        return view('purchase.detail', $data);
    }
    public function purchase_detail_add($id)
    {
        $data['purchase_id'] = $id;
        $data['getProducts'] = Product::get();
        return view('purchase.detail_add', $data);
    }
    public function purchase_detail_add_insert(Request $request)
    {
        $validated = $request->validate([
            'purchase_id'   => 'required|integer|exists:purchases,id',
            'product_id'    => 'required|integer|exists:products,id',
            'purchase_price' => 'required|numeric|min:0',
            'amount'        => 'required|integer|min:1',
            'subtotal'      => 'required|numeric|min:0',
        ]);

        try {
            PurchaseDetail::create($validated);
            return redirect('admin/purchase/detail/' . $validated['purchase_id'])
                ->with('success', 'Record successfully created.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }
}
