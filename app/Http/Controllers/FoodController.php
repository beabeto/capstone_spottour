<?php

namespace App\Http\Controllers;

use App\Models\Food;
use App\Models\FoodMenu;
use App\Models\FoodComment;
use App\Models\FoodReply;
use App\Models\FoodRating;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FoodController extends Controller
{
    public function ownerIndex()
    {
        $foods = Food::where(
            'owner_id',
            Auth::id()
        )->latest()->get();

        return view(
            'owner.foods',
            compact('foods')
        );
    }

    public function ownerShow($id)
{
    $food = Food::with(
        'menus',
        'comments.user'
    )->findOrFail($id);

    return view(
        'owner.food-details',
        compact('food')
    );
}



    public function create()
    {
        return view(
            'owner.create-food'
        );
    }

    public function store(Request $request)
    {
        $request->validate([

            'name' => 'required',

            'location' => 'required',

            'description' => 'required'

        ]);

        $image = null;

        if($request->hasFile('image'))
        {
            $file = $request->file('image');

            $image =
                time().'_'.
                $file->getClientOriginalName();

            $file->move(
                public_path(
                    'uploads/foods'
                ),
                $image
            );
        }

        Food::create([

            'owner_id' => Auth::id(),

            'name' => $request->name,

            'location' => $request->location,

            'description' => $request->description,

            'available_tables' =>
                $request->available_tables,

            'venue_capacity' =>
                $request->venue_capacity,

            'map_link' =>
                $request->map_link,

            'image' => $image

        ]);

        return redirect(
            '/owner/foods'
        )->with(
            'success',
            'Food Establishment Added.'
        );
    }

    

    public function menus($id)
{
    $food = Food::findOrFail($id);

    $menus = FoodMenu::where(
        'food_id',
        $id
    )->get();

    return view(
        'owner.food-menus',
        compact(
            'food',
            'menus'
        )
    );
}

public function createMenu($foodId)
{
    $food = Food::findOrFail($foodId);

    return view(
        'owner.create-menu',
        compact('food')
    );
}

public function storeMenu(Request $request, $foodId)
{
    $image = null;

    if($request->hasFile('image'))
    {
        $file = $request->file('image');

        $image =
            time().'_'.
            $file->getClientOriginalName();

        $file->move(
            public_path('uploads/menus'),
            $image
        );
    }

    FoodMenu::create([

        'food_id' => $foodId,

        'name' => $request->name,

        'description' => $request->description,

        'price' => $request->price,

        'category' => $request->category,

        'image' => $image

    ]);

    return redirect(
        '/owner/foods/'.$foodId.'/menus'
    )->with(
        'success',
        'Menu Item Added Successfully.'
    );
}

public function deleteMenu($id)
{
    $menu = FoodMenu::findOrFail($id);

    $foodId = $menu->food_id;

    $menu->delete();

    return redirect(
        '/owner/foods/'.$foodId.'/menus'
    )->with(
        'success',
        'Menu Item Deleted.'
    );
}

public function editMenu($id)
{
    $menu = FoodMenu::findOrFail($id);

    return view(
        'owner.edit-menu',
        compact('menu')
    );
}

public function updateMenu(
    Request $request,
    $id
)
{
    $menu = FoodMenu::findOrFail($id);

    $image = $menu->image;

    if($request->hasFile('image'))
    {
        $file = $request->file('image');

        $image =
            time().'_'.
            $file->getClientOriginalName();

        $file->move(
            public_path('uploads/menus'),
            $image
        );
    }

    $menu->update([

        'name' => $request->name,

        'category' => $request->category,

        'price' => $request->price,

        'description' => $request->description,

        'image' => $image

    ]);

    return redirect(
        '/owner/foods/'.$menu->food_id.'/menus'
    )->with(
        'success',
        'Menu Updated Successfully.'
    );
}

public function adminIndex()
{
    $foods = Food::with('owner')
        ->latest()
        ->get();

    return view(
        'admin.foods',
        compact('foods')
    );
}

public function adminShow($id)
{
    $food = Food::with(
        'owner',
        'menus',
        'comments.user',
        'comments.ratings',
        'ratings'
    )->findOrFail($id);

    return view(
        'admin.food-details',
        compact('food')
    );
}

public function approve($id)
{
    $food = Food::findOrFail($id);

    $food->update([
        'status' => 'approved'
    ]);

    return back()->with(
        'success',
        'Food Establishment Approved.'
    );
}

public function reject($id)
{
    $food = Food::findOrFail($id);

    $food->update([
        'status' => 'rejected'
    ]);

    return back()->with(
        'success',
        'Food Establishment Rejected.'
    );
}

public function adminDelete($id)
{
    $food = Food::findOrFail($id);

    $food->delete();

    return back()->with(
        'success',
        'Food Establishment Deleted.'
    );
}

public function comment(Request $request)
{
    FoodComment::create([

        'food_id' => $request->food_id,

        'user_id' => Auth::id(),

        'comment' => $request->comment

    ]);

    return back()->with(
        'success',
        'Comment Added.'
    );
}

public function userIndex()
{
    $foods = Food::where('status', 'approved')
        ->latest()
        ->get();

    return view('user.foods', compact('foods'));
}

public function userShow($id)
{
    $food = Food::with(
        'menus',
        'comments.user',
        'ratings'
    )->findOrFail($id);

    return view('user.food-details', compact('food'));
}

public function show($id)
{
    $food = Food::with(
        'menus',
        'comments.user',
        'ratings'
    )->findOrFail($id);

    return view('user.food-details', compact('food'));
}

public function reply(Request $request)
{
    $request->validate([
        'comment_id' => 'required|exists:food_comments,id',
        'reply' => 'required|string',
    ]);

    FoodReply::create([
        'food_comment_id' => $request->comment_id,
        'user_id' => Auth::id(),
        'parent_reply_id' => $request->parent_reply_id,
        'reply' => $request->reply,
    ]);

    return back()->with(
        'success',
        'Reply Added Successfully.'
    );
}

public function editComment($id)
{
    $comment = FoodComment::findOrFail($id);

    if ($comment->user_id != Auth::id()) {
        abort(403);
    }

    return view('user.edit-food-comment', compact('comment'));
}

public function updateComment(Request $request, $id)
{
    $comment = FoodComment::findOrFail($id);

    if ($comment->user_id != Auth::id()) {
        abort(403);
    }

    $comment->update([
        'comment' => $request->comment,
    ]);

    return redirect()->back()->with(
        'success',
        'Comment Updated Successfully.'
    );
}

public function deleteComment($id)
{
    $comment = FoodComment::findOrFail($id);

    if ($comment->user_id != Auth::id()) {
        abort(403);
    }

    $comment->delete();

    return back()->with(
        'success',
        'Comment Deleted Successfully.'
    );
}

public function rate(Request $request)
{
    $request->validate([
        'food_id' => 'required|exists:foods,id',
        'rating' => 'required|integer|min:1|max:5',
    ]);

    // Check if the user has already rated this food
    $rating = FoodRating::where('food_id', $request->food_id)
        ->where('user_id', Auth::id())
        ->first();

    if ($rating) {

        $rating->update([
            'rating' => $request->rating,
        ]);

    } else {

        FoodRating::create([
            'food_id' => $request->food_id,
            'user_id' => Auth::id(),
            'rating' => $request->rating,
        ]);

    }

    return back()->with(
        'success',
        'Rating submitted successfully.'
    );
}
}