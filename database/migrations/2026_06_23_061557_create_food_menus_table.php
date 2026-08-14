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
        Schema::create('food_menus', function (Blueprint $table) {

    $table->id();

    $table->foreignId('food_id');

    $table->string('name');

    $table->text('description')->nullable();

    $table->decimal('price',10,2);

    $table->string('image')->nullable();

    $table->enum(
        'category',
        [
            'meal',
            'drink',
            'dessert',
            'appetizer'
        ]
    )->default('meal');

    $table->timestamps();

});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('food_menus');
    }
};
