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
        Schema::create('payables', function (Blueprint $table) {
    $table->id();

    $table->foreignId('shop_id')
          ->constrained()
          ->onDelete('cascade');

    $table->foreignId('purchase_id')
          ->constrained()
          ->onDelete('cascade');

    $table->string('supplier_name');

    $table->decimal('amount_due', 10, 2);

    $table->date('due_date')->nullable();

    $table->enum('status', ['pending', 'paid'])
          ->default('pending');

    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payables');
    }
};
