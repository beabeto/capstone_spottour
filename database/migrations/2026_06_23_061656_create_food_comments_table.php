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
        Schema::table('food_comments', function (Blueprint $table) {

    $table->unsignedBigInteger('food_id');

    $table->unsignedBigInteger('user_id');

    $table->text('comment');

});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('food_comments');
    }
};
