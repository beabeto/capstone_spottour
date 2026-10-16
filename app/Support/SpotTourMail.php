<?php

namespace App\Support;

use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Throwable;

class SpotTourMail
{
    public static function notify(?User $user, string $subject, string $body): void
    {
        if (! $user?->email) {
            return;
        }

        try {
            Mail::raw($body, function ($message) use ($user, $subject) {
                $message->to($user->email, $user->name)->subject($subject);
            });
        } catch (Throwable) {
            // Keep the web flow working if mail is not configured.
        }
    }
}
