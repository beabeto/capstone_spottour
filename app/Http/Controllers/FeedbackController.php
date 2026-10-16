<?php

namespace App\Http\Controllers;

use App\Models\Feedback;
use App\Models\Notification;
use App\Models\User;
use App\Support\SpotTourMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class FeedbackController extends Controller
{
    public function userIndex()
    {
        $feedbacks = Feedback::with('admin')
            ->where('user_id', Auth::id())
            ->latest()
            ->get();

        return view('user.feedback', compact('feedbacks'));
    }

    public function ownerIndex()
    {
        $feedbacks = Feedback::with('admin')
            ->where('user_id', Auth::id())
            ->latest()
            ->get();

        return view('owner.feedback', compact('feedbacks'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'subject' => 'required|max:255',
            'message' => 'required',
        ]);

        $feedback = Feedback::create([
            'user_id' => Auth::id(),
            'subject' => $request->subject,
            'message' => $request->message,
            'status' => 'pending',
        ]);

        $preview = Str::limit($request->subject, 80);
        foreach (User::where('role', 'admin')->get() as $admin) {
            Notification::create([
                'user_id' => $admin->id,
                'sender_id' => Auth::id(),
                'message' => Auth::user()->name.' sent feedback: '.$preview,
                'is_read' => 0,
                'category' => 'feedback',
            ]);

            SpotTourMail::notify(
                $admin,
                'New SpotTour feedback',
                "Hi {$admin->name},\n\n".Auth::user()->name." sent feedback:\n\nSubject: {$feedback->subject}\n\n{$feedback->message}\n\nOpen SpotTour → Feedback to reply.\n"
            );
        }

        return back()->with('success', 'Feedback sent. The admin was notified on the web and by email.');
    }

    public function adminIndex()
    {
        $feedbacks = Feedback::with(['user', 'admin'])
            ->latest()
            ->get();

        return view('admin.feedback', compact('feedbacks'));
    }

    public function reply(Request $request, $id)
    {
        $this->ensureAdministrator();

        $request->validate([
            'reply' => 'required',
        ]);

        $feedback = Feedback::with('user')->findOrFail($id);
        $admin = Auth::user();

        $feedback->update([
            'reply' => $request->reply,
            'replied_by' => $admin->id,
            'replied_at' => now(),
            'status' => 'replied',
        ]);

        $message = 'Admin '.$admin->name.' replied to your feedback "'.$feedback->subject.'".';

        Notification::create([
            'user_id' => $feedback->user_id,
            'sender_id' => $admin->id,
            'message' => $message,
            'is_read' => 0,
            'category' => 'feedback',
        ]);

        SpotTourMail::notify(
            $feedback->user,
            'Admin replied to your SpotTour feedback',
            "Hi {$feedback->user?->name},\n\nAdmin {$admin->name} replied to your feedback \"{$feedback->subject}\":\n\n{$request->reply}\n\nYou can also read this reply in SpotTour → Feedback.\n"
        );

        return back()->with('success', 'Reply sent. The traveller was notified on the web and by email.');
    }

    public function resolve($id)
    {
        $this->ensureAdministrator();

        Feedback::findOrFail($id)->update([
            'status' => 'resolved',
        ]);

        return back()->with('success', 'Feedback marked as resolved.');
    }

    public function update(Request $request, $id)
    {
        $this->ensureAdministrator();

        $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        Feedback::findOrFail($id)->update([
            'subject' => $request->subject,
            'message' => $request->message,
        ]);

        return back()->with('success', 'Feedback record updated.');
    }

    public function delete($id)
    {
        $this->ensureAdministrator();

        Feedback::findOrFail($id)->delete();

        return back()->with('success', 'Feedback deleted.');
    }

    private function ensureAdministrator(): void
    {
        abort_unless(Auth::check() && Auth::user()->role === 'admin', 403);
    }
}
