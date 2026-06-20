<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function generateInvoice(Request $request, $id)
    {
        $sale = Sale::with('items.item')->findOrFail($id);
        $this->authorizeShop($request, $sale);

        return response()->json($sale);
    }
}
