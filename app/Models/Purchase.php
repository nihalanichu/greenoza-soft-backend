<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    protected $fillable = [
        'shop_id',
        'supplier_name',
        'purchase_type',
        'invoice_no',
        'total_amount',
        'paid_amount',
        'balance',
    ];

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    public function items()
    {
        return $this->hasMany(PurchaseItem::class);
    }

    public function payables()
    {
        return $this->hasMany(Payable::class);
    }
}
