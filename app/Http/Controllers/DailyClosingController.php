<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Sale;
use Illuminate\Http\Request;

class DailyClosingController extends Controller
{
    public function closingReport(Request $request)
    {
        $sales = $this->scopeByShop($request, Sale::query());
        $expenses = $this->scopeByShop($request, Expense::query());

        $cashSales = (float) $sales->where('sale_type', 'cash')->sum('total_amount');
        $creditSales = (float) $sales->where('sale_type', 'credit')->sum('total_amount');
        $totalExpenses = (float) $expenses->sum('amount');
        $closingBalance = $cashSales + $creditSales - $totalExpenses;

        return response()->json([
            'cash_sales' => $cashSales,
            'credit_sales' => $creditSales,
            'expenses' => $totalExpenses,
            'closing_balance' => $closingBalance,
        ]);
    }
}
