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

        $filtered = [];

        foreach ($data as $key => $value) {
            if (! Schema::hasColumn($table, $key)) {
                continue;
            }

            $filtered[$key] = $value === '' ? null : $value;
        }

        return $filtered;
    }
}
