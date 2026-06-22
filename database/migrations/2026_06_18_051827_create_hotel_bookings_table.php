<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hotel_bookings', function ($table) {

    $table->id();

    $table->unsignedBigInteger('user_id');

    $table->unsignedBigInteger('hotel_id');

    $table->date('check_in');

    $table->date('check_out');

    $table->integer('rooms');

    $table->enum(
        'status',
        [
            'pending',
            'approved',
            'rejected'
        ]
    )->default('pending');

    $table->timestamps();

});
    }

    public function down(): void
    {
        Schema::dropIfExists(
            'hotel_bookings'
        );
    }
};