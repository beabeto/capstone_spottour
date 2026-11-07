<?php

namespace App\Support;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class EnsureLiveSchema
{
    private static bool $ran = false;

    public static function run(): void
    {
        if (self::$ran) {
            return;
        }

        try {
            if (! Schema::hasTable('spots')) {
                Schema::create('spots', function (Blueprint $table) {
                    $table->id();
                    $table->unsignedBigInteger('user_id')->nullable();
                    $table->string('name');
                    $table->string('location')->nullable();
                    $table->text('description')->nullable();
                    $table->decimal('price', 10, 2)->nullable();
                    $table->string('image')->nullable();
                    $table->text('gallery_images')->nullable();
                    $table->string('status')->default('pending');
                    $table->string('booking_type')->default('none');
                    $table->integer('available_cottages')->nullable();
                    $table->decimal('cottage_price', 10, 2)->nullable();
                    $table->unsignedInteger('daily_booking_limit')->nullable();
                    $table->text('map_link')->nullable();
                    $table->double('latitude')->nullable();
                    $table->double('longitude')->nullable();
                    $table->timestamp('deleted_at')->nullable();
                    $table->timestamps();
                });
            }

            foreach (['spots', 'hotels', 'foods'] as $listing) {
                self::column($listing, 'deleted_at', fn (Blueprint $table) => $table->timestamp('deleted_at')->nullable());
                self::column($listing, 'latitude', fn (Blueprint $table) => $table->decimal('latitude', 10, 8)->nullable());
                self::column($listing, 'longitude', fn (Blueprint $table) => $table->decimal('longitude', 11, 8)->nullable());
                self::column($listing, 'map_link', fn (Blueprint $table) => $table->text('map_link')->nullable());
                self::column($listing, 'gallery_images', fn (Blueprint $table) => $table->text('gallery_images')->nullable());
                self::column($listing, 'daily_booking_limit', fn (Blueprint $table) => $table->unsignedInteger('daily_booking_limit')->nullable());
                self::column($listing, 'image', fn (Blueprint $table) => $table->string('image')->nullable());
                self::column($listing, 'status', fn (Blueprint $table) => $table->string('status')->default('pending'));
            }

            self::column('spots', 'price', fn (Blueprint $table) => $table->decimal('price', 10, 2)->nullable());
            self::column('spots', 'booking_type', fn (Blueprint $table) => $table->string('booking_type')->default('none'));
            self::column('spots', 'available_cottages', fn (Blueprint $table) => $table->integer('available_cottages')->nullable());
            self::column('spots', 'cottage_price', fn (Blueprint $table) => $table->decimal('cottage_price', 10, 2)->nullable());
            self::column('hotels', 'price_per_night', fn (Blueprint $table) => $table->decimal('price_per_night', 10, 2)->nullable());
            self::column('hotels', 'available_rooms', fn (Blueprint $table) => $table->integer('available_rooms')->default(0));
            self::column('hotels', 'amenities', fn (Blueprint $table) => $table->text('amenities')->nullable());
            self::column('hotels', 'room_types', fn (Blueprint $table) => $table->text('room_types')->nullable());
            self::column('foods', 'available_tables', fn (Blueprint $table) => $table->integer('available_tables')->default(0));
            self::column('foods', 'venue_capacity', fn (Blueprint $table) => $table->integer('venue_capacity')->default(0));
            self::column('foods', 'price_per_guest', fn (Blueprint $table) => $table->decimal('price_per_guest', 10, 2)->nullable());

            if (! Schema::hasTable('packages')) {
                Schema::create('packages', function (Blueprint $table) {
                    $table->id();
                    $table->unsignedBigInteger('spot_id')->nullable();
                    $table->unsignedBigInteger('hotel_id')->nullable();
                    $table->unsignedBigInteger('food_id')->nullable();
                    $table->string('name');
                    $table->string('kind', 20)->nullable();
                    $table->text('description')->nullable();
                    $table->decimal('price', 10, 2)->nullable();
                    $table->timestamps();
                });
            }

            if (! Schema::hasTable('hotel_rooms')) {
                Schema::create('hotel_rooms', function (Blueprint $table) {
                    $table->id();
                    $table->unsignedBigInteger('hotel_id');
                    $table->string('name');
                    $table->decimal('price_per_night', 10, 2)->default(0);
                    $table->unsignedInteger('available_rooms')->default(0);
                    $table->string('cover_image')->nullable();
                    $table->text('gallery_images')->nullable();
                    $table->timestamps();
                });
            }

            if (! Schema::hasTable('food_menus')) {
                Schema::create('food_menus', function (Blueprint $table) {
                    $table->id();
                    $table->unsignedBigInteger('food_id');
                    $table->string('name');
                    $table->text('description')->nullable();
                    $table->decimal('price', 10, 2)->default(0);
                    $table->string('category')->default('meal');
                    $table->string('image')->nullable();
                    $table->timestamps();
                });
            }

            foreach (['bookings', 'hotel_bookings', 'food_bookings'] as $booking) {
                self::column($booking, 'package_id', fn (Blueprint $table) => $table->unsignedBigInteger('package_id')->nullable());
                self::column($booking, 'package_name', fn (Blueprint $table) => $table->string('package_name')->nullable());
            }
            self::column('bookings', 'needs_guide', fn (Blueprint $table) => $table->boolean('needs_guide')->default(false));
            self::column('hotel_bookings', 'hotel_room_id', fn (Blueprint $table) => $table->unsignedBigInteger('hotel_room_id')->nullable());
            self::column('hotel_bookings', 'room_name', fn (Blueprint $table) => $table->string('room_name')->nullable());

            foreach ([
                'uploads/spots',
                'uploads/hotels',
                'uploads/hotel-rooms',
                'uploads/foods',
                'uploads/menus',
                'uploads/profile',
                'uploads/comments',
                'uploads/receipts',
                'uploads/gcash-qr',
            ] as $dir) {
                $path = public_path($dir);
                if (! is_dir($path)) {
                    mkdir($path, 0775, true);
                }
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
            self::$ran = true;
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
