<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('notifications', 'food_id')) {
            Schema::table('notifications', function (Blueprint $table) {
                $table->unsignedBigInteger('food_id')->nullable()->after('hotel_id');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('notifications', 'food_id')) {
            Schema::table('notifications', function (Blueprint $table) {
                $table->dropColumn('food_id');
            });
        }
    }
};
