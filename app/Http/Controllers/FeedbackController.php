<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Feedback;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;

class FeedbackController extends Controller
{
    // ================= USER =================

   public function userIndex()
{
    $feedbacks = Feedback::where(
        'user_id',
        Auth::id()
    )
    ->latest()
    ->get();

    return view(
        'user.feedback',
        compact('feedbacks')
    );
}

    // ================= OWNER =================

    public function ownerIndex()
    {
        $feedbacks = Feedback::where(
            'user_id',
            Auth::id()
        )
        ->latest()
        ->get();

        return view(
            'owner.feedback',
            compact('feedbacks')
        );
    }

    // ================= SEND FEEDBACK =================

    public function store(Request $request)
    {
        $request->validate([

            'subject' => 'required|max:255',

            'message' => 'required'

        ]);

        Feedback::create([

            'user_id' => Auth::id(),

            'subject' => $request->subject,

            'message' => $request->message,

            'status' => 'pending'

        ]);

        return back()->with(
            'success',
            'Feedback sent successfully.'
        );
    }

    // ================= ADMIN =================

    public function adminIndex()
    {
        $feedbacks = Feedback::with('user')
            ->latest()
            ->get();

        return view(
            'admin.feedback',
            compact('feedbacks')
        );
    }

    // ================= REPLY =================

    public function reply(Request $request, $id)
{
    $request->validate([

        'reply' => 'required'

    ]);

    $feedback = Feedback::findOrFail($id);

    $feedback->update([

        'reply' => $request->reply,

        'status' => 'replied'

    ]);

    Notification::create([

        'user_id' => $feedback->user_id,

        'sender_id' => Auth::id(),

        'message' => '💬 Your feedback has been replied to by the administrator.',

        'is_read' => 0

    ]);

    return back()->with(
        'success',
        'Reply sent successfully.'
    );
}

    // ================= RESOLVE =================

    public function resolve($id)
    {
        $feedback = Feedback::findOrFail($id);

        $feedback->update([

            'status' => 'resolved'

        ]);

        return back()->with(
            'success',
            'Feedback marked as resolved.'
        );
    }

    // ================= DELETE =================

    public function delete($id)
    {
        Feedback::findOrFail($id)->delete();

        return back()->with(
            'success',
            'Feedback deleted.'
        );
    }
}