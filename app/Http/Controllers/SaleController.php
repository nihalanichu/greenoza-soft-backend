<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Sale;
use App\Models\SaleItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SaleController extends Controller
{
    public function index(Request $request)
    {
        $query = Sale::with('items.item');
        $query = $this->scopeByShop($request, $query);

        if ($request->query('sale_type')) {
            $query->where('sale_type', $request->query('sale_type'));
        }

        return response()->json($query->orderByDesc('created_at')->get());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'customer_name' => 'nullable|string',
            'sale_type' => 'required|in:cash,credit',
            'invoice_no' => 'nullable|string',
            'paid_amount' => 'required|numeric|min:0',
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'required|integer|exists:items,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
        ]);

        $shopId = $request->user()->role === 'admin'
            ? $request->input('shop_id', $request->user()->shop_id)
            : $request->user()->shop_id;

        DB::transaction(function () use ($request, $data, $shopId, &$sale) {
            $total = 0;

            foreach ($data['items'] as $itemData) {
                $item = Item::where('id', $itemData['item_id'])
                    ->where('shop_id', $shopId)
                    ->firstOrFail();

                if ($itemData['quantity'] > $item->quantity) {
                    abort(422, 'Not enough stock available for '.$item->name);
                }

                $subtotal = $itemData['quantity'] * $itemData['price'];
                $total += $subtotal;
            }

            $sale = Sale::create([
                'shop_id' => $shopId,
                'customer_name' => $data['customer_name'] ?? null,
                'sale_type' => $data['sale_type'],
                'invoice_no' => $data['invoice_no'] ?? null,
                'total_amount' => $total,
                'paid_amount' => $data['paid_amount'],
                'balance' => max($total - $data['paid_amount'], 0),
            ]);

            foreach ($data['items'] as $itemData) {
                $item = Item::where('id', $itemData['item_id'])
                    ->where('shop_id', $shopId)
                    ->firstOrFail();
                $item->decrement('quantity', $itemData['quantity']);

                SaleItem::create([
                    'sale_id' => $sale->id,
                    'item_id' => $itemData['item_id'],
                    'quantity' => $itemData['quantity'],
                    'price' => $itemData['price'],
                    'subtotal' => $itemData['quantity'] * $itemData['price'],
                ]);
            }
        });

        return response()->json($sale->load('items.item'), 201);
    }

    public function show(Request $request, Sale $sale)
    {
        $this->authorizeShop($request, $sale);
        return response()->json($sale->load('items.item'));
    }

    public function update(Request $request, Sale $sale)
    {
        $this->authorizeShop($request, $sale);

        $data = $request->validate([
            'customer_name' => 'nullable|string',
            'sale_type' => 'required|in:cash,credit',
            'invoice_no' => 'nullable|string',
            'paid_amount' => 'required|numeric|min:0',
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'required|integer|exists:items,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($sale, $data, $request) {
            foreach ($sale->items as $oldItem) {
                $product = Item::find($oldItem->item_id);
                if ($product) {
                    $product->increment('quantity', $oldItem->quantity);
                }
            }

            $sale->items()->delete();

            $shopId = $request->user()->role === 'admin' ? $request->input('shop_id', $sale->shop_id) : $request->user()->shop_id;
            $total = 0;

            foreach ($data['items'] as $itemData) {
                $item = Item::where('id', $itemData['item_id'])
                    ->where('shop_id', $shopId)
                    ->firstOrFail();

                if ($itemData['quantity'] > $item->quantity) {
                    abort(422, 'Not enough stock available for '.$item->name);
                }

                $subtotal = $itemData['quantity'] * $itemData['price'];
                $total += $subtotal;
            }

            $sale->update([
                'customer_name' => $data['customer_name'] ?? null,
                'sale_type' => $data['sale_type'],
                'invoice_no' => $data['invoice_no'] ?? null,
                'total_amount' => $total,
                'paid_amount' => $data['paid_amount'],
                'balance' => max($total - $data['paid_amount'], 0),
            ]);

            foreach ($data['items'] as $itemData) {
                $item = Item::where('id', $itemData['item_id'])
                    ->where('shop_id', $shopId)
                    ->firstOrFail();
                $item->decrement('quantity', $itemData['quantity']);

                SaleItem::create([
                    'sale_id' => $sale->id,
                    'item_id' => $itemData['item_id'],
                    'quantity' => $itemData['quantity'],
                    'price' => $itemData['price'],
                    'subtotal' => $itemData['quantity'] * $itemData['price'],
                ]);
            }
        });

        return response()->json($sale->load('items.item'));
    }

    public function destroy(Request $request, Sale $sale)
    {
        $this->authorizeShop($request, $sale);

        DB::transaction(function () use ($sale) {
            foreach ($sale->items as $item) {
                $product = Item::find($item->item_id);
                if ($product) {
                    $product->increment('quantity', $item->quantity);
                }
            }

            $sale->delete();
        });

        return response()->json(['message' => 'Sale deleted']);
    }
}
