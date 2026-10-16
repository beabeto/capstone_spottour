<?php

namespace App\Support;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class BookingNotice
{
    public static function send(int $userId, string $message, array $links = [], string $category = 'booking'): void
    {
        if ($userId <= 0) {
            return;
        }

        Notification::create(array_merge([
            'user_id' => $userId,
            'sender_id' => Auth::id(),
            'message' => $message,
            'is_read' => 0,
        ], $links, [
            'category' => $category,
        ]));
    }

    public static function admins(string $message, array $links = [], array $except = [], string $category = 'booking'): void
    {
        $skip = array_map('intval', $except);

        User::where('role', 'admin')->pluck('id')->each(function ($id) use ($message, $links, $skip, $category) {
            if (in_array((int) $id, $skip, true)) {
                return;
            }

            self::send((int) $id, $message, $links, $category);
        });
    }
}
