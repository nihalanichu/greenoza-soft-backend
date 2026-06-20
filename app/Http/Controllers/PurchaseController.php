<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseController extends Controller
{
    public function index(Request $request)
    {
        $query = Purchase::with('items.item');
        $query = $this->scopeByShop($request, $query);

        if ($request->query('purchase_type')) {
            $query->where('purchase_type', $request->query('purchase_type'));
        }

        return response()->json($query->orderByDesc('created_at')->get());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'supplier_name' => 'required|string',
            'purchase_type' => 'required|in:cash,credit',
            'invoice_no' => 'nullable|string',
            'paid_amount' => 'required|numeric|min:0',
            'shop_id' => 'nullable|integer|exists:shops,id',
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'required|integer|exists:items,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
        ]);

        $shopId = $request->user()->role === 'admin'
            ? $data['shop_id'] ?? null
            : $request->user()->shop_id;

        if ($request->user()->role === 'admin' && ! $shopId) {
            abort(422, 'Admin users must select a shop before creating a purchase.');
        }

        DB::transaction(function () use ($data, $shopId, &$purchase) {
            $total = 0;

            foreach ($data['items'] as $itemData) {
                $subtotal = $itemData['quantity'] * $itemData['price'];
                $total += $subtotal;
            }

            $purchase = Purchase::create([
                'shop_id' => $shopId,
                'supplier_name' => $data['supplier_name'],
                'purchase_type' => $data['purchase_type'],
                'invoice_no' => $data['invoice_no'] ?? null,
                'total_amount' => $total,
                'paid_amount' => $data['paid_amount'],
                'balance' => max($total - $data['paid_amount'], 0),
            ]);

            foreach ($data['items'] as $itemData) {
                $item = Item::where('id', $itemData['item_id'])
                    ->where('shop_id', $shopId)
                    ->firstOrFail();

                $item->increment('quantity', $itemData['quantity']);

                PurchaseItem::create([
                    'purchase_id' => $purchase->id,
                    'item_id' => $itemData['item_id'],
                    'quantity' => $itemData['quantity'],
                    'price' => $itemData['price'],
                    'subtotal' => $itemData['quantity'] * $itemData['price'],
                ]);
            }
        });

        return response()->json($purchase->load('items.item'), 201);
    }

    public function show(Request $request, Purchase $purchase)
    {
        $this->authorizeShop($request, $purchase);
        return response()->json($purchase->load('items.item'));
    }

    public function update(Request $request, Purchase $purchase)
    {
        $this->authorizeShop($request, $purchase);

        $data = $request->validate([
            'supplier_name' => 'required|string',
            'purchase_type' => 'required|in:cash,credit',
            'invoice_no' => 'nullable|string',
            'paid_amount' => 'required|numeric|min:0',
            'shop_id' => 'nullable|integer|exists:shops,id',
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'required|integer|exists:items,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($request, $purchase, $data) {
            foreach ($purchase->items as $oldItem) {
                $product = Item::find($oldItem->item_id);
                if ($product) {
                    $product->decrement('quantity', $oldItem->quantity);
                }
            }

            $purchase->items()->delete();

            $total = 0;
            $shopId = $request->user()->role === 'admin'
                ? $request->input('shop_id', $purchase->shop_id)
                : $request->user()->shop_id;

            foreach ($data['items'] as $itemData) {
                $subtotal = $itemData['quantity'] * $itemData['price'];
                $total += $subtotal;

                $item = Item::where('id', $itemData['item_id'])
                    ->where('shop_id', $shopId)
                    ->firstOrFail();

                $item->increment('quantity', $itemData['quantity']);

                PurchaseItem::create([
                    'purchase_id' => $purchase->id,
                    'item_id' => $itemData['item_id'],
                    'quantity' => $itemData['quantity'],
                    'price' => $itemData['price'],
                    'subtotal' => $subtotal,
                ]);
            }

            $purchase->update([
                'shop_id' => $shopId,
                'supplier_name' => $data['supplier_name'],
                'purchase_type' => $data['purchase_type'],
                'invoice_no' => $data['invoice_no'] ?? null,
                'total_amount' => $total,
                'paid_amount' => $data['paid_amount'],
                'balance' => max($total - $data['paid_amount'], 0),
            ]);
        });

        return response()->json($purchase->load('items.item'));
    }

    public function destroy(Request $request, Purchase $purchase)
    {
        $this->authorizeShop($request, $purchase);

        DB::transaction(function () use ($purchase) {
            foreach ($purchase->items as $item) {
                $product = Item::find($item->item_id);
                if ($product) {
                    $product->decrement('quantity', $item->quantity);
                }
            }

            $purchase->delete();
        });

        return response()->json(['message' => 'Purchase deleted']);
    }
}
