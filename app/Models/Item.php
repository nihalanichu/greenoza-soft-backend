<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    protected $fillable = [
        'shop_id',
        'name',
        'quantity',
        'buy_price',
        'sell_price',
        'unit',
    ];

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    public function purchaseItems()
    {
        return $this->hasMany(PurchaseItem::class);
    }

    public function saleItems()
    {
        return $this->hasMany(SaleItem::class);
    }
}
