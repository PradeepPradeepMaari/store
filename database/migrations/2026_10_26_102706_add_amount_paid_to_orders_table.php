<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->decimal('amount_paid', 12, 2)->nullable()->after('grand_total')
                  ->comment('Cash amount handed by customer');
            $table->decimal('change_amount', 12, 2)->nullable()->after('amount_paid')
                  ->comment('Change returned to customer (grand_total - amount_paid)');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['amount_paid', 'change_amount']);
        });
    }
};
