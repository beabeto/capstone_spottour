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
    Schema::create('spots', function (Blueprint $table) {

        $table->id();

        $table->foreignId('user_id');

        $table->string('name');

        $table->string('location');

        $table->text('description');

        $table->decimal('price',10,2)
              ->nullable();

        $table->string('image')
              ->nullable();

        $table->enum('status',[
            'pending',
            'approved',
            'rejected'
        ])->default('pending');

        

        $table->enum('booking_type',[
    'none',
    'cottage'
])->default('none');

$table->integer('available_cottages')
      ->nullable();

$table->decimal('cottage_price',10,2)
      ->nullable();

      $table->text('map_link')->nullable();

        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('spots');
    }
};
