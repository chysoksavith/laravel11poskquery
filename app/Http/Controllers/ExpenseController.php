<?php

namespace App\Http\Controllers;

use App\Http\Requests\ExpenseRequest;
use App\Models\Expense;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function index()
    {
        return view('expense.list');
    }
    public function geExpense()
    {
        $expenses = Expense::orderBy('created_at', 'desc')->paginate(15);
        return response()->json($expenses);
    }
    public function store(ExpenseRequest $request)
    {
        $validated = $request->validated();
        $expense = Expense::create($validated);
        return response()->json([
            'success' => 'Expense created successfully!',
            'expense' => $expense,
        ], 201);
    }
    public function edit($id)
    {
        $expense = Expense::findOrFail($id);
        return response()->json($expense);
    }
    public function update(ExpenseRequest $request, $id)
    {
        $validated = $request->validated();
        $expense = Expense::findOrFail($id);
        $expense->update($validated);
        return response()->json([
            'success' => 'Expense updated successfully',
            'data' => $expense
        ]);
    }
    public function destroy($id)
    {
        $expense = Expense::findOrFail($id);
        $expense->delete();
        return response()->json([
            'success' => 'Expense deleted successfully',
            'data' => $expense
        ]);
    }
}
