<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ListingPersist
{
    public static function create(string $table, array $data): int
    {
        $row = self::row($table, $data);

        if (! isset($row['name'])) {
            throw new \RuntimeException("The {$table} table is missing a name column, so the listing cannot be saved.");
        }

        try {
            return (int) DB::table($table)->insertGetId($row);
        } catch (\Throwable $e) {
            report($e);

            $minimal = array_intersect_key($row, array_flip([
                'user_id', 'owner_id', 'name', 'location', 'description', 'status', 'created_at', 'updated_at',
            ]));

            try {
                return (int) DB::table($table)->insertGetId($minimal);
            } catch (\Throwable $retry) {
                throw $e;
            }
        }
    }

    private static function row(string $table, array $data): array
    {
        $columns = self::columns($table);

        if ($columns === []) {
            throw new \RuntimeException("The {$table} table is missing. Redeploy so the live database can be prepared.");
        }

        $row = [];
        foreach ($data as $key => $value) {
            if (! in_array($key, $columns, true)) {
                continue;
            }
            if ($value === '' || $value === null) {
                continue;
            }
            if (is_array($value)) {
                if ($value === []) {
                    continue;
                }
                $value = json_encode(array_values($value));
            }
            $row[$key] = $value;
        }

        $now = now();
        if (in_array('created_at', $columns, true)) {
            $row['created_at'] = $now;
        }
        if (in_array('updated_at', $columns, true)) {
            $row['updated_at'] = $now;
        }

        return $row;
    }

    public static function columns(string $table): array
    {
        try {
            if (! Schema::hasTable($table)) {
                return [];
            }

            return Schema::getColumnListing($table);
        } catch (\Throwable $e) {
            report($e);

            return [];
        }
    }

    public static function error(\Throwable $e, string $thing): string
    {
        $message = trim($e->getMessage());
        if ($message === '' && $e->getPrevious()) {
            $message = trim($e->getPrevious()->getMessage());
        }
        if ($message === '') {
            $message = class_basename($e).' in '.basename($e->getFile()).' line '.$e->getLine();
        }

        return 'Could not save this '.$thing.': '.$message;
    }
}
