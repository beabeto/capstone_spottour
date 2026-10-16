<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach (['bookings', 'hotel_bookings', 'food_bookings'] as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->string('arrival_code', 12)->nullable()->index();
                $table->timestamp('arrived_at')->nullable();
            });
        }

        Schema::create('day_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->date('plan_date');
            $table->foreignId('spot_id')->nullable()->constrained('spots')->nullOnDelete();
            $table->foreignId('hotel_id')->nullable()->constrained('hotels')->nullOnDelete();
            $table->foreignId('food_id')->nullable()->constrained('foods')->nullOnDelete();
            $table->string('note')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('day_plans');

        foreach (['bookings', 'hotel_bookings', 'food_bookings'] as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->dropColumn(['arrival_code', 'arrived_at']);
            });
        }
    }
};
