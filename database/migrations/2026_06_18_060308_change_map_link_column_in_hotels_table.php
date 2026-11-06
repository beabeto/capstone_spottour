<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        // hotels.map_link is already text/longText on create — no change needed
    }

    public function down(): void
    {
        //
    }
};
