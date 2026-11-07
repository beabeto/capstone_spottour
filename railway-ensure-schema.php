<?php

require __DIR__.'/vendor/autoload.php';

$app = require __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

function addColumn(string $table, string $column, callable $define): void
{
    if (! Schema::hasTable($table) || Schema::hasColumn($table, $column)) {
        return;
    }

    Schema::table($table, $define);
}

foreach (['spots', 'hotels', 'foods'] as $listing) {
    addColumn($listing, 'deleted_at', fn (Blueprint $table) => $table->timestamp('deleted_at')->nullable());
    addColumn($listing, 'latitude', fn (Blueprint $table) => $table->decimal('latitude', 10, 8)->nullable());
    addColumn($listing, 'longitude', fn (Blueprint $table) => $table->decimal('longitude', 11, 8)->nullable());
    addColumn($listing, 'gallery_images', fn (Blueprint $table) => $table->json('gallery_images')->nullable());
    addColumn($listing, 'daily_booking_limit', fn (Blueprint $table) => $table->unsignedInteger('daily_booking_limit')->nullable());
}

addColumn('spots', 'map_link', fn (Blueprint $table) => $table->text('map_link')->nullable());
addColumn('hotels', 'map_link', fn (Blueprint $table) => $table->text('map_link')->nullable());
addColumn('foods', 'map_link', fn (Blueprint $table) => $table->text('map_link')->nullable());

foreach (['bookings', 'hotel_bookings', 'food_bookings'] as $booking) {
    addColumn($booking, 'payer_name', fn (Blueprint $table) => $table->string('payer_name')->nullable());
    addColumn($booking, 'payer_number', fn (Blueprint $table) => $table->string('payer_number')->nullable());
    addColumn($booking, 'contact_email', fn (Blueprint $table) => $table->string('contact_email')->nullable());
    addColumn($booking, 'arrival_code', fn (Blueprint $table) => $table->string('arrival_code', 12)->nullable());
    addColumn($booking, 'arrived_at', fn (Blueprint $table) => $table->timestamp('arrived_at')->nullable());
    addColumn($booking, 'payment_method', fn (Blueprint $table) => $table->string('payment_method', 20)->nullable());
    addColumn($booking, 'payment_reference', fn (Blueprint $table) => $table->string('payment_reference', 80)->nullable());
}

if (! Schema::hasTable('day_plans')) {
    Schema::create('day_plans', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('user_id');
        $table->date('plan_date');
        $table->unsignedBigInteger('spot_id')->nullable();
        $table->unsignedBigInteger('hotel_id')->nullable();
        $table->unsignedBigInteger('food_id')->nullable();
        $table->string('note')->nullable();
        $table->timestamps();
    });
}

addColumn('day_plans', 'spot_time', fn (Blueprint $table) => $table->time('spot_time')->nullable());
addColumn('day_plans', 'food_time', fn (Blueprint $table) => $table->time('food_time')->nullable());
addColumn('day_plans', 'hotel_time', fn (Blueprint $table) => $table->time('hotel_time')->nullable());

if (! Schema::hasTable('chat_threads')) {
    Schema::create('chat_threads', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('user_id');
        $table->string('status', 20)->default('bot');
        $table->string('assignee_role', 20)->nullable();
        $table->unsignedBigInteger('assignee_id')->nullable();
        $table->string('listing_type', 20)->nullable();
        $table->unsignedBigInteger('listing_id')->nullable();
        $table->string('listing_name')->nullable();
        $table->unsignedInteger('traveller_unread')->default(0);
        $table->unsignedInteger('staff_unread')->default(0);
        $table->timestamp('last_message_at')->nullable();
        $table->timestamps();
    });
}

if (! Schema::hasTable('chat_messages')) {
    Schema::create('chat_messages', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('chat_thread_id');
        $table->unsignedBigInteger('sender_id')->nullable();
        $table->string('sender_role', 20);
        $table->text('body');
        $table->timestamps();
    });
}

addColumn('notifications', 'chat_thread_id', fn (Blueprint $table) => $table->unsignedBigInteger('chat_thread_id')->nullable());
addColumn('users', 'gender', fn (Blueprint $table) => $table->string('gender', 24)->nullable());
addColumn('users', 'birth_date', fn (Blueprint $table) => $table->date('birth_date')->nullable());
addColumn('users', 'profile_image', fn (Blueprint $table) => $table->string('profile_image')->nullable());
addColumn('users', 'is_online', fn (Blueprint $table) => $table->boolean('is_online')->default(false));

echo "Schema ensured.".PHP_EOL;
