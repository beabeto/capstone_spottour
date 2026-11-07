<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        foreach (['bookings', 'hotel_bookings', 'food_bookings'] as $bookingTable) {
            if (! Schema::hasTable($bookingTable)) {
                continue;
            }

            if (! Schema::hasColumn($bookingTable, 'payer_name')) {
                Schema::table($bookingTable, function (Blueprint $table) {
                    $table->string('payer_name')->nullable();
                });
            }

            if (! Schema::hasColumn($bookingTable, 'payer_number')) {
                Schema::table($bookingTable, function (Blueprint $table) {
                    $table->string('payer_number')->nullable();
                });
            }

            if (! Schema::hasColumn($bookingTable, 'contact_email')) {
                Schema::table($bookingTable, function (Blueprint $table) {
                    $table->string('contact_email')->nullable();
                });
            }
        }
    }

    public function down(): void
    {
        foreach (['bookings', 'hotel_bookings', 'food_bookings'] as $bookingTable) {
            if (Schema::hasTable($bookingTable) && Schema::hasColumn($bookingTable, 'contact_email')) {
                Schema::table($bookingTable, function (Blueprint $table) {
                    $table->dropColumn('contact_email');
                });
            }
        }
    }
};
