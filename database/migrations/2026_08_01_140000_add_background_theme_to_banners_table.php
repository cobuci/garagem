<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('banners', function (Blueprint $table) {
            $table->string('background_theme')->nullable()->after('background_prompt');
            $table->string('background_mood')->nullable()->after('background_theme');
            $table->string('background_intensity')->nullable()->after('background_mood');
        });
    }

    public function down(): void
    {
        Schema::table('banners', function (Blueprint $table) {
            $table->dropColumn(['background_theme', 'background_mood', 'background_intensity']);
        });
    }
};
