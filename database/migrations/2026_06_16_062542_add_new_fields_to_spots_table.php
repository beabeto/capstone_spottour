<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('spots', 'latitude')) {
            Schema::table('spots', function (Blueprint $table) {
                $table->decimal('latitude', 10, 8)->nullable();
            });
        }

        if (! Schema::hasColumn('spots', 'longitude')) {
            Schema::table('spots', function (Blueprint $table) {
                $table->decimal('longitude', 11, 8)->nullable();
            });
        }

        if (! Schema::hasColumn('spots', 'map_link')) {
            Schema::table('spots', function (Blueprint $table) {
                $table->text('map_link')->nullable();
            });
        }

        if (! Schema::hasColumn('spots', 'booking_type')) {
            Schema::table('spots', function (Blueprint $table) {
                $table->enum('booking_type', ['none', 'cottage'])->default('none');
            });
        }

        if (! Schema::hasColumn('spots', 'available_cottages')) {
            Schema::table('spots', function (Blueprint $table) {
                $table->integer('available_cottages')->nullable();
            });
        }

        if (! Schema::hasColumn('spots', 'cottage_price')) {
            Schema::table('spots', function (Blueprint $table) {
                $table->decimal('cottage_price', 10, 2)->nullable();
            });
        }
    }

    public function down(): void
    {
        $columns = collect([
            'latitude',
            'longitude',
            'map_link',
            'booking_type',
            'available_cottages',
            'cottage_price',
        ])->filter(fn (string $column) => Schema::hasColumn('spots', $column))->all();

        if ($columns !== []) {
            Schema::table('spots', function (Blueprint $table) use ($columns) {
                $table->dropColumn($columns);
            });
        }
    }
};
