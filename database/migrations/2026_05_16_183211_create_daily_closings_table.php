<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('daily_closings', function (Blueprint $table) {
    $table->id();

    $table->foreignId('shop_id')
          ->constrained()
          ->onDelete('cascade');

    $table->date('closing_date');

    $table->decimal('cash_sales', 10, 2)->default(0);

    $table->decimal('credit_sales', 10, 2)->default(0);

    $table->decimal('expenses', 10, 2)->default(0);

    $table->decimal('closing_balance', 10, 2)->default(0);

    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_closings');
    }
};
