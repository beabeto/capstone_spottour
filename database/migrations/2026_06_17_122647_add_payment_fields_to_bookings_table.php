<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('bookings', 'receipt')) {
            Schema::table('bookings', function (Blueprint $table) {
                $table->string('receipt')->nullable();
            });
        }

        if (! Schema::hasColumn('bookings', 'payer_name')) {
            Schema::table('bookings', function (Blueprint $table) {
                $table->string('payer_name')->nullable();
            });
        }

        if (! Schema::hasColumn('bookings', 'payer_number')) {
            Schema::table('bookings', function (Blueprint $table) {
                $table->string('payer_number')->nullable();
            });
        }
    }

    public function down(): void
    {
        //
    }
};
