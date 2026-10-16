<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach (['spots', 'hotels', 'foods'] as $table) {
            Schema::table($table, function (Blueprint $table) {
                $table->unsignedInteger('daily_booking_limit')->nullable();
            });
        }
    }

    public function down(): void
    {
        foreach (['spots', 'hotels', 'foods'] as $table) {
            Schema::table($table, function (Blueprint $table) {
                $table->dropColumn('daily_booking_limit');
            });
        }
    }
};
