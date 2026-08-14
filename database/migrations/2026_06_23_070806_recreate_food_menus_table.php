<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('food_menus');

        Schema::create('food_menus', function (Blueprint $table) {

            $table->id();

            $table->unsignedBigInteger('food_id');

            $table->string('name');

            $table->text('description')->nullable();

            $table->decimal(
                'price',
                10,
                2
            );

            $table->string('category')
                  ->default('meal');

            $table->string('image')
                  ->nullable();

            $table->timestamps();

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('food_menus');
    }
};