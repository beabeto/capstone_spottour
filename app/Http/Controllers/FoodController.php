<?php

namespace App\Http\Controllers;

use App\Models\Food;
use App\Models\User;
use App\Models\FoodMenu;
use App\Models\FoodComment;
use App\Models\FoodReply;
use App\Models\FoodReplyRating;
use App\Models\FoodRating;
use Illuminate\Http\Request;
use App\Support\BookingNotice;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

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
    $food = Food::with([
        'menus',
        'ratings.user',
        'comments.user',
        'comments.replies.user',
        'comments.ratings',
    ])->findOrFail($id);

    return view(
        'owner.food-details',
        compact('food')
    );
}



    public function create()
    {
        abort_if(request()->is('admin/*') && Auth::user()->role !== 'admin', 403);
        $owners = Auth::user()->role === 'admin'
            ? User::where('role', 'owner')->orderBy('name')->get()
            : collect();

        return view('owner.create-food', compact('owners'));
    }

    public function store(Request $request)
    {
        $request->validate([

            'name' => 'required',

            'location' => 'required',

            'description' => 'required',

            'available_tables' => 'required|integer|min:1',

            'venue_capacity' => 'required|integer|min:1',

            'price_per_guest' => 'nullable|numeric|min:0',

            'daily_booking_limit' => 'nullable|integer|min:1',

            'map_link' => 'nullable|url',

            'image' => 'nullable|image|mimes:jpeg,png,webp|max:5120',
            'gallery_images.*' => 'nullable|image|mimes:jpeg,png,webp|max:5120',
            'owner_id' => Auth::user()->role === 'admin' ? 'required|exists:users,id,role,owner' : 'nullable',
            'menus' => 'required|array|min:1',
            'menus.*.name' => 'required|string|max:120',
            'menus.*.category' => ['required', Rule::in(array_keys(FoodMenu::categories()))],
            'menus.*.price' => 'required|numeric|min:0',
            'menus.*.description' => 'nullable|string|max:1000',
            'menus.*.image' => 'nullable|image|max:5120',

        ]);

        $isAdmin = Auth::user()->role === 'admin';

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

        $gallery = [];
        foreach ($request->file('gallery_images', []) as $file) {
            $name = time().'_'.uniqid().'_'.$file->getClientOriginalName();
            $file->move(public_path('uploads/foods'), $name);
            $gallery[] = $name;
        }

        $coords = \App\Support\PlaceMap::locate(
            null,
            null,
            $request->map_link,
            (string) $request->name,
            (string) $request->location
        );

        $food = Food::create([

            'owner_id' => $isAdmin ? $request->integer('owner_id') : Auth::id(),
            'status' => $isAdmin ? 'approved' : 'pending',

            'name' => $request->name,

            'location' => $request->location,

            'description' => $request->description,

            'available_tables' =>
                $request->available_tables,

            'venue_capacity' =>
                $request->integer('venue_capacity'),

            'price_per_guest' => $request->filled('price_per_guest') ? $request->input('price_per_guest') : 0,

            'daily_booking_limit' => $request->filled('daily_booking_limit') ? $request->integer('daily_booking_limit') : null,

            'map_link' =>
                $request->map_link,

            'latitude' => $coords['latitude'],
            'longitude' => $coords['longitude'],

            'image' => $image,
            'gallery_images' => $gallery,

        ]);

        $directory = public_path('uploads/menus');
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        foreach ($request->input('menus') as $index => $menu) {
            $imageName = null;
            $menuImage = $request->file("menus.$index.image");
            if ($menuImage) {
                $imageName = time().'_'.uniqid().'_'.$menuImage->getClientOriginalName();
                $menuImage->move($directory, $imageName);
            }

            FoodMenu::create([
                'food_id' => $food->id,
                'name' => $menu['name'],
                'description' => $menu['description'] ?? null,
                'price' => $menu['price'],
                'category' => $menu['category'],
                'image' => $imageName,
            ]);
        }

        if (!$isAdmin) {
            BookingNotice::admins(
                Auth::user()->name.' submitted the restaurant "'.$food->name.'" for review.',
                ['food_id' => $food->id],
                [],
                'listing'
            );
        }

        return redirect($isAdmin ? '/admin/foods' : '/owner/foods')->with(
            'success',
            'Restaurant saved with its menu.'
        );
    }

    public function edit($id)
    {
        $food = Food::findOrFail($id);
        abort_unless(
            (int) $food->owner_id === (int) Auth::id() || Auth::user()?->role === 'admin',
            403
        );

        $owners = Auth::user()?->role === 'admin'
            ? User::where('role', 'owner')->orderBy('name')->get()
            : collect();

        return view('owner.edit-food', compact('food', 'owners'));
    }

    public function update(Request $request, $id)
    {
        $food = Food::findOrFail($id);
        abort_unless(
            (int) $food->owner_id === (int) Auth::id() || Auth::user()?->role === 'admin',
            403
        );

        $isAdmin = Auth::user()?->role === 'admin';

        $request->validate([
            'name' => 'required|string|max:150',
            'location' => 'required|string|max:255',
            'description' => 'required|string',
            'available_tables' => 'required|integer|min:1',
            'venue_capacity' => 'required|integer|min:1',
            'price_per_guest' => 'nullable|numeric|min:0',
            'daily_booking_limit' => 'nullable|integer|min:1',
            'map_link' => 'nullable|url',
            'image' => 'nullable|image|mimes:jpeg,png,webp|max:5120',
            'gallery_images.*' => 'nullable|image|mimes:jpeg,png,webp|max:5120',
            'owner_id' => $isAdmin ? 'required|exists:users,id,role,owner' : 'nullable',
        ]);

        $image = $food->image;
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $image = time().'_'.$file->getClientOriginalName();
            $file->move(public_path('uploads/foods'), $image);
        }

        $gallery = $food->gallery_images ?? [];
        foreach ($request->file('gallery_images', []) ?: [] as $file) {
            $name = time().'_'.uniqid().'_'.$file->getClientOriginalName();
            $file->move(public_path('uploads/foods'), $name);
            $gallery[] = $name;
        }

        $coords = \App\Support\PlaceMap::locate(
            $food->latitude,
            $food->longitude,
            $request->map_link,
            (string) $request->name,
            (string) $request->location
        );

        $food->update([
            'owner_id' => $isAdmin ? $request->integer('owner_id') : $food->owner_id,
            'name' => $request->name,
            'location' => $request->location,
            'description' => $request->description,
            'available_tables' => $request->integer('available_tables'),
            'venue_capacity' => $request->integer('venue_capacity'),
            'price_per_guest' => $request->filled('price_per_guest') ? $request->input('price_per_guest') : ($food->price_per_guest ?? 0),
            'daily_booking_limit' => $request->filled('daily_booking_limit') ? $request->integer('daily_booking_limit') : null,
            'map_link' => $request->map_link,
            'latitude' => $coords['latitude'] ?? $food->latitude,
            'longitude' => $coords['longitude'] ?? $food->longitude,
            'image' => $image,
            'gallery_images' => $gallery,
            'status' => $isAdmin ? $food->status : 'pending',
        ]);

        if (! $isAdmin) {
            BookingNotice::admins(
                Auth::user()->name.' updated the restaurant "'.$food->name.'" and it needs review.',
                ['food_id' => $food->id],
                [],
                'listing'
            );
        }

        return redirect($isAdmin ? '/admin/foods/'.$food->id : '/owner/foods/'.$food->id)
            ->with('success', 'Restaurant updated successfully.');
    }

    public function delete($id)
    {
        $food = Food::findOrFail($id);
        abort_unless((int) $food->owner_id === (int) Auth::id() || Auth::user()?->role === 'admin', 403);

        $food->delete();

        return redirect(Auth::user()?->role === 'admin' ? '/admin/foods' : '/owner/foods')
            ->with('success', 'Restaurant deleted successfully.');
    }

    public function menus($id)
{
    $food = Food::findOrFail($id);
    abort_unless($food->owner_id === Auth::id() || Auth::user()?->role === 'admin', 403);

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
    abort_unless($food->owner_id === Auth::id() || Auth::user()?->role === 'admin', 403);

    return view(
        'owner.create-menu',
        compact('food')
    );
}

