<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('day_plans', function (Blueprint $table) {
            $table->time('spot_time')->nullable()->after('spot_id');
            $table->time('food_time')->nullable()->after('food_id');
            $table->time('hotel_time')->nullable()->after('hotel_id');
        });
    }

    public function down(): void
    {
        Schema::table('day_plans', function (Blueprint $table) {
            $table->dropColumn(['spot_time', 'food_time', 'hotel_time']);
        });
    }
};
