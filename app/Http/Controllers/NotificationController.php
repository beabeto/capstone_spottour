<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Notification::with(
            'sender'
        )
        ->where(
            'user_id',
            Auth::id()
        )
        ->latest()
        ->get();

        return view(
            'user.notifications',
            compact('notifications')
        );
        
    }

    public function open($id)
{
    $notification = Notification::findOrFail($id);

    $notification->update([
        'is_read' => 1
    ]);

    if($notification->food_id)
    {
        return redirect(
            '/foods/' .
            $notification->food_id .
            '#comment' .
            $notification->comment_id
        );
    }

    if($notification->hotel_id)
    {
        return redirect(
            '/hotels/' .
            $notification->hotel_id .
            '#comment' .
            $notification->comment_id
        );
    }

    if($notification->spot_id)
    {
        return redirect(
            '/spots/' .
            $notification->spot_id .
            '#comment' .
            $notification->comment_id
        );
    }

    return redirect('/user');
}

    public function delete($id)
    {
        Notification::findOrFail($id)
            ->delete();

        return back();
    }
}