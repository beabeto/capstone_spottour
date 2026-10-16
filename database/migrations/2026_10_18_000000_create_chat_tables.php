<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('chat_threads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('status', 20)->default('bot');
            $table->string('assignee_role', 20)->nullable();
            $table->unsignedBigInteger('assignee_id')->nullable();
            $table->string('listing_type', 20)->nullable();
            $table->unsignedBigInteger('listing_id')->nullable();
            $table->string('listing_name')->nullable();
            $table->unsignedInteger('traveller_unread')->default(0);
            $table->unsignedInteger('staff_unread')->default(0);
            $table->timestamp('last_message_at')->nullable();
            $table->timestamps();
        });

        Schema::create('chat_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chat_thread_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('sender_id')->nullable();
            $table->string('sender_role', 20);
            $table->text('body');
            $table->timestamps();
        });

        if (!Schema::hasColumn('notifications', 'chat_thread_id')) {
            Schema::table('notifications', function (Blueprint $table) {
                $table->unsignedBigInteger('chat_thread_id')->nullable();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('notifications', 'chat_thread_id')) {
            Schema::table('notifications', function (Blueprint $table) {
                $table->dropColumn('chat_thread_id');
            });
        }

        Schema::dropIfExists('chat_messages');
        Schema::dropIfExists('chat_threads');
    }
};
