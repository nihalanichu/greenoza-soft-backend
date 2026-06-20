<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Payable;
use App\Models\Purchase;
use App\Models\Receivable;
use App\Models\Sale;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function profitLoss(Request $request)
    {
        $sales = $this->scopeByShop($request, Sale::query());
        $purchases = $this->scopeByShop($request, Purchase::query());
        $expenses = $this->scopeByShop($request, Expense::query());

        $cashSales = (float) $sales->where('sale_type', 'cash')->sum('total_amount');
        $creditSales = (float) $sales->where('sale_type', 'credit')->sum('total_amount');
        $purchaseCash = (float) $purchases->where('purchase_type', 'cash')->sum('total_amount');
        $purchaseCredit = (float) $purchases->where('purchase_type', 'credit')->sum('total_amount');
        $totalExpenses = (float) $expenses->sum('amount');

        return response()->json([
            'cash_sales' => $cashSales,
            'credit_sales' => $creditSales,
            'cash_purchases' => $purchaseCash,
            'credit_purchases' => $purchaseCredit,
            'total_expenses' => $totalExpenses,
            'profit' => $cashSales + $creditSales - ($purchaseCash + $purchaseCredit + $totalExpenses),
            'sales_total' => $cashSales + $creditSales,
            'purchase_total' => $purchaseCash + $purchaseCredit,
        ]);
    }

    public function salesHistory(Request $request)
    {
        $query = $this->scopeByShop($request, Sale::with('items.item'));

        if ($request->query('sale_type')) {
            $query->where('sale_type', $request->query('sale_type'));
        }

        return response()->json($query->orderByDesc('created_at')->get());
    }

    public function payablesStatus(Request $request)
    {
        $status = $this->scopeByShop($request, Payable::query())
            ->selectRaw('status, count(*) as count, sum(amount_due) as total')
            ->groupBy('status')
            ->get();

        return response()->json($status);
    }

    public function receivablesStatus(Request $request)
    {
        $status = $this->scopeByShop($request, Receivable::query())
            ->selectRaw('status, count(*) as count, sum(amount_due) as total')
            ->groupBy('status')
            ->get();

        return response()->json($status);
    }
}
