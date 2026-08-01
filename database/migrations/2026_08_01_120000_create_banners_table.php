<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('banners', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('format')->default('stories');
            $table->json('design');
            $table->text('background_prompt')->nullable();
            $table->string('background_path')->nullable();
            $table->string('background_status')->default('none');
            $table->string('export_status')->default('none');
            $table->string('export_png_path')->nullable();
            $table->string('export_pdf_path')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('banners');
    }
};
