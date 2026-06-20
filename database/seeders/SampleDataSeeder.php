<?php

namespace Database\Seeders;

use App\Models\DailyClosing;
use App\Models\Expense;
use App\Models\Item;
use App\Models\Payable;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Receivable;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SampleDataSeeder extends Seeder
{
    public function run(): void
    {
        $shop1 = Shop::create([
            'shop_name' => 'Fresh Fruit Market 1',
            'address' => 'Market Road 1',
            'phone' => '0123456789',
        ]);

        $shop2 = Shop::create([
            'shop_name' => 'Fresh Fruit Market 2',
            'address' => 'Market Road 2',
            'phone' => '0987654321',
        ]);

        User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'shop_id' => null,
        ]);

        User::create([
            'name' => 'Shop One Manager',
            'email' => 'shop1@example.com',
            'password' => Hash::make('password'),
            'role' => 'shop',
            'shop_id' => $shop1->id,
        ]);

        User::create([
            'name' => 'Shop Two Manager',
            'email' => 'shop2@example.com',
            'password' => Hash::make('password'),
            'role' => 'shop',
            'shop_id' => $shop2->id,
        ]);

        $apple = Item::create([
            'shop_id' => $shop1->id,
            'name' => 'Apple',
            'quantity' => 80,
            'buy_price' => 10.00,
            'sell_price' => 12.00,
            'unit' => 'kg',
        ]);

        $banana = Item::create([
            'shop_id' => $shop1->id,
            'name' => 'Banana',
            'quantity' => 120,
            'buy_price' => 5.00,
            'sell_price' => 8.00,
            'unit' => 'kg',
        ]);

        $carrot = Item::create([
            'shop_id' => $shop1->id,
            'name' => 'Carrot',
            'quantity' => 60,
            'buy_price' => 8.00,
            'sell_price' => 10.00,
            'unit' => 'kg',
        ]);

        $orange = Item::create([
            'shop_id' => $shop2->id,
            'name' => 'Orange',
            'quantity' => 90,
            'buy_price' => 9.00,
            'sell_price' => 11.00,
            'unit' => 'kg',
        ]);

        $tomato = Item::create([
            'shop_id' => $shop2->id,
            'name' => 'Tomato',
            'quantity' => 110,
            'buy_price' => 6.00,
            'sell_price' => 9.50,
            'unit' => 'kg',
        ]);

        $potato = Item::create([
            'shop_id' => $shop2->id,
            'name' => 'Potato',
            'quantity' => 130,
            'buy_price' => 4.50,
            'sell_price' => 7.00,
            'unit' => 'kg',
        ]);

        $purchase1 = Purchase::create([
            'shop_id' => $shop1->id,
            'supplier_name' => 'Green Suppliers',
            'purchase_type' => 'cash',
            'invoice_no' => 'PUR-1001',
            'total_amount' => 1800.00,
            'paid_amount' => 1800.00,
            'balance' => 0.00,
        ]);

        PurchaseItem::create([
            'purchase_id' => $purchase1->id,
            'item_id' => $apple->id,
            'quantity' => 50,
            'price' => 10.00,
            'subtotal' => 500.00,
        ]);

        PurchaseItem::create([
            'purchase_id' => $purchase1->id,
            'item_id' => $banana->id,
            'quantity' => 100,
            'price' => 5.00,
            'subtotal' => 500.00,
        ]);

        PurchaseItem::create([
            'purchase_id' => $purchase1->id,
            'item_id' => $carrot->id,
            'quantity' => 80,
            'price' => 10.00,
            'subtotal' => 800.00,
        ]);

        $purchase2 = Purchase::create([
            'shop_id' => $shop2->id,
            'supplier_name' => 'Farm Fresh Suppliers',
            'purchase_type' => 'credit',
            'invoice_no' => 'PUR-2001',
            'total_amount' => 950.00,
            'paid_amount' => 450.00,
            'balance' => 500.00,
        ]);

        PurchaseItem::create([
            'purchase_id' => $purchase2->id,
            'item_id' => $orange->id,
            'quantity' => 50,
            'price' => 9.00,
            'subtotal' => 450.00,
        ]);

        PurchaseItem::create([
            'purchase_id' => $purchase2->id,
            'item_id' => $tomato->id,
            'quantity' => 50,
            'price' => 10.00,
            'subtotal' => 500.00,
        ]);

        Payable::create([
            'shop_id' => $shop2->id,
            'purchase_id' => $purchase2->id,
            'supplier_name' => 'Farm Fresh Suppliers',
            'amount_due' => 500.00,
            'due_date' => date('Y-m-d', strtotime('+10 days')),
            'status' => 'pending',
        ]);

        $sale1 = Sale::create([
            'shop_id' => $shop1->id,
            'customer_name' => 'Local Customer',
            'sale_type' => 'cash',
            'invoice_no' => 'SAL-1001',
            'total_amount' => 560.00,
            'paid_amount' => 560.00,
            'balance' => 0.00,
        ]);

        SaleItem::create([
            'sale_id' => $sale1->id,
            'item_id' => $apple->id,
            'quantity' => 20,
            'price' => 12.00,
            'subtotal' => 240.00,
        ]);

        SaleItem::create([
            'sale_id' => $sale1->id,
            'item_id' => $banana->id,
            'quantity' => 20,
            'price' => 8.00,
            'subtotal' => 160.00,
        ]);

        SaleItem::create([
            'sale_id' => $sale1->id,
            'item_id' => $carrot->id,
            'quantity' => 16,
            'price' => 10.00,
            'subtotal' => 160.00,
        ]);

        $sale2 = Sale::create([
            'shop_id' => $shop1->id,
            'customer_name' => 'Wholesale Buyer',
            'sale_type' => 'credit',
            'invoice_no' => 'SAL-1002',
            'total_amount' => 280.00,
            'paid_amount' => 80.00,
            'balance' => 200.00,
        ]);

        SaleItem::create([
            'sale_id' => $sale2->id,
            'item_id' => $banana->id,
            'quantity' => 10,
            'price' => 8.00,
            'subtotal' => 80.00,
        ]);

        SaleItem::create([
            'sale_id' => $sale2->id,
            'item_id' => $carrot->id,
            'quantity' => 20,
            'price' => 10.00,
            'subtotal' => 200.00,
        ]);

        Receivable::create([
            'shop_id' => $shop1->id,
            'sale_id' => $sale2->id,
            'customer_name' => 'Wholesale Buyer',
            'amount_due' => 200.00,
            'due_date' => date('Y-m-d', strtotime('+15 days')),
            'status' => 'pending',
        ]);

        Expense::create([
            'shop_id' => $shop1->id,
            'category' => 'Utilities',
            'title' => 'Electricity bill',
            'amount' => 120.00,
            'expense_date' => date('Y-m-d'),
            'notes' => 'Weekly market stall electricity',
        ]);

        Expense::create([
            'shop_id' => $shop2->id,
            'category' => 'Supplies',
            'title' => 'Packaging bags',
            'amount' => 75.00,
            'expense_date' => date('Y-m-d'),
            'notes' => 'Extra produce packaging',
        ]);

        DailyClosing::create([
            'shop_id' => $shop1->id,
            'closing_date' => date('Y-m-d'),
            'cash_sales' => 560.00,
            'credit_sales' => 280.00,
            'expenses' => 120.00,
            'closing_balance' => 720.00,
        ]);

        DailyClosing::create([
            'shop_id' => $shop2->id,
            'closing_date' => date('Y-m-d'),
            'cash_sales' => 0.00,
            'credit_sales' => 0.00,
            'expenses' => 75.00,
            'closing_balance' => -75.00,
        ]);
    }
}
