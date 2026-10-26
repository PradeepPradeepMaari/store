<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->decimal('price', 10, 2)->unsigned()->comment('Unit price in base currency');
            $table->decimal('tax_percentage', 5, 2)->default(0)->comment('Tax rate as a percentage (e.g. 18.00)');
            $table->unsignedInteger('stock_quantity')->default(0);
            $table->timestamps();

            // Constraints
            $table->index('code');
            $table->index('stock_quantity');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
