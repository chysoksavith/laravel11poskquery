<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\Sale;
use App\Models\SaleDetail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class SaleController extends Controller
{
    public function index()
    {
        $members = Member::select('id', 'name_member')->get();
        $users = User::select('id', 'name')->get();
        return view('sale.list', compact('members', 'users'));
    }
    public function getSales(Request $request)
    {
        $query = Sale::with(['member', 'user']);

        // Apply search filter if provided
        if ($request->has('search') && $request->search !== null) {
            $query->whereHas('member', function ($q) use ($request) {
                $q->where('name_member', 'LIKE', '%' . $request->search . '%');
            })->orWhereHas('user', function ($q) use ($request) {
                $q->where('name', 'LIKE', '%' . $request->search . '%');
            });
        }
        // Default pagination size (can be made configurable via request)
        $perPage = $request->get('per_page', 10);
        // Fetch paginated results
        $sales = $query->orderBy('created_at', 'desc')->paginate($perPage);
        // Return JSON response
        return response()->json($sales);
    }
    public function store(Request $request)
    {
        $validated = $request->validate([
            'member_id' => ['required', 'exists:members,id'],
            'user_id' => ['required', 'exists:users,id'],
            'accepted' => ['required', 'in:yes,no'],
            'total_item' => ['required', 'integer'],
            'total_price' => ['required', 'numeric'],
            'discount' => ['nullable', 'numeric', 'min:0', 'max:100']
        ]);
        if (!is_null($validated['discount'])) {
            $validated['total_price'] = $validated['total_price'] - ($validated['total_price'] * ($validated['discount'] / 100));
        }
        $sale = Sale::create($validated);
        return response()->json([
            'success' => "Sale created successfully",
            'data' => $sale
        ]);
    }
    public function edit($id)
    {
        $sale = Sale::findOrFail($id);
        return response()->json($sale);
    }
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'member_id' => ['required', 'exists:members,id'],
            'user_id' => ['required', 'exists:users,id'],
            'accepted' => ['required', 'in:yes,no'],
            'total_item' => ['required', 'integer'],
            'total_price' => ['required', 'numeric'],
            'discount' => ['nullable', 'numeric', 'min:0', 'max:100']
        ]);

        $sale = Sale::findOrFail($id);

        if (!is_null($validated['discount'])) {
            $validated['total_price'] = $validated['total_price'] - ($validated['total_price'] * ($validated['discount'] / 100));
        }

        $sale->update($validated);

        return response()->json([
            'success' => "Sale updated successfully",
            'data' => $sale
        ]);
    }
    public function destroy($id)
    {
        $sale = Sale::findOrFail($id);
        $sale->delete();
        return response()->json([
            'success' => "Sale updated successfully",
        ]);
    }
    // sale Detail
    public function saleDetailList($id)
    {
        $data['getRecord'] = SaleDetail::select('sale_details.*', 'products.product_name')
            ->join('products', 'products.id', '=', 'sale_details.product_id')
            ->where('sale_details.sale_id', '=', $id)->paginate(1);
        return view('sale.sales_detail_list', $data);
    }
}
