<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('account_balances', function (Blueprint $table) {
            $table->id();
            $table->integer('current_balance')->default(0);
            $table->integer('target_balance')->default(0);
            $table->timestamps();
        });

        DB::table('account_balances')->insert([
            'current_balance' => 0,
            'target_balance'  => 0,
            'created_at'      => now(),
            'updated_at'      => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('account_balances');
    }
};
