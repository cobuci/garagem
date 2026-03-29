<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mobile_sales', function (Blueprint $table) {
            $table->id();
            $table->string('local_id')->unique();
            $table->foreignId('customer_id')->nullable()->nullOnDelete()->constrained('customers');
            $table->string('customer_name')->nullable();
            $table->bigInteger('total_amount_cents');
            $table->string('status')->default('pending');
            $table->timestamp('device_created_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mobile_sales');
    }
};
