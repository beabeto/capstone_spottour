<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {

            if (!Schema::hasColumn('bookings', 'receipt')) {
    $table->string('receipt')->nullable();
}

if (!Schema::hasColumn('bookings', 'payer_name')) {
    $table->string('payer_name')->nullable();
}

if (!Schema::hasColumn('bookings', 'payer_number')) {
    $table->string('payer_number')->nullable();
}

        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {

            $table->dropColumn([
                'receipt',
                'payer_name',
                'payer_number'
            ]);

        });
    }
};