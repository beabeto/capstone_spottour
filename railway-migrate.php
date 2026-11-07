<?php

require __DIR__.'/vendor/autoload.php';

$app = require __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

if (! function_exists('markMigrationRan')) {
    function markMigrationRan(string $output): bool
    {
        if (! preg_match('/(\d{4}_\d{2}_\d{2}_\d{6}_[A-Za-z0-9_]+)\s+\.+\s+[0-9.]+ms FAIL/', $output, $match)) {
            return false;
        }

        $name = $match[1];

        if (DB::table('migrations')->where('migration', $name)->exists()) {
            return false;
        }

        $batch = ((int) DB::table('migrations')->max('batch')) + 1;
        DB::table('migrations')->insert([
            'migration' => $name,
            'batch' => $batch,
        ]);

        echo "Marked {$name} as run (already exists).".PHP_EOL;

        return true;
    }
}

for ($i = 0; $i < 100; $i++) {
    $code = Artisan::call('migrate', ['--force' => true]);
    $output = Artisan::output();
    echo $output;

    if ($code === 0) {
        try {
            $email = 'admin@spottour.com';
            $password = 'Admin12345';
            $hash = \Illuminate\Support\Facades\Hash::make($password);
            $now = now();
            $existing = DB::table('users')->where('email', $email)->first();

            if ($existing) {
                DB::table('users')->where('id', $existing->id)->update([
                    'password' => $hash,
                    'role' => 'admin',
                    'updated_at' => $now,
                ]);
            } else {
                DB::table('users')->insert([
                    'name' => 'SpotTour Admin',
                    'email' => $email,
                    'password' => $hash,
                    'role' => 'admin',
                    'gender' => 'prefer_not_to_say',
                    'birth_date' => '2000-01-01',
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }

            echo "Admin ready: {$email}".PHP_EOL;
        } catch (\Throwable $e) {
            echo 'Admin seed skipped: '.$e->getMessage().PHP_EOL;
        }
        exit(0);
    }

    $skip = str_contains($output, 'already exists') || str_contains($output, 'Duplicate column');

    if ($skip && markMigrationRan($output)) {
        continue;
    }

    fwrite(STDERR, $output);
    exit(1);
}

fwrite(STDERR, "Too many migrate retries.".PHP_EOL);
exit(1);