public function storeMenu(Request $request, $foodId)
{
    $food = Food::findOrFail($foodId);
    abort_unless($food->owner_id === Auth::id() || Auth::user()?->role === 'admin', 403);
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
        (Auth::user()?->role === 'admin' ? '/admin/foods/' : '/owner/foods/').$foodId.'/menus'
    )->with(
        'success',
        'Menu Item Added Successfully.'
    );
}

public function deleteMenu($id)
{
    $menu = FoodMenu::findOrFail($id);
    $food = Food::findOrFail($menu->food_id);
    abort_unless($food->owner_id === Auth::id() || Auth::user()?->role === 'admin', 403);

    $foodId = $menu->food_id;

    $menu->delete();

    return redirect(
        (Auth::user()?->role === 'admin' ? '/admin/foods/' : '/owner/foods/').$foodId.'/menus'
    )->with(
        'success',
        'Menu Item Deleted.'
    );
}

public function editMenu($id)
{
    $menu = FoodMenu::findOrFail($id);
    $food = Food::findOrFail($menu->food_id);
    abort_unless($food->owner_id === Auth::id() || Auth::user()?->role === 'admin', 403);

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
    $food = Food::findOrFail($menu->food_id);
    abort_unless($food->owner_id === Auth::id() || Auth::user()?->role === 'admin', 403);

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
        (Auth::user()?->role === 'admin' ? '/admin/foods/' : '/owner/foods/').$menu->food_id.'/menus'
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
    $deletedFoods = Food::onlyTrashed()->with('owner')->latest('deleted_at')->get();

    return view(
        'admin.foods',
        compact('foods', 'deletedFoods')
    );
}

