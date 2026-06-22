<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('spots', function (Blueprint $table) {

            $table->decimal('latitude',10,8)
                  ->nullable();

            $table->decimal('longitude',11,8)
                  ->nullable();

            $table->text('map_link')
                  ->nullable();

            $table->enum('booking_type',[
                'none',
                'cottage'
            ])->default('none');

            $table->integer('available_cottages')
                  ->nullable();

            $table->decimal('cottage_price',10,2)
                  ->nullable();

        });
    }

    public function down(): void
    {
        Schema::table('spots', function (Blueprint $table) {

            $table->dropColumn([
                'latitude',
                'longitude',
                'map_link',
                'booking_type',
                'available_cottages',
                'cottage_price'
            ]);

        });
    }
};