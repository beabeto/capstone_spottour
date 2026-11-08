<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach (['bookings', 'hotel_bookings', 'food_bookings'] as $tableName) {
            if (! Schema::hasTable($tableName) || ! in_array('status', Schema::getColumnListing($tableName), true)) {
                continue;
            }

            Schema::table($tableName, function (Blueprint $table) {
                $table->string('status', 32)->default('pending')->change();
            });
        }
    }

    public function down(): void
    {
        // Keep the string column to avoid truncating newer payment states.
    }
};
