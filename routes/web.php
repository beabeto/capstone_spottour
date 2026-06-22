<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\SpotController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\UserSpotController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\OwnerController;
use App\Http\Controllers\HotelController;

Route::get(
    '/owner/hotels',
    [HotelController::class,'ownerIndex']
);

Route::get(
    '/owner/hotels/create',
    [HotelController::class,'create']
);

Route::post(
    '/owner/hotels/store',
    [HotelController::class,'store']
);

Route::get(
    '/owner/hotels/{id}',
    [HotelController::class,'ownerShow']
);

Route::get(
    '/admin/hotels',
    [HotelController::class,'adminIndex']
);

Route::get(
    '/admin/hotels/{id}',
    [HotelController::class,'adminShow']
);

Route::get(
    '/hotels',
    [HotelController::class,'userIndex']
);

Route::get(
    '/hotels/{id}',
    [HotelController::class,'show']
);

Route::get(
    '/owner/hotels/edit/{id}',
    [HotelController::class,'edit']
);

Route::post(
    '/owner/hotels/update/{id}',
    [HotelController::class,'update']
);

Route::get(
    '/owner/hotels/delete/{id}',
    [HotelController::class,'delete']
);

Route::post(
    '/hotel/rate',
    [HotelController::class,'rate']
);

Route::post(
    '/hotel/comment',
    [HotelController::class,'comment']
);

Route::post(
    '/hotel/comment/reply',
    [HotelController::class,'reply']
);

Route::post(
    '/hotel/comment/rate',
    [HotelController::class,'rateComment']
);

Route::get(
    '/hotel/comment/{id}/like',
    [HotelController::class,'like']
);

Route::get(
    '/hotel/comment/{id}/dislike',
    [HotelController::class,'dislike']
);

Route::get(
    '/hotel/comments/delete/{id}',
    [HotelController::class,'deleteComment']
);

Route::get(
    '/hotel/comments/edit/{id}',
    [HotelController::class,'editComment']
);

Route::post(
    '/hotel/comments/update/{id}',
    [HotelController::class,'updateComment']
);


Route::get(
    '/hotel/reply/edit/{id}',
    [HotelController::class,'editReply']
);

Route::post(
    '/hotel/reply/update/{id}',
    [HotelController::class,'updateReply']
);

Route::get(
    '/hotel/reply/delete/{id}',
    [HotelController::class,'deleteReply']
);

Route::get(
    '/owner/reviews',
    [OwnerController::class,'reviews']
);

Route::get(
    '/admin/hotels/approve/{id}',
    [HotelController::class,'approve']
);

Route::get(
    '/admin/hotels/reject/{id}',
    [HotelController::class,'reject']
);

Route::get(
    '/admin/hotels/delete/{id}',
    [HotelController::class,'adminDelete']
);

Route::get(
    '/hotel/favorite/{hotel}',
    [HotelController::class,'favorite']
);

Route::get(
    '/hotel/bookmark/{hotel}',
    [HotelController::class,'bookmark']
);

Route::get(
    '/hotel/checklist/{hotel}',
    [HotelController::class,'checklist']
);

Route::get(
    '/user/hotel-favorites',
    [HotelController::class,'favoritesPage']
);

Route::get(
    '/user/hotel-bookmarks',
    [HotelController::class,'bookmarksPage']
);

Route::get(
    '/user/hotel-checklist',
    [HotelController::class,'checklistsPage']
);

Route::get(
    '/notification/{id}',
    [NotificationController::class,'open']
);

Route::get(
    '/notification/delete/{id}',
    [NotificationController::class,'delete']
);

Route::get(
    '/user/notifications',
    [NotificationController::class, 'index']
);

Route::post('/comment/rate', [CommentController::class,'rate']);

Route::post('/comment/reply', [CommentController::class,'reply']);

Route::get('/comment/{id}/like', [CommentController::class,'like']);

Route::get('/comment/{id}/dislike', [CommentController::class,'dislike']);

Route::get(
    '/comments/edit/{id}',
    [CommentController::class,'edit']
);

Route::post(
    '/comments/update/{id}',
    [CommentController::class,'update']
);

Route::get(
    '/comments/delete/{id}',
    [CommentController::class,'delete']
);

Route::get(
    '/reply/edit/{id}',
    [CommentController::class,'editReply']
);

Route::post(
    '/reply/update/{id}',
    [CommentController::class,'updateReply']
);

Route::get(
    '/reply/delete/{id}',
    [CommentController::class,'deleteReply']
);


