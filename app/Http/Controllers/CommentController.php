<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\SpotComment;
use App\Models\CommentReply;
use App\Models\CommentRating;
use App\Models\CommentReaction;
use App\Models\CommentReplyRating;
use App\Models\Notification;

class CommentController extends Controller
{
   public function rate(Request $request)
{
    $request->validate([

    'comment_id' => 'required',

    'rating' => 'required|integer|between:1,5'

]);

    CommentRating::updateOrCreate(

        [
            'comment_id' => $request->comment_id,
            'user_id' => Auth::id()
        ],

        [
            'rating' => $request->rating
        ]

    );

    $comment = SpotComment::find(
        $request->comment_id
    );

    if($comment && $comment->user_id != Auth::id())
    {
        Notification::create([

    'user_id' => $comment->user_id,

    'sender_id' => Auth::id(),

    'spot_id' => $comment->spot_id,

    'comment_id' => $comment->id,

    'message' => Auth::user()->name .
                 ' rated your comment.',

    'is_read' => 0

]);
    }

   return back()->with(
    'success',
    ' You rated this comment ' .
    $request->rating .
    ' star(s).'
);
}

    public function reply(Request $request)
{
    $request->validate([

        'comment_id' => 'required|exists:spot_comments,id',

        'reply' => 'required|string|max:1000',
        'image' => 'nullable|image|mimes:jpeg,png,webp|max:5120'

    ]);

    $image = $request->hasFile('image') ? time().'_'.$request->file('image')->getClientOriginalName() : null;
    if ($image) $request->file('image')->move(public_path('uploads/comments'), $image);
    $reply = CommentReply::create([

        'comment_id' => $request->comment_id,

        'user_id' => Auth::id(),

        'parent_reply_id' => $request->parent_reply_id,

        'reply' => $request->reply,
        'image' => $image

    ]);

    $comment = SpotComment::find(
        $request->comment_id
    );

    if(
        $comment &&
        $comment->user_id != Auth::id()
    )
    {
        Notification::create([

            'user_id' => $comment->user_id,

            'sender_id' => Auth::id(),

            'spot_id' => $comment->spot_id,

            'comment_id' => $comment->id,

            'message' => Auth::user()->name .
                         ' replied to your comment.',

            'is_read' => 0

        ]);
    }

    return back()->with(
        'success',
        ' Reply posted.'
    );
}

public function like($id)
{
    CommentReaction::updateOrCreate(

        [
            'comment_id' => $id,
            'user_id' => Auth::id()
        ],

        [
            'type' => 'like'
        ]

    );

    $comment = SpotComment::find($id);

    if($comment && $comment->user_id != Auth::id())
    {
        Notification::create([

    'user_id' => $comment->user_id,

    'sender_id' => Auth::id(),

    'spot_id' => $comment->spot_id,

    'comment_id' => $comment->id,

    'message' => Auth::user()->name .
                 ' liked your comment.',

    'is_read' => 0

]);
    }

    return back()->with(
    'success',
    ' Comment liked.'
);
}

   public function dislike($id)
{
    CommentReaction::updateOrCreate(

        [
            'comment_id' => $id,
            'user_id' => Auth::id()
        ],

        [
            'type' => 'dislike'
        ]

    );

    $comment = SpotComment::find($id);

    if($comment && $comment->user_id != Auth::id())
    {
        Notification::create([

    'user_id' => $comment->user_id,

    'sender_id' => Auth::id(),

    'spot_id' => $comment->spot_id,

    'comment_id' => $comment->id,

    'message' => Auth::user()->name .
                 ' disliked your comment.',

    'is_read' => 0

]);
    }

    return back()->with(
    'success',
    ' Comment disliked.'
);
}

public function edit($id)
{
    $comment = SpotComment::findOrFail($id);

    return view(
        'user.edit-comment',
        compact('comment')
    );
}

public function update(
    Request $request,
    $id
)

{
    $request->validate([

    'comment' => 'required|max:1000'

]);

    $comment = SpotComment::findOrFail($id);

    if(Auth::id() != $comment->user_id)
    {
        abort(403);
    }

    $comment->update([

        'comment' => $request->comment

    ]);

    return redirect(
    '/spots/' . $comment->spot_id
)->with(
    'success',
    ' Comment updated.'
);
}

public function delete($id)
{
    $comment = SpotComment::findOrFail($id);

    if(Auth::id() != $comment->user_id)
    {
        abort(403);
    }

    $comment->delete();

    return back()->with(
    'success',
    ' Comment deleted.'
);
}

public function deleteReply($id)
{
    $reply = CommentReply::findOrFail($id);

    if(Auth::id() != $reply->user_id)
    {
        abort(403);
    }

    $reply->delete();

    return back()->with(
    'success',
    ' Reply deleted.'
);
}

public function editReply($id)
{
    $reply = CommentReply::findOrFail($id);

    return view(
        'user.edit-reply',
        compact('reply')
    );


}

public function updateReply(
    Request $request,
    $id
)
{
    $request->validate([

    'reply' => 'required|max:1000'

]);

    $reply = CommentReply::findOrFail($id);

    if(Auth::id() != $reply->user_id)
    {
        abort(403);
    }

    $reply->update([

        'reply' => $request->reply

    ]);

    return back()->with(
    'success',
    ' Reply updated.'
);
}

public function rateReply(Request $request)
{
    $request->validate(['reply_id' => 'required|exists:comment_replies,id', 'rating' => 'required|integer|between:1,5']);
    CommentReplyRating::updateOrCreate(
        ['reply_id' => $request->reply_id, 'user_id' => Auth::id()],
        ['rating' => $request->rating]
    );
    return back()->with('success', 'Reply rating saved.');
}
}
