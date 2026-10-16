<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        foreach (['bookings', 'hotel_bookings', 'food_bookings'] as $bookingTable) {
            if (!Schema::hasColumn($bookingTable, 'contact_email')) {
                Schema::table($bookingTable, function (Blueprint $table) {
                    $table->string('contact_email')->nullable()->after('payer_number');
                });
            }
        }
    }

    public function down(): void
    {
        foreach (['bookings', 'hotel_bookings', 'food_bookings'] as $bookingTable) {
            if (Schema::hasColumn($bookingTable, 'contact_email')) {
                Schema::table($bookingTable, function (Blueprint $table) {
                    $table->dropColumn('contact_email');
                });
            }
        }
    }
};
