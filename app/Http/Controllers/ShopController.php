<?php

namespace App\Http\Controllers;

use App\Models\Shop;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->role === 'admin') {
            return response()->json(Shop::orderBy('shop_name')->get());
        }

        return response()->json(Shop::where('id', $user->shop_id)->get());
    }

    public function show(Request $request, Shop $shop)
    {
        $user = $request->user();

        if ($user->role !== 'admin' && $shop->id !== $user->shop_id) {
            abort(404);
        }

        return response()->json($shop);
    }
}
