<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\OwnerController;
use App\Http\Controllers\UserSpotController;
use App\Http\Controllers\SpotController;
use App\Http\Controllers\HotelController;
use App\Http\Controllers\FoodController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\FeedbackController;

///////////////////////////////////////////////////////////////
// AUTHENTICATION
///////////////////////////////////////////////////////////////

Route::get('/', [AuthController::class,'index']);

Route::post('/login', [AuthController::class,'login']);

Route::post('/register', [AuthController::class,'register']);

Route::get('/password/forgot', [AuthController::class, 'showForgotPassword'])->middleware('guest');
Route::post('/password/code', [AuthController::class, 'sendPasswordResetCode'])->middleware('guest');
Route::get('/password/reset', [AuthController::class, 'showForgotPassword'])->middleware('guest');
Route::post('/password/reset', [AuthController::class, 'resetPasswordWithCode'])->middleware('guest');

Route::get('/logout', [AuthController::class,'logout']);

///////////////////////////////////////////////////////////////
// DASHBOARDS
///////////////////////////////////////////////////////////////

Route::get(
    '/user',
    [UserSpotController::class,'dashboard']
)->middleware('auth');

Route::get(
    '/owner',
    [OwnerController::class,'dashboard']
);

Route::get('/owner/revenue-period', [OwnerController::class, 'revenuePeriod']);
Route::get('/owner/booking-period', [OwnerController::class, 'bookingPeriod']);
Route::get('/booking/availability', [BookingController::class, 'availability']);
Route::get('/admin/calendar', [BookingController::class, 'calendar'])->middleware('auth');

Route::get(
    '/admin',
    [AdminController::class,'dashboard']
);

///////////////////////////////////////////////////////////////
// PROFILE
///////////////////////////////////////////////////////////////

Route::get(
    '/user/profile',
    [AuthController::class,'profile']
);

Route::post(
    '/profile/update',
    [AuthController::class,'updateProfile']
);

Route::get('/owner/profile', function () {
    return Auth::check() && Auth::user()->role === 'owner'
        ? view('owner.profile', ['storeName' => \App\Models\Spot::where('user_id', Auth::id())->value('name') ?? \App\Models\Hotel::where('owner_id', Auth::id())->value('name') ?? \App\Models\Food::where('owner_id', Auth::id())->value('name')])
        : redirect('/')->with('error', 'Please sign in as an owner to open your profile.');
});

Route::get('/admin/profile', function () {
    return Auth::check() && Auth::user()->role === 'admin'
        ? view('admin.profile')
        : redirect('/')->with('error', 'Please sign in as an administrator to open your profile.');
});

///////////////////////////////////////////////////////////////
// SEARCH
///////////////////////////////////////////////////////////////

Route::get(
    '/search',
    [UserSpotController::class,'search']
);

Route::get('/search/suggestions', [UserSpotController::class, 'suggestions']);

///////////////////////////////////////////////////////////////
// USER PAGES
///////////////////////////////////////////////////////////////

