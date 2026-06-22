<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {

    $table->id();

    $table->foreignId('user_id');

    $table->foreignId('spot_id');

    $table->date('booking_date');

    $table->integer('quantity');

    $table->decimal('total_price',10,2);

    $table->enum('status',[
        'pending_payment',
        'pending_verification',
        'approved',
        'rejected',
        'completed'
    ])->default('pending_payment');

    $table->timestamps();

});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
