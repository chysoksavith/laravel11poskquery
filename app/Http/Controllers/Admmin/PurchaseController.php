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
        PurchaseDetail::where('purchase_details.purchase_id', '=', $id)->delete();
        return response()->json([
            'success' => "Purchase deleted successfully"
        ]);
    }
    public function purchase_detail(Request $request, $id)
    {
        $data['purchase_id'] = $id;

        // Fetch records with product name
        $getRecord = PurchaseDetail::select('purchase_details.*', 'products.product_name')
            ->join('products', 'products.id', '=', 'purchase_details.product_id')
            ->where('purchase_details.purchase_id', $id);

        // Check if any filters are applied
        $hasFilter = false;

        if ($request->filled('id')) {
            $getRecord->where('purchase_details.id', $request->id);
            $hasFilter = true;
        }

        if ($request->filled('product_id')) {
            $getRecord->where('products.product_name', 'like', '%' . $request->product_id . '%');
            $hasFilter = true;
        }

        if ($request->filled('purchase_price')) {
            $getRecord->where('purchase_details.purchase_price', $request->purchase_price);
            $hasFilter = true;
        }

        if ($request->filled('amount')) {
            $getRecord->where('purchase_details.amount', $request->amount);
            $hasFilter = true;
        }

        if ($request->filled('subtotal')) {
            $getRecord->where('purchase_details.subtotal', $request->subtotal);
            $hasFilter = true;
        }

        if ($request->filled('created_at')) {
            $getRecord->whereDate('purchase_details.created_at', $request->created_at);
            $hasFilter = true;
        }

        if ($request->filled('updated_at')) {
            $getRecord->whereDate('purchase_details.updated_at', $request->updated_at);
            $hasFilter = true;
        }

        // If no filters, retrieve all records for the given purchase_id
        if (!$hasFilter) {
            $getRecord->where('purchase_details.purchase_id', $id);
        }

        // Debugging output (optional: remove this after testing)
        // dd($getRecord->toSql(), $getRecord->getBindings());

        // Paginate results
        $data['getRecord'] = $getRecord->paginate(10);

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
    public function purchaseDetailEdit($id)
    {
        $data['getProduct'] = Product::get();
        $data['getRecord'] = PurchaseDetail::findOrFail($id);
        return view('purchase.detail_edit', $data);
    }
    public function purchaseDetailUpdate(Request $request, $id)
    {
        $validated = $request->validate([
            'purchase_id'   => 'required|integer|exists:purchases,id',
            'product_id'    => 'required|integer|exists:products,id',
            'purchase_price' => 'required|numeric|min:0',
            'amount'        => 'required|numeric|min:0',
            'subtotal'      => 'required|numeric|min:0',
        ]);

        try {
            $purchaseDetail = PurchaseDetail::findOrFail($id);

            $purchaseDetail->update($validated);
            return redirect('admin/purchase/detail/' . $validated['purchase_id'])
                ->with('success', 'Record successfully updated.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }
    public function purchaseDetailDelete($id)
    {
        $purchaseDetail = PurchaseDetail::findOrFail($id);
        $purchaseDetail->delete();
        return redirect()->back()->with('success', 'record delete success');
    }
}