public function adminShow($id)
{
    $food = Food::with(
        'owner',
        'menus',
        'comments.user',
        'comments.replies.user',
        'comments.ratings',
        'ratings.user'
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

    BookingNotice::send(
        (int) $food->owner_id,
        'Your restaurant "'.$food->name.'" was approved.',
        ['food_id' => $food->id],
        'listing'
    );

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

    BookingNotice::send(
        (int) $food->owner_id,
        'Your restaurant "'.$food->name.'" was rejected.',
        ['food_id' => $food->id],
        'listing'
    );

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

public function restore($id)
{
    $food = Food::onlyTrashed()->findOrFail($id);
    abort_if($food->deleted_at->lt(now()->subDays(30)), 410, 'This restaurant can no longer be restored.');
    $food->restore();
    return redirect('/admin/foods')->with('success', 'Restaurant restored successfully.');
}

public function comment(Request $request)
{
    $request->validate(['comment' => 'required|string|max:1000', 'image' => 'nullable|image|mimes:jpeg,png,webp|max:5120']);
    $image = $request->hasFile('image') ? time().'_'.$request->file('image')->getClientOriginalName() : null;
    if ($image) $request->file('image')->move(public_path('uploads/comments'), $image);
    FoodComment::create([

        'food_id' => $request->food_id,

        'user_id' => Auth::id(),

        'comment' => $request->comment,
        'image' => $image

    ]);

    return back()->with(
        'success',
        'Comment Added.'
    );
}

public function userIndex()
{
    $foods = Food::where('status', 'approved')
        ->withAvg('ratings', 'rating')
        ->latest()
        ->get();

    return view('user.foods', compact('foods'));
}

public function userShow($id)
{
    $food = Food::with(
        'menus',
        'comments.user',
        'ratings.user'
    )->findOrFail($id);

    return view('user.food-details', compact('food'));
}

public function show($id)
{
    $food = Food::with(
        'menus',
        'comments.user',
        'ratings.user'
    )->findOrFail($id);

    return view('user.food-details', compact('food'));
}

public function reply(Request $request)
{
    $request->validate([
        'comment_id' => 'required|exists:food_comments,id',
        'reply' => 'required|string',
        'image' => 'nullable|image|mimes:jpeg,png,webp|max:5120',
    ]);

    $image = $request->hasFile('image') ? time().'_'.$request->file('image')->getClientOriginalName() : null;
    if ($image) $request->file('image')->move(public_path('uploads/comments'), $image);
    FoodReply::create([
        'food_comment_id' => $request->comment_id,
        'user_id' => Auth::id(),
        'parent_reply_id' => $request->parent_reply_id,
        'reply' => $request->reply,
        'image' => $image,
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

public function rateReply(Request $request)
{
    $request->validate(['reply_id' => 'required|exists:food_replies,id', 'rating' => 'required|integer|between:1,5']);
    FoodReplyRating::updateOrCreate(['reply_id' => $request->reply_id, 'user_id' => Auth::id()], ['rating' => $request->rating]);
    return back()->with('success', 'Reply rating saved.');
}

public function updateDailyLimit(Request $request, $id)
{
    $food = Food::findOrFail($id);
    abort_unless((int) $food->owner_id === (int) Auth::id(), 403);
    $request->validate(['daily_booking_limit' => 'nullable|integer|min:1']);
    $food->update([
        'daily_booking_limit' => $request->filled('daily_booking_limit') ? $request->integer('daily_booking_limit') : null,
    ]);

    return back()->with('success', 'Daily booking limit saved.');
}
}
