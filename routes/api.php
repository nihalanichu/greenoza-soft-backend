<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DailyClosingController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\PayableController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\ReceivableController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\ItemController;
use App\Http\Middleware\AuthenticateApiToken;
use App\Http\Middleware\CorsMiddleware;
use App\Http\Middleware\ShopIsolationMiddleware;
use Illuminate\Support\Facades\Route;

Route::middleware(CorsMiddleware::class)->group(function () {
    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware([AuthenticateApiToken::class, ShopIsolationMiddleware::class])->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);

        Route::apiResource('shops', ShopController::class)->only(['index', 'show']);
        Route::apiResource('inventory', ItemController::class);
        Route::apiResource('purchases', PurchaseController::class);
        Route::apiResource('sales', SaleController::class);
        Route::apiResource('expenses', ExpenseController::class);
        Route::apiResource('payables', PayableController::class);
        Route::apiResource('receivables', ReceivableController::class);

        Route::get('/reports/profit-loss', [ReportController::class, 'profitLoss']);
        Route::get('/reports/sales-history', [ReportController::class, 'salesHistory']);
        Route::get('/reports/payables-status', [ReportController::class, 'payablesStatus']);
        Route::get('/reports/receivables-status', [ReportController::class, 'receivablesStatus']);
        Route::get('/daily-closing', [DailyClosingController::class, 'closingReport']);
        Route::get('/invoice/{id}', [InvoiceController::class, 'generateInvoice']);
    });

    Route::options('/{any}', function () {
        return response()->noContent();
    })->where('any', '.*');
});