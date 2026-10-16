<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('hotel_rooms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hotel_id')->constrained('hotels')->cascadeOnDelete();
            $table->string('name');
            $table->decimal('price_per_night', 10, 2);
            $table->unsignedInteger('available_rooms')->default(0);
            $table->string('cover_image')->nullable();
            $table->json('gallery_images')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void { Schema::dropIfExists('hotel_rooms'); }
};
