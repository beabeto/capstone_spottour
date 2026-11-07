<?php

namespace App\Support;

use Illuminate\Support\Facades\Schema;

class SchemaSafe
{
    public static function only(string $table, array $data): array
    {
        if (! Schema::hasTable($table)) {
            return [];
        }

        return array_filter(
            $data,
            fn ($key) => Schema::hasColumn($table, $key),
            ARRAY_FILTER_USE_KEY
        );
    }
}
