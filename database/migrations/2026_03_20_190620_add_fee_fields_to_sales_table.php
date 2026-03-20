<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->after('discount_amount', function (Blueprint $table) {
                $table->bigInteger('fee_amount')->default(0);
                $table->decimal('fee_percentage', 5, 2)->default(0);
                $table->boolean('pass_fee_to_customer')->default(false);
                $table->bigInteger('net_amount')->default(0);
            });
        });
    }

    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn(['fee_amount', 'fee_percentage', 'pass_fee_to_customer', 'net_amount']);
        });
    }
};
