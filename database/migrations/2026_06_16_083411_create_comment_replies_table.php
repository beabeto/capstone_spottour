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
    if (!Schema::hasTable('comment_replies')) {

        Schema::create('comment_replies', function (Blueprint $table) {

            $table->id();
            $table->foreignId('comment_id');
            $table->foreignId('user_id');
            $table->text('reply');
            $table->timestamps();

        });

    }
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comment_replies');
    }
};
