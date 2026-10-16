<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('food_bookings', function (Blueprint $table) {
            if (! Schema::hasColumn('food_bookings', 'menu_items')) {
                $table->json('menu_items')->nullable()->after('special_request');
            }
        });
    }

    public function down(): void
    {
        Schema::table('food_bookings', function (Blueprint $table) {
            if (Schema::hasColumn('food_bookings', 'menu_items')) {
                $table->dropColumn('menu_items');
            }
        });
    }
};
