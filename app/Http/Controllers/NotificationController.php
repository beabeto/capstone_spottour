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

        Notification::where('user_id', Auth::id())
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return view(
            'user.notifications',
            compact('notifications')
        );
        
    }

public function open($id)
{
    $notification = Notification::where('user_id', Auth::id())->findOrFail($id);

    $notification->update([
        'is_read' => 1
    ]);

    if ($notification->category === 'chat' && $notification->chat_thread_id) {
        $role = Auth::user()->role;

        if ($role === 'admin') {
            return redirect('/admin/chats/'.$notification->chat_thread_id);
        }

        if ($role === 'owner') {
            return redirect('/owner/chats/'.$notification->chat_thread_id);
        }

        return redirect('/user?chat=1');
    }

    if ($notification->category === 'listing') {
        $role = Auth::user()->role;
        $prefix = $role === 'admin' ? '/admin' : '/owner';

        if ($notification->food_id) {
            return redirect($prefix.'/foods/'.$notification->food_id);
        }

        if ($notification->hotel_id) {
            return redirect($prefix.'/hotels/'.$notification->hotel_id);
        }

        if ($notification->spot_id) {
            return redirect($prefix.'/spots/'.$notification->spot_id);
        }
    }

    $message = strtolower((string) $notification->message);
    $isBooking = $notification->category === 'booking'
        || str_contains($message, 'booking')
        || str_contains($message, 'reservation')
        || str_contains($message, 'payment')
        || str_contains($message, 'paid');

    if ($isBooking) {
        $role = Auth::user()->role;

        if ($role === 'admin') {
            return redirect('/admin/bookings');
        }

        if ($role === 'owner') {
            return redirect('/owner/bookings');
        }

        return redirect('/user/bookings');
    }

    if($notification->food_id)
    {
        return redirect('/foods/' . $notification->food_id . ($notification->comment_id ? '#comment'.$notification->comment_id : ''));
    }

    if($notification->hotel_id)
    {
        return redirect('/hotels/' . $notification->hotel_id . ($notification->comment_id ? '#comment'.$notification->comment_id : ''));
    }

    if($notification->spot_id)
    {
        return redirect('/spots/' . $notification->spot_id . ($notification->comment_id ? '#comment'.$notification->comment_id : ''));
    }

    return redirect('/user');
}

    public function adminIndex()
    {
        abort_unless(Auth::user()?->role === 'admin', 403);

        $notifications = Notification::with('sender')
            ->where('user_id', Auth::id())
            ->latest()
            ->get();

        Notification::where('user_id', Auth::id())
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return view('admin.notifications', compact('notifications'));
    }

    public function ownerIndex()
    {
        $notifications = Notification::with('sender')
            ->where('user_id', Auth::id())
            ->latest()
            ->get();

        Notification::where('user_id', Auth::id())
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return view('owner.notifications', compact('notifications'));
    }

    public function delete($id)
    {
        Notification::where('user_id', Auth::id())->findOrFail($id)
            ->delete();

        return back();
    }
}
