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
    Schema::create('foods', function (Blueprint $table) {

    $table->id();

    $table->unsignedBigInteger('owner_id');

    $table->string('name');

    $table->string('location');

    $table->text('description');

    $table->integer('available_tables')
          ->default(0);

    $table->integer('venue_capacity')
          ->default(0);

    $table->string('map_link')
          ->nullable();

    $table->string('image')
          ->nullable();

    $table->enum(
        'status',
        ['pending','approved','rejected']
    )->default('pending');

    $table->timestamps();
});
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('foods');
    }
};
