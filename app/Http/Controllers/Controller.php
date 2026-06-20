<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

abstract class Controller
{
    protected function scopeByShop(Request $request, $query)
    {
        $user = $request->user();

        if (! $user) {
            return $query;
        }

        if ($user->role === 'admin') {
            $shopId = $request->query('shop_id', $request->input('shop_id'));
            if ($shopId) {
                return $query->where('shop_id', $shopId);
            }

            return $query;
        }

        return $query->where('shop_id', $user->shop_id);
    }

    protected function authorizeShop(Request $request, Model $model): void
    {
        $user = $request->user();

        if (! $user || $user->role === 'admin') {
            return;
        }

        if ($model->shop_id !== $user->shop_id) {
            abort(404);
        }
    }
}
