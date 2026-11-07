<?php

namespace App\Support;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class EnsureLiveSchema
{
    public static function run(): void
    {
        try {
            foreach (['spots', 'hotels', 'foods'] as $listing) {
                self::column($listing, 'deleted_at', fn (Blueprint $table) => $table->timestamp('deleted_at')->nullable());
                self::column($listing, 'latitude', fn (Blueprint $table) => $table->decimal('latitude', 10, 8)->nullable());
                self::column($listing, 'longitude', fn (Blueprint $table) => $table->decimal('longitude', 11, 8)->nullable());
                self::column($listing, 'map_link', fn (Blueprint $table) => $table->text('map_link')->nullable());
                self::column($listing, 'gallery_images', fn (Blueprint $table) => $table->text('gallery_images')->nullable());
                self::column($listing, 'daily_booking_limit', fn (Blueprint $table) => $table->unsignedInteger('daily_booking_limit')->nullable());
            }

            foreach (['bookings', 'hotel_bookings', 'food_bookings'] as $booking) {
                self::column($booking, 'payer_name', fn (Blueprint $table) => $table->string('payer_name')->nullable());
                self::column($booking, 'payer_number', fn (Blueprint $table) => $table->string('payer_number')->nullable());
                self::column($booking, 'contact_email', fn (Blueprint $table) => $table->string('contact_email')->nullable());
                self::column($booking, 'arrival_code', fn (Blueprint $table) => $table->string('arrival_code', 12)->nullable());
                self::column($booking, 'arrived_at', fn (Blueprint $table) => $table->timestamp('arrived_at')->nullable());
                self::column($booking, 'payment_method', fn (Blueprint $table) => $table->string('payment_method', 20)->nullable());
                self::column($booking, 'payment_reference', fn (Blueprint $table) => $table->string('payment_reference', 80)->nullable());
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

            self::column('day_plans', 'spot_time', fn (Blueprint $table) => $table->time('spot_time')->nullable());
            self::column('day_plans', 'food_time', fn (Blueprint $table) => $table->time('food_time')->nullable());
            self::column('day_plans', 'hotel_time', fn (Blueprint $table) => $table->time('hotel_time')->nullable());

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

            foreach ([
                'sender_id', 'spot_id', 'hotel_id', 'food_id', 'comment_id', 'chat_thread_id',
            ] as $column) {
                self::column('notifications', $column, fn (Blueprint $table) => $table->unsignedBigInteger($column)->nullable());
            }
            self::column('notifications', 'category', fn (Blueprint $table) => $table->string('category', 30)->nullable());

            self::column('users', 'gender', fn (Blueprint $table) => $table->string('gender', 24)->nullable());
            self::column('users', 'birth_date', fn (Blueprint $table) => $table->date('birth_date')->nullable());
            self::column('users', 'profile_image', fn (Blueprint $table) => $table->string('profile_image')->nullable());
            self::column('users', 'is_online', fn (Blueprint $table) => $table->boolean('is_online')->default(false));
        } catch (\Throwable $e) {
            report($e);
        }
    }

    private static function column(string $table, string $column, callable $define): void
    {
        if (! Schema::hasTable($table) || Schema::hasColumn($table, $column)) {
            return;
        }

        Schema::table($table, $define);
    }
}
