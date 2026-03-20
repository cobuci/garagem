<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('weather_histories', function (Blueprint $table) {
            $table->id();
            $table->string('city');
            $table->date('date');
            $table->decimal('temp_current', 5, 2)->nullable();
            $table->decimal('temp_max', 5, 2);
            $table->decimal('temp_min', 5, 2);
            $table->decimal('precipitation', 5, 2);
            $table->integer('weather_code');
            $table->string('icon');
            $table->string('description');
            $table->timestamps();

            $table->unique(['city', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('weather_histories');
    }
};
