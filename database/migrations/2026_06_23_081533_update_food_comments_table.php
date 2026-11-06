<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('food_comments')) {
            return;
        }

        if (! Schema::hasColumn('food_comments', 'food_id')) {
            Schema::table('food_comments', function (Blueprint $table) {
                $table->unsignedBigInteger('food_id');
            });
        }

        if (! Schema::hasColumn('food_comments', 'user_id')) {
            Schema::table('food_comments', function (Blueprint $table) {
                $table->unsignedBigInteger('user_id');
            });
        }

        if (! Schema::hasColumn('food_comments', 'comment')) {
            Schema::table('food_comments', function (Blueprint $table) {
                $table->text('comment');
            });
        }
    }

    public function down(): void
    {
        //
    }
};
