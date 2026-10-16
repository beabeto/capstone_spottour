<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $values = "'pending','pending_payment','pending_verification','approved','rejected','cancelled','completed'";

        foreach (['bookings', 'hotel_bookings', 'food_bookings'] as $table) {
            DB::statement("ALTER TABLE `{$table}` MODIFY `status` ENUM({$values}) NOT NULL DEFAULT 'pending'");
        }
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE `bookings` MODIFY `status` ENUM('pending_payment','pending_verification','approved','rejected','completed') NOT NULL DEFAULT 'pending_payment'");
        DB::statement("ALTER TABLE `hotel_bookings` MODIFY `status` ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending'");
        DB::statement("ALTER TABLE `food_bookings` MODIFY `status` ENUM('pending','approved','rejected','cancelled') NOT NULL DEFAULT 'pending'");
    }
};
