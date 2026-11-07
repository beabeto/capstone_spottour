<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach (['bookings', 'hotel_bookings', 'food_bookings'] as $tableName) {
            if (! Schema::hasTable($tableName)) {
                continue;
            }

            if (! Schema::hasColumn($tableName, 'payment_method')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->string('payment_method', 20)->nullable();
                });
            }

            if (! Schema::hasColumn($tableName, 'payment_reference')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->string('payment_reference', 80)->nullable();
                });
            }
        }
    }

    public function down(): void
    {
        foreach (['bookings', 'hotel_bookings', 'food_bookings'] as $tableName) {
            $drops = [];
            if (Schema::hasColumn($tableName, 'payment_method')) {
                $drops[] = 'payment_method';
            }
            if (Schema::hasColumn($tableName, 'payment_reference')) {
                $drops[] = 'payment_reference';
            }
            if ($drops !== []) {
                Schema::table($tableName, function (Blueprint $table) use ($drops) {
                    $table->dropColumn($drops);
                });
            }
        }
    }
};
