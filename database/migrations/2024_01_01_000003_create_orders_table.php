<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')
                  ->constrained('customers')
                  ->cascadeOnDelete();
            $table->decimal('subtotal', 12, 2)->comment('Sum of all item subtotals before tax');
            $table->decimal('tax_amount', 12, 2)->comment('Total tax across all items');
            $table->decimal('grand_total', 12, 2)->comment('subtotal + tax_amount');
            $table->timestamps();

            $table->index('customer_id');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
