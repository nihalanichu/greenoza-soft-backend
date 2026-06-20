<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $query = Expense::query();
        $query = $this->scopeByShop($request, $query);

        if ($request->query('category')) {
            $query->where('category', $request->query('category'));
        }

        return response()->json($query->orderByDesc('expense_date')->get());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'category' => 'required|string',
            'title' => 'required|string',
            'amount' => 'required|numeric|min:0',
            'expense_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        $data['shop_id'] = $request->user()->role === 'admin'
            ? $request->input('shop_id', $request->user()->shop_id)
            : $request->user()->shop_id;

        return response()->json(Expense::create($data), 201);
    }

    public function show(Request $request, Expense $expense)
    {
        $this->authorizeShop($request, $expense);
        return response()->json($expense);
    }

    public function update(Request $request, Expense $expense)
    {
        $this->authorizeShop($request, $expense);

        $data = $request->validate([
            'category' => 'required|string',
            'title' => 'required|string',
            'amount' => 'required|numeric|min:0',
            'expense_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        $expense->update($data);

        return response()->json($expense);
    }

    public function destroy(Request $request, Expense $expense)
    {
        $this->authorizeShop($request, $expense);
        $expense->delete();

        return response()->json(['message' => 'Expense removed']);
    }
}