Route::get(
    '/user/bookings',
    [BookingController::class,'userBookings']
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

///////////////////////////////////////////////////////////////
// NOTIFICATIONS
///////////////////////////////////////////////////////////////

Route::get(
    '/user/notifications',
    [NotificationController::class,'index']
);

Route::get(
    '/notification/{id}',
    [NotificationController::class,'open']
);

Route::get(
    '/notification/delete/{id}',
    [NotificationController::class,'delete']
);

///////////////////////////////////////////////////////////////
// FEEDBACK
///////////////////////////////////////////////////////////////

Route::get(
    '/user/feedback',
    [FeedbackController::class,'userIndex']
);

Route::post(
    '/user/feedback',
    [FeedbackController::class,'store']
);

Route::get(
    '/owner/feedback',
    [FeedbackController::class,'ownerIndex']
);

Route::post(
    '/owner/feedback',
    [FeedbackController::class,'store']
);

Route::get(
    '/admin/feedback',
    [FeedbackController::class,'adminIndex']
);

Route::post(
    '/admin/feedback/reply/{id}',
    [FeedbackController::class,'reply']
);

Route::get(
    '/admin/feedback/resolve/{id}',
    [FeedbackController::class,'resolve']
);

Route::post(
    '/admin/feedback/update/{id}',
    [FeedbackController::class,'update']
);

Route::get(
    '/admin/feedback/delete/{id}',
    [FeedbackController::class,'delete']
);

///////////////////////////////////////////////////////////////
// SPOT ROUTES
///////////////////////////////////////////////////////////////

//
// USER
//

Route::get(
    '/spots',
    [SpotController::class,'userIndex']
);

Route::get(
    '/spots/{id}',
    [SpotController::class,'show']
);

//
// RATINGS
//

Route::post(
    '/spot/rate',
    [SpotController::class,'rate']
);

//
// COMMENTS
//

Route::post(
    '/spot/comment',
    [SpotController::class,'comment']
);

Route::post(
    '/comment/reply',
    [CommentController::class,'reply']
);

Route::post(
    '/comment/rate',
    [CommentController::class,'rate']
);

Route::post('/reply/rate', [CommentController::class,'rateReply']);

Route::get(
    '/comment/{id}/like',
    [CommentController::class,'like']
);

Route::get(
    '/comment/{id}/dislike',
    [CommentController::class,'dislike']
);

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

//
// COMMENT REPLIES
//

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

//
// FAVORITES
//

Route::get(
    '/favorite/{spot}',
    [UserSpotController::class,'favorite']
);

//
// BOOKMARKS
//

Route::get(
    '/bookmark/{spot}',
    [UserSpotController::class,'bookmark']
);

//
// CHECKLIST
//

Route::get(
    '/checklist/{spot}',
    [UserSpotController::class,'checklist']
);

///////////////////////////////////////////////////////////////
// OWNER SPOTS
///////////////////////////////////////////////////////////////

Route::get(
    '/owner/spots',
    [SpotController::class,'ownerIndex']
);

Route::get(
    '/owner/spots/create',
    [SpotController::class,'create']
);

Route::post(
    '/owner/spots/store',
    [SpotController::class,'store']
);

Route::get(
    '/owner/spots/{id}',
    [SpotController::class,'ownerShow']
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

///////////////////////////////////////////////////////////////
// ADMIN SPOTS
///////////////////////////////////////////////////////////////

Route::get(
    '/admin/spots',
    [SpotController::class,'adminIndex']
);

Route::get('/admin/spots/create', [SpotController::class, 'create']);
Route::post('/admin/spots/store', [SpotController::class, 'store']);

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

Route::get('/admin/spots/restore/{id}', [SpotController::class,'restore']);

Route::get('/admin/spots/{id}', [SpotController::class,'adminShow'])->whereNumber('id');

///////////////////////////////////////////////////////////////
// HOTEL ROUTES
///////////////////////////////////////////////////////////////

//
// USER HOTELS
//

Route::get(
    '/hotels',
    [HotelController::class,'userIndex']
);

Route::get(
    '/hotels/{id}',
    [HotelController::class,'show']
);

//
// HOTEL RATINGS
//

Route::post(
    '/hotel/rate',
    [HotelController::class,'rate']
);

//
// HOTEL COMMENTS
//

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

Route::post('/hotel/reply/rate', [HotelController::class,'rateReply']);

Route::get(
    '/hotel/comment/{id}/like',
    [HotelController::class,'like']
);

Route::get(
    '/hotel/comment/{id}/dislike',
    [HotelController::class,'dislike']
);

//
// HOTEL COMMENT CRUD
//

Route::get(
    '/hotel/comments/edit/{id}',
    [HotelController::class,'editComment']
);

Route::post(
    '/hotel/comments/update/{id}',
    [HotelController::class,'updateComment']
);

Route::get(
    '/hotel/comments/delete/{id}',
    [HotelController::class,'deleteComment']
);

//
// HOTEL REPLY CRUD
//

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

//
// HOTEL FAVORITES
//

Route::get(
    '/hotel/favorite/{hotel}',
    [UserSpotController::class,'hotelFavorite']
);

//
// HOTEL BOOKMARKS
//

Route::get(
    '/hotel/bookmark/{hotel}',
    [UserSpotController::class,'hotelBookmark']
);

//
// HOTEL CHECKLIST
//

Route::get(
    '/hotel/checklist/{hotel}',
    [UserSpotController::class,'hotelChecklist']
);

///////////////////////////////////////////////////////////////
// OWNER HOTELS
///////////////////////////////////////////////////////////////

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

Route::get('/owner/hotels/{id}/rooms', [HotelController::class, 'rooms']);
Route::post('/owner/hotels/{id}/rooms', [HotelController::class, 'storeRoom']);

Route::get(
    '/owner/hotels/{id}',
    [HotelController::class,'ownerShow']
)->whereNumber('id');

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

///////////////////////////////////////////////////////////////
// ADMIN HOTELS
///////////////////////////////////////////////////////////////

Route::get(
    '/admin/hotels',
    [HotelController::class,'adminIndex']
);

Route::get('/admin/hotels/create', [HotelController::class, 'create']);
Route::post('/admin/hotels/store', [HotelController::class, 'store']);
Route::get('/admin/hotels/{id}/rooms', [HotelController::class, 'rooms'])->whereNumber('id');
Route::post('/admin/hotels/{id}/rooms', [HotelController::class, 'storeRoom'])->whereNumber('id');

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

Route::get('/admin/hotels/restore/{id}', [HotelController::class,'restore']);

Route::get('/admin/hotels/{id}', [HotelController::class,'adminShow'])->whereNumber('id');

///////////////////////////////////////////////////////////////
// OWNER HOTEL REVIEWS
///////////////////////////////////////////////////////////////

Route::get(
    '/owner/reviews',
    [OwnerController::class,'reviews']
);

///////////////////////////////////////////////////////////////
// FOOD / RESTAURANT ROUTES
///////////////////////////////////////////////////////////////

//
// USER RESTAURANTS
//

Route::get(
    '/foods',
    [FoodController::class,'userIndex']
);

Route::get(
    '/foods/{id}',
    [FoodController::class,'show']
);

//
// FOOD RATINGS
//

Route::post(
    '/food/rate',
    [FoodController::class,'rate']
);

//
// FOOD COMMENTS
//

Route::post(
    '/food/comment',
    [FoodController::class,'comment']
);

Route::post(
    '/food/comment/reply',
    [FoodController::class,'reply']
);

Route::post(
    '/food/comment/rate',
    [FoodController::class,'rateComment']
);

Route::post('/food/reply/rate', [FoodController::class,'rateReply']);

Route::get(
    '/food/comment/{id}/like',
    [FoodController::class,'like']
);

Route::get(
    '/food/comment/{id}/dislike',
    [FoodController::class,'dislike']
);

//
// FOOD COMMENT CRUD
//

Route::get(
    '/food/comments/edit/{id}',
    [FoodController::class,'editComment']
);

Route::post(
    '/food/comments/update/{id}',
    [FoodController::class,'updateComment']
);

Route::get(
    '/food/comments/delete/{id}',
    [FoodController::class,'deleteComment']
);

//
// FOOD FAVORITES
//

Route::get(
    '/food/favorite/{food}',
    [UserSpotController::class,'foodFavorite']
);

//
// FOOD BOOKMARKS
//

Route::get(
    '/food/bookmark/{food}',
    [UserSpotController::class,'foodBookmark']
);

//
// FOOD CHECKLIST
//

Route::get(
    '/food/checklist/{food}',
    [UserSpotController::class,'foodChecklist']
);

///////////////////////////////////////////////////////////////
// OWNER RESTAURANTS
///////////////////////////////////////////////////////////////

Route::get(
    '/owner/foods',
    [FoodController::class,'ownerIndex']
);

Route::get(
    '/owner/foods/create',
    [FoodController::class,'create']
);

Route::post(
    '/owner/foods/store',
    [FoodController::class,'store']
);

Route::post(
    '/owner/foods/{id}/daily-limit',
    [FoodController::class, 'updateDailyLimit']
);

Route::get(
    '/owner/foods/{id}',
    [FoodController::class,'ownerShow']
);

Route::get(
    '/owner/foods/edit/{id}',
    [FoodController::class,'edit']
);

Route::post(
    '/owner/foods/update/{id}',
    [FoodController::class,'update']
);

Route::get(
    '/owner/foods/delete/{id}',
    [FoodController::class,'delete']
);

///////////////////////////////////////////////////////////////
// FOOD MENUS
///////////////////////////////////////////////////////////////

Route::get(
    '/owner/foods/{food}/menus',
    [FoodController::class,'menus']
);

Route::get(
    '/owner/foods/{food}/menus/create',
    [FoodController::class,'createMenu']
);

Route::post(
    '/owner/foods/{food}/menus/store',
    [FoodController::class,'storeMenu']
);

Route::get(
    '/owner/menus/edit/{id}',
    [FoodController::class,'editMenu']
);

Route::post(
    '/owner/menus/update/{id}',
    [FoodController::class,'updateMenu']
);

Route::get(
    '/owner/menus/delete/{id}',
    [FoodController::class,'deleteMenu']
);

///////////////////////////////////////////////////////////////
// ADMIN RESTAURANTS
///////////////////////////////////////////////////////////////

Route::get(
    '/admin/foods',
    [FoodController::class,'adminIndex']
);

Route::get('/admin/foods/create', [FoodController::class, 'create']);
Route::post('/admin/foods/store', [FoodController::class, 'store']);
Route::get('/admin/foods/{id}/menus', [FoodController::class, 'menus'])->whereNumber('id');
Route::get('/admin/foods/{id}/menus/create', [FoodController::class, 'createMenu'])->whereNumber('id');
Route::post('/admin/foods/{id}/menus/store', [FoodController::class, 'storeMenu'])->whereNumber('id');
Route::get('/admin/menus/edit/{id}', [FoodController::class, 'editMenu']);
Route::post('/admin/menus/update/{id}', [FoodController::class, 'updateMenu']);
Route::get('/admin/menus/delete/{id}', [FoodController::class, 'deleteMenu']);

Route::get(
    '/admin/foods/approve/{id}',
    [FoodController::class,'approve']
);

Route::get(
    '/admin/foods/reject/{id}',
    [FoodController::class,'reject']
);

Route::get(
    '/admin/foods/delete/{id}',
    [FoodController::class,'adminDelete']
);

Route::get('/admin/foods/restore/{id}', [FoodController::class,'restore']);

Route::get('/admin/foods/{id}', [FoodController::class,'adminShow'])->whereNumber('id');

///////////////////////////////////////////////////////////////
// BOOKING ROUTES
///////////////////////////////////////////////////////////////

//
// USER SPOT BOOKING
//

Route::post(
    '/bookings/store',
    [BookingController::class,'store']
);

Route::get('/booking/spot/{id}', [BookingController::class, 'spotBookingPage']);
Route::get('/booking/hotel/{id}', [BookingController::class, 'hotelBookingPage']);
Route::get('/booking/food/{id}', [BookingController::class, 'foodBookingPage']);

Route::get(
    '/booking/confirm',
    [BookingController::class,'confirm']
);

Route::match(['get', 'post'],
    '/booking/payment',
    [BookingController::class,'paymentPage']
);

Route::get(
    '/booking/cancel/{id}',
    [BookingController::class,'cancel']
);

Route::get(
    '/booking/confirmation/{type}/{id}',
    [BookingController::class, 'downloadConfirmation']
)->whereIn('type', ['spot', 'hotel', 'food']);

Route::get(
    '/booking/paymongo/{result}/{type}/{id}',
    [BookingController::class, 'paymongoResult']
)->whereIn('result', ['return', 'cancel'])->whereIn('type', ['spot', 'hotel', 'food']);

///////////////////////////////////////////////////////////////
// USER HOTEL BOOKING
///////////////////////////////////////////////////////////////

Route::get(
    '/hotel/booking/confirm',
    [BookingController::class,'hotelConfirm']
);

Route::match(
    ['get','post'],
    '/hotel/payment',
    [BookingController::class,'hotelPaymentPage']
);

Route::post(
    '/hotel/booking/store',
    [BookingController::class,'hotelStore']
);

Route::get(
    '/hotel/booking/cancel/{id}',
    [BookingController::class,'cancelHotel']
);

///////////////////////////////////////////////////////////////
// USER FOOD BOOKING
///////////////////////////////////////////////////////////////

Route::get(
    '/food/booking/confirm',
    [BookingController::class,'foodConfirm']
);

Route::match(
    ['get','post'],
    '/food/payment',
    [BookingController::class,'foodPaymentPage']
);

Route::post(
    '/food/booking/store',
    [BookingController::class,'foodStore']
);

Route::get(
    '/food/booking/cancel/{id}',
    [BookingController::class,'cancelFood']
);

///////////////////////////////////////////////////////////////
// PAYMENT
///////////////////////////////////////////////////////////////

Route::post(
    '/payment/upload',
    [PaymentController::class,'upload']
);

///////////////////////////////////////////////////////////////
// OWNER BOOKINGS
///////////////////////////////////////////////////////////////

Route::get(
    '/owner/bookings',
    [BookingController::class,'ownerBookings']
);

Route::post('/owner/arrivals', [BookingController::class, 'arriveByCode']);
Route::get('/owner/bookings/arrive/{type}/{id}', [BookingController::class, 'arrive']);

Route::get('/owner/notifications', [\App\Http\Controllers\NotificationController::class, 'ownerIndex']);
Route::get('/owner/packages', [\App\Http\Controllers\PackageController::class, 'index']);
Route::post('/owner/packages', [\App\Http\Controllers\PackageController::class, 'store']);
Route::get('/owner/packages/delete/{id}', [\App\Http\Controllers\PackageController::class, 'destroy']);

Route::get(
    '/owner/calendar',
    [BookingController::class,'calendar']
);

//
// SPOT BOOKING
//

Route::get(
    '/owner/bookings/approve/{id}',
    [BookingController::class,'approve']
);

Route::get(
    '/owner/bookings/reject/{id}',
    [BookingController::class,'reject']
);

//
// HOTEL BOOKING
//

Route::get(
    '/owner/hotel-bookings/approve/{id}',
    [BookingController::class,'approveHotel']
);

Route::get(
    '/owner/hotel-bookings/reject/{id}',
    [BookingController::class,'rejectHotel']
);

//
// FOOD BOOKING
//

Route::get(
    '/owner/food-bookings/approve/{id}',
    [BookingController::class,'approveFood']
);

Route::get(
    '/owner/food-bookings/reject/{id}',
    [BookingController::class,'rejectFood']
);

///////////////////////////////////////////////////////////////
// ADMIN BOOKINGS
///////////////////////////////////////////////////////////////

Route::get('/admin/reports', [\App\Http\Controllers\AdminController::class, 'visitorReport']);
Route::get('/admin/reports/pdf', [\App\Http\Controllers\AdminController::class, 'visitorReportPdf']);
Route::get('/user/plan', [\App\Http\Controllers\DayPlanController::class, 'index']);
Route::post('/user/plan', [\App\Http\Controllers\DayPlanController::class, 'store']);
Route::get('/admin/day-plans', [\App\Http\Controllers\DayPlanController::class, 'adminIndex']);
Route::get('/owner/day-plans', [\App\Http\Controllers\DayPlanController::class, 'ownerIndex']);

Route::get(
    '/admin/bookings',
    [BookingController::class,'adminBookings']
);

///////////////////////////////////////////////////////////////
// ADMIN USERS
///////////////////////////////////////////////////////////////

Route::get(
    '/admin/users',
    [AdminController::class,'users']
);

Route::get(
    '/admin/owners',
    [AdminController::class,'owners']
);

Route::get('/admin/accounts/{user}', [AdminController::class, 'account'])->middleware('auth');

Route::post(
    '/admin/accounts/{user}/password',
    [AdminController::class,'resetAccountPassword']
)->middleware('auth');

Route::post('/admin/accounts/{user}', [AdminController::class, 'updateAccount'])->middleware('auth');

Route::post('/admin/accounts/{user}/delete', [AdminController::class, 'deleteAccount'])->middleware('auth');

Route::get(
    '/admin/activities',
    [AdminController::class,'activities']
)->middleware('auth');

Route::middleware('auth')->group(function () {
    Route::get('/chat', [\App\Http\Controllers\ChatController::class, 'state']);
    Route::post('/chat', [\App\Http\Controllers\ChatController::class, 'send']);
    Route::get('/admin/notifications', [\App\Http\Controllers\NotificationController::class, 'adminIndex']);
    Route::get('/admin/chats', [\App\Http\Controllers\ChatController::class, 'adminIndex']);
    Route::get('/admin/chats/{thread}', [\App\Http\Controllers\ChatController::class, 'adminShow']);
    Route::post('/admin/chats/{thread}', [\App\Http\Controllers\ChatController::class, 'reply']);
    Route::get('/owner/chats', [\App\Http\Controllers\ChatController::class, 'ownerIndex']);
    Route::get('/owner/chats/{thread}', [\App\Http\Controllers\ChatController::class, 'ownerShow']);
    Route::post('/owner/chats/{thread}', [\App\Http\Controllers\ChatController::class, 'reply']);
});
