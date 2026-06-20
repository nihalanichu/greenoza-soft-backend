<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        $query = Item::with('shop');
        $query = $this->scopeByShop($request, $query);

        return response()->json($query->orderBy('name')->get());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'quantity' => 'required|integer|min:0',
            'buy_price' => 'required|numeric|min:0',
            'sell_price' => 'required|numeric|min:0',
            'unit' => 'nullable|string',
            'shop_id' => 'nullable|exists:shops,id',
        ]);

        $data['shop_id'] = $request->user()->role === 'admin'
            ? $data['shop_id'] ?? $request->user()->shop_id
            : $request->user()->shop_id;

        return response()->json(Item::create($data), 201);
    }

    public function show(Request $request, Item $item)
    {
        $this->authorizeShop($request, $item);
        return response()->json($item);
    }

    public function update(Request $request, Item $item)
    {
        $this->authorizeShop($request, $item);

        $data = $request->validate([
            'name' => 'required|string',
            'quantity' => 'required|integer|min:0',
            'buy_price' => 'required|numeric|min:0',
            'sell_price' => 'required|numeric|min:0',
            'unit' => 'nullable|string',
        ]);

        $item->update($data);

        return response()->json($item);
    }

    public function destroy(Request $request, Item $item)
    {
        $this->authorizeShop($request, $item);
        $item->delete();

        return response()->json(['message' => 'Item deleted']);
    }
}
