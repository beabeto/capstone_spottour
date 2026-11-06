<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('bookings', 'booking_type')) {
            Schema::table('bookings', function (Blueprint $table) {
                $table->string('booking_type')->default('spot');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('bookings', 'booking_type')) {
            Schema::table('bookings', function (Blueprint $table) {
                $table->dropColumn('booking_type');
            });
        }
    }
};
