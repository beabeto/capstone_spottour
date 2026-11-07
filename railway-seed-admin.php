<?php

require __DIR__.'/vendor/autoload.php';

$app = require __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

$email = 'admin@spottour.com';
$password = 'Admin12345';
$hash = Hash::make($password);
$now = now();

$existing = DB::table('users')->where('email', $email)->first();

if ($existing) {
    DB::table('users')->where('id', $existing->id)->update([
        'password' => $hash,
        'role' => 'admin',
        'updated_at' => $now,
    ]);
} else {
    $row = [
        'name' => 'SpotTour Admin',
        'email' => $email,
        'password' => $hash,
        'role' => 'admin',
        'created_at' => $now,
        'updated_at' => $now,
    ];

    if (Schema::hasColumn('users', 'gender')) {
        $row['gender'] = 'prefer_not_to_say';
    }
    if (Schema::hasColumn('users', 'birth_date')) {
        $row['birth_date'] = '2000-01-01';
    }

    DB::table('users')->insert($row);
}

echo "Admin ready: {$email} / {$password}".PHP_EOL;
