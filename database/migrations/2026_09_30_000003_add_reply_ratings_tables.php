<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        foreach (['comment_reply_ratings' => 'reply_id', 'hotel_reply_ratings' => 'reply_id', 'food_reply_ratings' => 'reply_id'] as $tableName => $replyId) {
            Schema::create($tableName, function (Blueprint $table) use ($replyId) {
                $table->id();
                $table->unsignedBigInteger($replyId);
                $table->unsignedBigInteger('user_id');
                $table->unsignedTinyInteger('rating');
                $table->timestamps();
                $table->unique([$replyId, 'user_id']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('comment_reply_ratings');
        Schema::dropIfExists('hotel_reply_ratings');
        Schema::dropIfExists('food_reply_ratings');
    }
};
