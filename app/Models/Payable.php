<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payable extends Model
{
    protected $fillable = [
        'shop_id',
        'purchase_id',
        'supplier_name',
        'amount_due',
        'due_date',
        'status',
    ];

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }
}
