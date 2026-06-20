<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    protected $fillable = [
        'shop_id',
        'category',
        'title',
        'amount',
        'expense_date',
        'notes',
    ];

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }
}
