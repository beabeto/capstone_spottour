<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('foods')) {
            return;
        }

        $columns = Schema::getColumnListing('foods');
        if (! in_array('map_link', $columns, true)) {
            Schema::table('foods', function (Blueprint $table) {
                $table->text('map_link')->nullable();
            });
            return;
        }

        Schema::table('foods', function (Blueprint $table) {
            $table->text('map_link')->nullable()->change();
        });
    }

    public function down(): void
    {
        // Keep existing long Google Maps URLs when rolling back application code.
    }
};
