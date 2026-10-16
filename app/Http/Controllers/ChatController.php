<?php

namespace App\Http\Controllers;

use App\Models\ChatMessage;
use App\Models\ChatThread;
use App\Models\Notification;
use App\Models\User;
use App\Support\SpotTourMail;
use App\Support\TravellerAssistant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ChatController extends Controller
{
    public function state(Request $request)
    {
        $thread = $this->travellerThread();
        if ($request->boolean('open')) {
            $thread->update(['traveller_unread' => 0]);
        }

        return response()->json($this->payload($thread->fresh()));
    }

    public function send(Request $request, TravellerAssistant $assistant)
    {
        $data = $request->validate(['message' => 'required|string|max:1000']);
        $thread = $this->travellerThread();
        $text = trim($data['message']);

        $this->say($thread, 'traveller', $text, Auth::id());

        if ($this->backToHelper($text) && $thread->status === 'with_staff') {
            $thread->update([
                'status' => 'bot',
                'assignee_role' => null,
                'assignee_id' => null,
            ]);
        }

        $thread->refresh();

        if ($thread->status === 'with_staff') {
            $thread->increment('staff_unread');
            $thread->update(['last_message_at' => now()]);
            $this->notifyStaff($thread, $text);
        } else {
            $result = $assistant->respond(Auth::user(), $text);
            if ($result['refer'] === 'admin') {
                $this->handoff($thread, 'admin', null, $text);
            } elseif ($result['refer'] === 'owner' && !empty($result['listing']['owner_id'])) {
                $this->handoff($thread, 'owner', $result['listing'], $text);
            } elseif (!empty($result['listing'])) {
                $thread->update([
                    'listing_type' => $result['listing']['type'],
                    'listing_id' => $result['listing']['id'],
                    'listing_name' => $result['listing']['name'],
                    'assignee_id' => $result['listing']['owner_id'],
                ]);
            }

            $this->say($thread, 'bot', $result['text']);
            $thread->update([
                'traveller_unread' => 0,
                'last_message_at' => now(),
            ]);
        }

        return response()->json($this->payload($thread->fresh()));
    }

    public function adminIndex()
    {
        abort_unless(Auth::user()?->role === 'admin', 403);

        return view('chat.inbox', [
            'workspace' => 'admin',
            'threads' => $this->staffThreads('admin'),
            'active' => null,
        ]);
    }

    public function adminShow(ChatThread $thread)
    {
        abort_unless(Auth::user()?->role === 'admin' && $thread->assignee_role === 'admin', 403);
        $thread->update(['staff_unread' => 0]);

        return view('chat.inbox', [
            'workspace' => 'admin',
            'threads' => $this->staffThreads('admin'),
            'active' => $thread->load('traveller', 'messages.sender'),
        ]);
    }

    public function ownerIndex()
    {
        abort_unless(Auth::user()?->role === 'owner', 403);

        return view('chat.inbox', [
            'workspace' => 'owner',
            'threads' => $this->staffThreads('owner'),
            'active' => null,
        ]);
    }

    public function ownerShow(ChatThread $thread)
    {
        abort_unless(Auth::user()?->role === 'owner' && (int) $thread->assignee_id === (int) Auth::id(), 403);
        $thread->update(['staff_unread' => 0]);

        return view('chat.inbox', [
            'workspace' => 'owner',
            'threads' => $this->staffThreads('owner'),
            'active' => $thread->load('traveller', 'messages.sender'),
        ]);
    }

    public function reply(Request $request, ChatThread $thread)
    {
        $role = Auth::user()?->role;
        $allowed = ($role === 'admin' && $thread->assignee_role === 'admin')
            || ($role === 'owner' && (int) $thread->assignee_id === (int) Auth::id());
        abort_unless($allowed, 403);

        $data = $request->validate(['message' => 'required|string|max:1000']);
        $this->say($thread, $role, trim($data['message']), Auth::id());
        $thread->update([
            'traveller_unread' => $thread->traveller_unread + 1,
            'staff_unread' => 0,
            'last_message_at' => now(),
        ]);

        $roleLabel = $role === 'admin' ? 'Admin' : 'Owner';
        $message = $roleLabel.' '.Auth::user()->name.' replied in your SpotTour chat.';

        Notification::create([
            'user_id' => $thread->user_id,
            'sender_id' => Auth::id(),
            'message' => $message,
            'is_read' => 0,
            'category' => 'chat',
            'chat_thread_id' => $thread->id,
        ]);

        $traveller = $thread->traveller ?: User::find($thread->user_id);
        SpotTourMail::notify(
            $traveller,
            'SpotTour chat reply from '.$roleLabel,
            "Hi {$traveller?->name},\n\n{$roleLabel} ".Auth::user()->name." replied to your SpotTour chat:\n\n".trim($data['message'])."\n\nOpen SpotTour and check Help chat or your notifications.\n"
        );

        return back()->with('success', 'Reply sent. The traveller was notified on the web and by email.');
    }

    private function travellerThread(): ChatThread
    {
        abort_unless(Auth::user()?->role === 'user', 403);

        $thread = ChatThread::firstOrCreate(
            ['user_id' => Auth::id()],
            ['status' => 'bot', 'last_message_at' => now()]
        );

        if ($thread->messages()->doesntExist()) {
            $this->say($thread, 'bot', "Hi. I can help you book, find a place, or check your reservations.\n\nTap a suggested question below, or type your own. If you need a person, ask for an admin or the owner of a listing.");
        }

        return $thread;
    }

    private function say(ChatThread $thread, string $role, string $body, ?int $senderId = null): void
    {
        ChatMessage::create([
            'chat_thread_id' => $thread->id,
            'sender_id' => $senderId,
            'sender_role' => $role,
            'body' => $body,
        ]);
    }

    private function handoff(ChatThread $thread, string $role, ?array $listing, string $question): void
    {
        $thread->update([
            'status' => 'with_staff',
            'assignee_role' => $role,
            'assignee_id' => $role === 'owner' ? ($listing['owner_id'] ?? null) : null,
            'listing_type' => $listing['type'] ?? $thread->listing_type,
            'listing_id' => $listing['id'] ?? $thread->listing_id,
            'listing_name' => $listing['name'] ?? $thread->listing_name,
            'staff_unread' => $thread->staff_unread + 1,
            'last_message_at' => now(),
        ]);

        $this->notifyStaff($thread->fresh(), $question);
    }

    private function notifyStaff(ChatThread $thread, string $question): void
    {
        $traveller = $thread->traveller ?: User::find($thread->user_id);
        $preview = Str::limit($question, 90);
        $message = ($traveller->name ?? 'A traveller').' needs help in chat: '.$preview;
        $ids = $thread->assignee_role === 'owner' && $thread->assignee_id
            ? collect([(int) $thread->assignee_id])
            : User::where('role', 'admin')->pluck('id');

        foreach ($ids as $id) {
            if ((int) $id === (int) Auth::id()) {
                continue;
            }

            Notification::create([
                'user_id' => $id,
                'sender_id' => Auth::id(),
                'message' => $message,
                'is_read' => 0,
                'category' => 'chat',
                'chat_thread_id' => $thread->id,
            ]);

            SpotTourMail::notify(
                User::find($id),
                'SpotTour traveller needs chat help',
                "Hi,\n\n{$message}\n\nOpen SpotTour → Traveller chat to reply.\n"
            );
        }
    }

    private function staffThreads(string $workspace)
    {
        return ChatThread::with(['traveller', 'latestMessage'])
            ->where('assignee_role', $workspace === 'owner' ? 'owner' : 'admin')
            ->when($workspace === 'owner', fn ($query) => $query->where('assignee_id', Auth::id()))
            ->latest('last_message_at')
            ->get();
    }

    private function payload(ChatThread $thread): array
    {
        $thread->load(['messages.sender']);

        return [
            'status' => $thread->status,
            'assignee' => $thread->assignee_role,
            'listing' => $thread->listing_name,
            'unread' => (int) $thread->traveller_unread,
            'suggestions' => $thread->status === 'with_staff'
                ? ['Back to the helper']
                : [
                    'How do I book a hotel?',
                    'How do I book a restaurant?',
                    'How do I pay with GCash?',
                    'Show my bookings',
                    'Talk to an admin',
                    'Talk to an owner',
                ],
            'messages' => $thread->messages->map(fn (ChatMessage $message) => [
                'id' => $message->id,
                'role' => $message->sender_role,
                'label' => $this->messageLabel($message),
                'body' => $message->body,
                'at' => optional($message->created_at)->diffForHumans(),
            ])->values(),
        ];
    }

    private function messageLabel(ChatMessage $message): string
    {
        return match ($message->sender_role) {
            'traveller' => 'You',
            'bot' => 'SpotTour helper',
            'admin' => 'Admin'.($message->sender?->name ? ' · '.$message->sender->name : ''),
            'owner' => 'Owner'.($message->sender?->name ? ' · '.$message->sender->name : ''),
            default => ucfirst((string) $message->sender_role),
        };
    }

    private function backToHelper(string $text): bool
    {
        return (bool) preg_match('/back to the helper|chat ?bot|helper na/iu', $text);
    }
}
