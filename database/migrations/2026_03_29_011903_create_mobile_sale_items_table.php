<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mobile_sale_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mobile_sale_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained();
            $table->bigInteger('unit_price_cents');
            $table->integer('quantity');
            $table->bigInteger('subtotal_cents');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mobile_sale_items');
    }
};
