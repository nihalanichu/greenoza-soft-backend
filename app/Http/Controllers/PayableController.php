<?php

namespace App\Http\Controllers;

use App\Models\Payable;
use Illuminate\Http\Request;

class PayableController extends Controller
{
    public function index(Request $request)
    {
        $query = Payable::with('purchase');
        $query = $this->scopeByShop($request, $query);

        return response()->json($query->orderByDesc('created_at')->get());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'purchase_id' => 'required|exists:purchases,id',
            'supplier_name' => 'required|string',
            'amount_due' => 'required|numeric|min:0',
            'due_date' => 'nullable|date',
            'status' => 'required|in:pending,paid',
        ]);

        $data['shop_id'] = $request->user()->role === 'admin'
            ? $request->input('shop_id', $request->user()->shop_id)
            : $request->user()->shop_id;

        return response()->json(Payable::create($data), 201);
    }

    public function show(Request $request, Payable $payable)
    {
        $this->authorizeShop($request, $payable);
        return response()->json($payable);
    }

    public function update(Request $request, Payable $payable)
    {
        $this->authorizeShop($request, $payable);

        $data = $request->validate([
            'supplier_name' => 'required|string',
            'amount_due' => 'required|numeric|min:0',
            'due_date' => 'nullable|date',
            'status' => 'required|in:pending,paid',
        ]);

        $payable->update($data);

        return response()->json($payable);
    }

    public function destroy(Request $request, Payable $payable)
    {
        $this->authorizeShop($request, $payable);
        $payable->delete();

        return response()->json(['message' => 'Payable removed']);
    }
}
