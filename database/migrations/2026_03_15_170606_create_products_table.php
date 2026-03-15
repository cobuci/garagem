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
            $table->foreignId('category_id')
                ->constrained('categories')
                ->cascadeOnDelete();
            $table->string('name');
            $table->string('brand')->nullable();
            $table->string('weight');
            $table->string('upc')->unique()->nullable();
            $table->integer('stock_quantity')->default(0);
            $table->integer('unit_cost')->nullable();
            $table->integer('sale_price')->nullable();
            $table->dateTime('expiration_date')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
