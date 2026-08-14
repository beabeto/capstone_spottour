<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('food_comment_ratings', function (Blueprint $table) {
            $table->id();

            $table->foreignId('food_comment_id')
                  ->constrained('food_comments')
                  ->cascadeOnDelete();

            $table->foreignId('user_id')
                  ->constrained('users')
                  ->cascadeOnDelete();

            $table->tinyInteger('rating');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('food_comment_ratings');
    }
};