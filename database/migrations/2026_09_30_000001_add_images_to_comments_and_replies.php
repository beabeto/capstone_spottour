<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        foreach (['spot_comments', 'hotel_comments', 'food_comments', 'comment_replies', 'hotel_replies', 'food_replies'] as $tableName) {
            if (!Schema::hasColumn($tableName, 'image')) {
                Schema::table($tableName, function (Blueprint $table) { $table->string('image')->nullable(); });
            }
        }
    }
    public function down(): void
    {
        foreach (['spot_comments', 'hotel_comments', 'food_comments', 'comment_replies', 'hotel_replies', 'food_replies'] as $tableName) {
            if (Schema::hasColumn($tableName, 'image')) {
                Schema::table($tableName, function (Blueprint $table) { $table->dropColumn('image'); });
            }
        }
    }
};
