<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('foods', fn (Blueprint $table) => $table->json('gallery_images')->nullable()->after('image'));
        Schema::table('hotels', fn (Blueprint $table) => $table->json('room_types')->nullable()->after('available_rooms'));
    }
    public function down(): void
    {
        Schema::table('foods', fn (Blueprint $table) => $table->dropColumn('gallery_images'));
        Schema::table('hotels', fn (Blueprint $table) => $table->dropColumn('room_types'));
    }
};
