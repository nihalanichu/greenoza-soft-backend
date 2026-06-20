<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    protected $fillable = [
        'shop_id',
        'customer_name',
        'sale_type',
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
        return $this->hasMany(SaleItem::class);
    }

    public function receivables()
    {
        return $this->hasMany(Receivable::class);
    }
}