Route::get(
    '/user/favorites',
    [UserSpotController::class,'favoritesPage']
);

Route::get(
    '/user/bookmarks',
    [UserSpotController::class,'bookmarksPage']
);

Route::get(
    '/user/checklist',
    [UserSpotController::class,'checklistsPage']
);

Route::get(
'/favorite/{spot}',
[UserSpotController::class,'favorite']
);

Route::get(
'/bookmark/{spot}',
[UserSpotController::class,'bookmark']
);

Route::get(
'/checklist/{spot}',
[UserSpotController::class,'checklist']
);


Route::view('/user/feedback','user.feedback');

Route::get(
'/owner/bookings',
[BookingController::class,
'ownerBookings']
);

Route::get(
'/admin/bookings',
[BookingController::class,
'adminBookings']
);

Route::get(
'/owner/bookings/approve/{id}',
[BookingController::class,
'approve']
);

Route::get(
'/owner/bookings/reject/{id}',
[BookingController::class,
'reject']
);

Route::post(
    '/bookings/store',
    [BookingController::class,'store']
);

Route::get(
    '/user/bookings',
    [BookingController::class,'userBookings']
);

Route::get(
    '/booking/confirm',
    [BookingController::class,'confirm']
);

Route::post(
'/booking/payment',
[BookingController::class,'paymentPage']
);

Route::get(
    '/owner/calendar',
    [BookingController::class,'calendar']
);

Route::get(
    '/booking/cancel/{id}',
    [BookingController::class,'cancel']
);

Route::get(
    '/admin/bookings',
    [BookingController::class,'adminBookings']
);

Route::get(
    '/hotel/booking/confirm',
    [BookingController::class,'hotelConfirm']
);

Route::post(
    '/hotel/booking/store',
    [BookingController::class,'hotelStore']
);

Route::post(
    '/payment/upload',
    [PaymentController::class,'upload']
);

Route::get('/', [AuthController::class,'index']);

Route::post('/register',[AuthController::class,'register']);

Route::post('/login',[AuthController::class,'login']);

Route::get('/logout',[AuthController::class,'logout']);

Route::post('/profile/update',[AuthController::class,'updateProfile']);

Route::get('/admin', function(){

    if(!Auth::check())
    {
        return redirect('/');
    }

    return view('admin');

});

Route::view('/owner','owner');
Route::view('/user','user');

Route::view('/bookings','user.bookings');

Route::view('/favorites','user.favorites');

Route::view('/checklist','user.checklist');

Route::view('/bookmarks','user.bookmarks');

Route::view('/feedback','user.feedback');

Route::get(
    '/user/profile',
    [AuthController::class,'profile']
);

Route::view('/owner/profile','owner.profile');

Route::view('/admin/profile', 'admin.profile');

Route::get('/admin/users',
    [AdminController::class,'users']);

Route::get('/admin/owners',
    [AdminController::class,'owners']);

    Route::get('/owner/spots',
    [SpotController::class,'ownerIndex']);

Route::get('/owner/spots/create',
    [SpotController::class,'create']);

Route::post('/owner/spots/store',
    [SpotController::class,'store']);

Route::get('/admin/spots',
    [SpotController::class,'adminIndex']);

Route::get('/spots',
    [SpotController::class,'userIndex']);

    Route::get('/spots/{id}',
    [SpotController::class,'show']);

Route::get('/owner/spots/{id}',
    [SpotController::class,'ownerShow']);

Route::get('/admin/spots/{id}',
    [SpotController::class,'adminShow']);

Route::post(
'/spot/rate',
[SpotController::class,'rate']
);

Route::post(
'/spot/comment',
[SpotController::class,'comment']
);

Route::get(
    '/owner/spots/edit/{id}',
    [SpotController::class,'edit']
);

Route::post(
    '/owner/spots/update/{id}',
    [SpotController::class,'update']
);

Route::get(
    '/owner/spots/delete/{id}',
    [SpotController::class,'delete']
);

Route::get(
    '/admin/spots',
    [SpotController::class,'adminIndex']
);

Route::get(
    '/admin/spots/{id}',
    [SpotController::class,'adminShow']
);

Route::get(
    '/admin/spots/approve/{id}',
    [SpotController::class,'approve']
);

Route::get(
    '/admin/spots/reject/{id}',
    [SpotController::class,'reject']
);

Route::get(
    '/admin/spots/delete/{id}',
    [SpotController::class,'adminDelete']
);