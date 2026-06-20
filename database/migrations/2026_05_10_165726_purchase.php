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
        Schema::create('purchases', function (Blueprint $table) {
         $table->id();

         $table->foreignId('shop_id')
          ->constrained()
          ->onDelete('cascade');

         $table->string('supplier_name');

         $table->enum('purchase_type', ['cash', 'credit']);

         $table->string('invoice_no')->nullable();

         $table->decimal('total_amount', 10, 2);

         $table->decimal('paid_amount', 10, 2)->default(0);

         $table->decimal('balance', 10, 2)->default(0);

         $table->timestamps();
      });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchases');
    }
};
