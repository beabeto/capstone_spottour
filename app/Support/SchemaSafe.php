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

        $columns = Schema::getColumnListing($table);
        $filtered = [];

        foreach ($data as $key => $value) {
            if (! in_array($key, $columns, true)) {
                continue;
            }

            $filtered[$key] = $value === '' ? null : $value;
        }

        return $filtered;
    }
}
