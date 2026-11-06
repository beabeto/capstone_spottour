<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('hotels', 'latitude')) {
            Schema::table('hotels', function (Blueprint $table) {
                $table->decimal('latitude', 10, 8)->nullable();
            });
        }

        if (! Schema::hasColumn('hotels', 'longitude')) {
            Schema::table('hotels', function (Blueprint $table) {
                $table->decimal('longitude', 11, 8)->nullable();
            });
        }

        if (! Schema::hasColumn('foods', 'latitude')) {
            Schema::table('foods', function (Blueprint $table) {
                $table->decimal('latitude', 10, 8)->nullable();
            });
        }

        if (! Schema::hasColumn('foods', 'longitude')) {
            Schema::table('foods', function (Blueprint $table) {
                $table->decimal('longitude', 11, 8)->nullable();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('hotels', 'latitude') || Schema::hasColumn('hotels', 'longitude')) {
            Schema::table('hotels', function (Blueprint $table) {
                $drops = [];
                if (Schema::hasColumn('hotels', 'latitude')) {
                    $drops[] = 'latitude';
                }
                if (Schema::hasColumn('hotels', 'longitude')) {
                    $drops[] = 'longitude';
                }
                if ($drops !== []) {
                    $table->dropColumn($drops);
                }
            });
        }

        if (Schema::hasColumn('foods', 'latitude') || Schema::hasColumn('foods', 'longitude')) {
            Schema::table('foods', function (Blueprint $table) {
                $drops = [];
                if (Schema::hasColumn('foods', 'latitude')) {
                    $drops[] = 'latitude';
                }
                if (Schema::hasColumn('foods', 'longitude')) {
                    $drops[] = 'longitude';
                }
                if ($drops !== []) {
                    $table->dropColumn($drops);
                }
            });
        }
    }
};
