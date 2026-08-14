<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('food_bookings', function (Blueprint $table) {

            $table->id();

            $table->foreignId('user_id')
                  ->constrained('users')
                  ->cascadeOnDelete();

            $table->foreignId('food_id')
                  ->constrained('foods')
                  ->cascadeOnDelete();

            $table->date('reservation_date');

            $table->time('reservation_time');

            $table->integer('guests');

            $table->decimal('total_price',10,2)->default(0);

            $table->text('special_request')->nullable();

            $table->string('payer_name');

            $table->string('payer_number');

            $table->string('receipt')->nullable();

            $table->enum('status',[
                'pending',
                'approved',
                'rejected',
                'cancelled'
            ])->default('pending');

            $table->timestamps();

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('food_bookings');
    }
};