<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('food_comments', function (Blueprint $table) {

            $table->unsignedBigInteger('food_id');

            $table->unsignedBigInteger('user_id');

            $table->text('comment');

        });
    }

    public function down(): void
    {
        Schema::table('food_comments', function (Blueprint $table) {

            $table->dropColumn([
                'food_id',
                'user_id',
                'comment'
            ]);

        });
    }
};