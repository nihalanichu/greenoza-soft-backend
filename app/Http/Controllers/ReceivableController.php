<?php

namespace App\Http\Controllers;

use App\Models\Receivable;
use Illuminate\Http\Request;

class ReceivableController extends Controller
{
    public function index(Request $request)
    {
        $query = Receivable::with('sale');
        $query = $this->scopeByShop($request, $query);

        return response()->json($query->orderByDesc('created_at')->get());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'sale_id' => 'required|exists:sales,id',
            'customer_name' => 'required|string',
            'amount_due' => 'required|numeric|min:0',
            'due_date' => 'nullable|date',
            'status' => 'required|in:pending,paid',
        ]);

        $data['shop_id'] = $request->user()->role === 'admin'
            ? $request->input('shop_id', $request->user()->shop_id)
            : $request->user()->shop_id;

        return response()->json(Receivable::create($data), 201);
    }

    public function show(Request $request, Receivable $receivable)
    {
        $this->authorizeShop($request, $receivable);
        return response()->json($receivable);
    }

    public function update(Request $request, Receivable $receivable)
    {
        $this->authorizeShop($request, $receivable);

        $data = $request->validate([
            'customer_name' => 'required|string',
            'amount_due' => 'required|numeric|min:0',
            'due_date' => 'nullable|date',
            'status' => 'required|in:pending,paid',
        ]);

        $receivable->update($data);

        return response()->json($receivable);
    }

    public function destroy(Request $request, Receivable $receivable)
    {
        $this->authorizeShop($request, $receivable);
        $receivable->delete();

        return response()->json(['message' => 'Receivable removed']);
    }
}
