<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('packages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('spot_id')->nullable()->constrained('spots')->nullOnDelete();
            $table->foreignId('hotel_id')->nullable()->constrained('hotels')->nullOnDelete();
            $table->foreignId('food_id')->nullable()->constrained('foods')->nullOnDelete();
            $table->string('name');
            $table->string('kind', 20);
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2)->nullable();
            $table->timestamps();
        });

        Schema::table('bookings', function (Blueprint $table) {
            $table->boolean('needs_guide')->default(false);
            $table->foreignId('package_id')->nullable();
            $table->string('package_name')->nullable();
        });

        Schema::table('hotel_bookings', function (Blueprint $table) {
            $table->foreignId('hotel_room_id')->nullable();
            $table->string('room_name')->nullable();
            $table->foreignId('package_id')->nullable();
            $table->string('package_name')->nullable();
        });

        Schema::table('food_bookings', function (Blueprint $table) {
            $table->foreignId('package_id')->nullable();
            $table->string('package_name')->nullable();
        });

        Schema::table('notifications', function (Blueprint $table) {
            $table->string('category', 20)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->dropColumn('category');
        });
        Schema::table('food_bookings', function (Blueprint $table) {
            $table->dropColumn(['package_id', 'package_name']);
        });
        Schema::table('hotel_bookings', function (Blueprint $table) {
            $table->dropColumn(['hotel_room_id', 'room_name', 'package_id', 'package_name']);
        });
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['needs_guide', 'package_id', 'package_name']);
        });
        Schema::dropIfExists('packages');
    }
};
