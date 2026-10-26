<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')
                  ->constrained('orders')
                  ->cascadeOnDelete();
            $table->foreignId('product_id')
                  ->constrained('products')
                  ->restrictOnDelete();
            $table->unsignedInteger('quantity');
            // Historical snapshot — values at time of order placement
            $table->decimal('unit_price', 10, 2)->comment('Product price at time of order');
            $table->decimal('tax_percentage', 5, 2)->comment('Tax rate at time of order');
            // Calculated fields
            $table->decimal('subtotal', 12, 2)->comment('unit_price * quantity');
            $table->decimal('tax_amount', 12, 2)->comment('subtotal * tax_percentage / 100');
            $table->decimal('total', 12, 2)->comment('subtotal + tax_amount');
            $table->timestamps();

            $table->index('order_id');
            $table->index('product_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
