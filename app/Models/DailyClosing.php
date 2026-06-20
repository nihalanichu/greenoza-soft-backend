<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailyClosing extends Model
{
    protected $fillable = [
        'shop_id',
        'closing_date',
        'cash_sales',
        'credit_sales',
        'expenses',
        'closing_balance',
    ];

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }
}
