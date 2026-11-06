<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('food_bookings', 'total_price')) {
            Schema::table('food_bookings', function (Blueprint $table) {
                $table->decimal('total_price', 10, 2)->default(0)->after('guests');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('food_bookings', 'total_price')) {
            Schema::table('food_bookings', function (Blueprint $table) {
                $table->dropColumn('total_price');
            });
        }
    }
};
