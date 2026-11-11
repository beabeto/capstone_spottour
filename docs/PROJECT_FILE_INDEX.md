# SpotTour Project File Index

Project files grouped by folder for use in a report or table of contents. Uploaded files under `public/uploads/`, installed dependencies, generated cache files, Git internals, and the actual `.env` file are excluded.

## (project root)

- `.dockerignore`
- `.editorconfig`
- `.env.example`
- `.gitattributes`
- `.gitignore`
- `.npmrc`
- `artisan`
- `composer.json`
- `composer.lock`
- `Dockerfile`
- `package.json`
- `phpunit.xml`
- `railway.toml`
- `RAILWAY_UPLOADS.md`
- `railway-ensure-schema.php`
- `railway-migrate.php`
- `railway-seed-admin.php`
- `README.md`
- `start.sh`
- `vite.config.js`

## app/Http/Controllers

- `app/Http/Controllers/AdminController.php`
- `app/Http/Controllers/AuthController.php`
- `app/Http/Controllers/BookingController.php`
- `app/Http/Controllers/ChatController.php`
- `app/Http/Controllers/CommentController.php`
- `app/Http/Controllers/Controller.php`
- `app/Http/Controllers/DayPlanController.php`
- `app/Http/Controllers/FeedbackController.php`
- `app/Http/Controllers/FoodController.php`
- `app/Http/Controllers/HotelController.php`
- `app/Http/Controllers/NotificationController.php`
- `app/Http/Controllers/OwnerController.php`
- `app/Http/Controllers/PackageController.php`
- `app/Http/Controllers/PaymentController.php`
- `app/Http/Controllers/SpotController.php`
- `app/Http/Controllers/UserSpotController.php`

## app/Http/Middleware

- `app/Http/Middleware/InjectTheme.php`

## app/Models

- `app/Models/Booking.php`
- `app/Models/Bookmark.php`
- `app/Models/ChatMessage.php`
- `app/Models/ChatThread.php`
- `app/Models/Checklist.php`
- `app/Models/CommentRating.php`
- `app/Models/CommentReaction.php`
- `app/Models/CommentReply.php`
- `app/Models/CommentReplyRating.php`
- `app/Models/DayPlan.php`
- `app/Models/Favorite.php`
- `app/Models/Feedback.php`
- `app/Models/Food.php`
- `app/Models/FoodBooking.php`
- `app/Models/FoodBookmark.php`
- `app/Models/FoodChecklist.php`
- `app/Models/FoodComment.php`
- `app/Models/FoodCommentRating.php`
- `app/Models/FoodFavorite.php`
- `app/Models/FoodMenu.php`
- `app/Models/FoodRating.php`
- `app/Models/FoodReaction.php`
- `app/Models/FoodReply.php`
- `app/Models/FoodReplyRating.php`
- `app/Models/Hotel.php`
- `app/Models/HotelBooking.php`
- `app/Models/HotelBookmark.php`
- `app/Models/HotelChecklist.php`
- `app/Models/HotelComment.php`
- `app/Models/HotelCommentRating.php`
- `app/Models/HotelFavorite.php`
- `app/Models/HotelPayment.php`
- `app/Models/HotelRating.php`
- `app/Models/HotelReaction.php`
- `app/Models/HotelReply.php`
- `app/Models/HotelReplyRating.php`
- `app/Models/HotelRoom.php`
- `app/Models/Notification.php`
- `app/Models/Package.php`
- `app/Models/Payment.php`
- `app/Models/Spot.php`
- `app/Models/SpotComment.php`
- `app/Models/SpotRating.php`
- `app/Models/User.php`

## app/Providers

- `app/Providers/AppServiceProvider.php`

## app/Support

- `app/Support/ArrivalCode.php`
- `app/Support/BookingDocument.php`
- `app/Support/BookingExtras.php`
- `app/Support/BookingNotice.php`
- `app/Support/DailyCapacity.php`
- `app/Support/EnsureLiveSchema.php`
- `app/Support/GalleryImages.php`
- `app/Support/ListingImage.php`
- `app/Support/ListingPersist.php`
- `app/Support/PaymentChoice.php`
- `app/Support/PayMongoCheckout.php`
- `app/Support/PlaceMap.php`
- `app/Support/SchemaSafe.php`
- `app/Support/SpotTourMail.php`
- `app/Support/TravellerAssistant.php`
- `app/Support/UpcomingReservations.php`
- `app/Support/VisitorReport.php`

## bootstrap

- `bootstrap/app.php`
- `bootstrap/providers.php`

## config

- `config/app.php`
- `config/auth.php`
- `config/cache.php`
- `config/database.php`
- `config/filesystems.php`
- `config/logging.php`
- `config/mail.php`
- `config/queue.php`
- `config/services.php`
- `config/session.php`

## database

- `database/.gitignore`

## database/factories

- `database/factories/UserFactory.php`

## database/migrations

- `database/migrations/0001_01_01_000000_create_users_table.php`
- `database/migrations/0001_01_01_000001_create_cache_table.php`
- `database/migrations/0001_01_01_000002_create_jobs_table.php`
- `database/migrations/2026_06_15_112225_add_profile_image_to_users_table.php`
- `database/migrations/2026_06_15_124902_add_status_to_users_table.php`
- `database/migrations/2026_06_15_130749_create_spots_table.php`
- `database/migrations/2026_06_16_054817_create_spot_ratings_table.php`
- `database/migrations/2026_06_16_055329_create_spot_comments_table.php`
- `database/migrations/2026_06_16_055712_create_comment_replies_table.php`
- `database/migrations/2026_06_16_055733_create_notifications_table.php`
- `database/migrations/2026_06_16_062542_add_new_fields_to_spots_table.php`
- `database/migrations/2026_06_16_070008_add_payment_fields_to_users_table.php`
- `database/migrations/2026_06_16_070125_create_bookings_table.php`
- `database/migrations/2026_06_16_070219_create_payments_table.php`
- `database/migrations/2026_06_16_072008_create_favorites_table.php`
- `database/migrations/2026_06_16_072208_create_bookmarks_table.php`
- `database/migrations/2026_06_16_072227_create_checklists_table.php`
- `database/migrations/2026_06_16_075428_add_booking_type_to_bookings_table.php`
- `database/migrations/2026_06_16_083232_create_comment_ratings_table.php`
- `database/migrations/2026_06_16_083411_create_comment_replies_table.php`
- `database/migrations/2026_06_16_083442_create_comment_reactions_table.php`
- `database/migrations/2026_06_17_061258_add_sender_id_to_notifications_table.php`
- `database/migrations/2026_06_17_062957_add_reference_to_notifications_table.php`
- `database/migrations/2026_06_17_071232_add_reference_columns_to_notifications_table.php`
- `database/migrations/2026_06_17_094728_add_parent_reply_id_to_comment_replies_table.php`
- `database/migrations/2026_06_17_121332_add_payer_details_to_bookings_table.php`
- `database/migrations/2026_06_17_122647_add_payment_fields_to_bookings_table.php`
- `database/migrations/2026_06_18_051732_create_hotels_table.php`
- `database/migrations/2026_06_18_051827_create_hotel_bookings_table.php`
- `database/migrations/2026_06_18_051837_create_hotel_ratings_table.php`
- `database/migrations/2026_06_18_051845_create_hotel_comments_table.php`
- `database/migrations/2026_06_18_051853_create_hotel_favorites_table.php`
- `database/migrations/2026_06_18_052217_create_hotel_bookmarks_table.php`
- `database/migrations/2026_06_18_052227_create_hotel_checklists_table.php`
- `database/migrations/2026_06_18_052237_create_hotel_payments_table.php`
- `database/migrations/2026_06_18_052703_create_hotel_replies_table.php`
- `database/migrations/2026_06_18_052712_create_hotel_comment_ratings_table.php`
- `database/migrations/2026_06_18_052728_create_hotel_reactions_table.php`
- `database/migrations/2026_06_18_060308_change_map_link_column_in_hotels_table.php`
- `database/migrations/2026_06_18_122005_add_hotel_id_to_notifications_table.php`
- `database/migrations/2026_06_23_054426_create_food_table.php`
- `database/migrations/2026_06_23_061557_create_food_menus_table.php`
- `database/migrations/2026_06_23_061624_create_food_bookings_table.php`
- `database/migrations/2026_06_23_061634_create_food_favorites_table.php`
- `database/migrations/2026_06_23_061639_create_food_bookmarks_table.php`
- `database/migrations/2026_06_23_061645_create_food_checklists_table.php`
- `database/migrations/2026_06_23_061652_create_food_ratings_table.php`
- `database/migrations/2026_06_23_061656_create_food_comments_table.php`
- `database/migrations/2026_06_23_061701_create_food_replies_table.php`
- `database/migrations/2026_06_23_061710_create_food_reactions_table.php`
- `database/migrations/2026_06_23_070806_recreate_food_menus_table.php`
- `database/migrations/2026_06_23_081533_update_food_comments_table.php`
- `database/migrations/2026_06_27_063608_recreate_food_ratings_table.php`
- `database/migrations/2026_06_27_064822_create_food_comment_ratings_table.php`
- `database/migrations/2026_06_28_065639_create_feedback_table.php`
- `database/migrations/2026_06_28_112721_add_is_online_to_users_table.php`
- `database/migrations/2026_06_28_145317_add_total_price_to_food_bookings_table.php`
- `database/migrations/2026_06_28_164825_add_price_per_guest_to_foods_table.php`
- `database/migrations/2026_08_20_000000_add_gcash_qr_to_users_table.php`
- `database/migrations/2026_08_22_000000_add_food_id_to_notifications_table.php`
- `database/migrations/2026_09_16_000000_add_soft_deletes_to_listings.php`
- `database/migrations/2026_09_30_000000_add_contact_email_to_booking_tables.php`
- `database/migrations/2026_09_30_000001_add_images_to_comments_and_replies.php`
- `database/migrations/2026_09_30_000002_add_profile_details_to_users_table.php`
- `database/migrations/2026_09_30_000003_add_reply_ratings_tables.php`
- `database/migrations/2026_09_30_000004_add_galleries_and_room_types.php`
- `database/migrations/2026_10_01_000000_create_hotel_rooms_table.php`
- `database/migrations/2026_10_01_000001_add_gallery_images_to_spots_table.php`
- `database/migrations/2026_10_05_000000_create_password_reset_codes_table.php`
- `database/migrations/2026_10_06_000000_add_coordinates_to_hotels_and_foods.php`
- `database/migrations/2026_10_07_000000_add_payment_method_to_bookings.php`
- `database/migrations/2026_10_11_000000_expand_booking_status_values.php`
- `database/migrations/2026_10_15_000000_add_packages_and_booking_specs.php`
- `database/migrations/2026_10_16_000000_add_arrivals_and_day_plans.php`
- `database/migrations/2026_10_17_000000_add_daily_booking_limit.php`
- `database/migrations/2026_10_18_000000_create_chat_tables.php`
- `database/migrations/2026_10_19_000000_add_times_to_day_plans.php`
- `database/migrations/2026_10_20_000000_add_replied_by_to_feedback_table.php`
- `database/migrations/2026_10_21_000000_add_menu_items_to_food_bookings_table.php`
- `database/migrations/2026_11_07_000001_repair_food_bookmark_checklist_columns.php`
- `database/migrations/2026_11_08_000000_expand_food_map_link_column.php`
- `database/migrations/2026_11_08_000001_use_string_status_for_all_booking_tables.php`
- `database/migrations/2026_11_08_000002_add_gallery_images_to_hotels_table.php`

## database/seeders

- `database/seeders/DatabaseSeeder.php`

## docs

- `docs/~$nerate-final-defense-ppt.cjs`
- `docs/~$SpotTour_Final_Defense.pptx`
- `docs/~$SpotTour_Final_Defense_v2.pptx`
- `docs/generate-final-defense-ppt.cjs`
- `docs/SPOTTOUR_EVALUATION_CHAPTERS.html`
- `docs/SpotTour_Final_Defense.pptx`
- `docs/SpotTour_Final_Defense_v2.pptx`
- `docs/SPOTTOUR_GFORM_SETUP.md`
- `docs/spottour-evaluation-gform-apps-script.js`

## public

- `public/.htaccess`
- `public/_preview-cal.html`
- `public/_preview-dash.html`
- `public/favicon.ico`
- `public/index.php`
- `public/robots.txt`

## public/css

- `public/css/account-pages.css`
- `public/css/another.css`
- `public/css/app.css`
- `public/css/backup_css.css`
- `public/css/listing-details.css`
- `public/css/responsive.css`
- `public/css/style.css`
- `public/css/theme.css`
- `public/css/traveller-chat.css`
- `public/css/user-dashboard.css`

## public/docs

- `public/docs/spottour-class-diagram.png`
- `public/docs/spottour-sequence-diagram.png`

## public/images

- `public/images/listing-placeholder.svg`

## public/js

- `public/js/availability-calendar.js`
- `public/js/theme.js`
- `public/js/traveller-chat.js`

## resources/js

- `resources/js/app.js`

## resources/views

- `resources/views/admin.blade.php`
- `resources/views/auth.blade.php`
- `resources/views/owner.blade.php`
- `resources/views/user.blade.php`
- `resources/views/welcome.blade.php`

## resources/views/admin

- `resources/views/admin/account.blade.php`
- `resources/views/admin/activities.blade.php`
- `resources/views/admin/bookings.blade.php`
- `resources/views/admin/day-plans.blade.php`
- `resources/views/admin/edit-hotel.blade.php`
- `resources/views/admin/edit-spot.blade.php`
- `resources/views/admin/feedback.blade.php`
- `resources/views/admin/food-details.blade.php`
- `resources/views/admin/foods.blade.php`
- `resources/views/admin/hotel-details.blade.php`
- `resources/views/admin/hotels.blade.php`
- `resources/views/admin/notifications.blade.php`
- `resources/views/admin/owners.blade.php`
- `resources/views/admin/profile.blade.php`
- `resources/views/admin/spot-details.blade.php`
- `resources/views/admin/spots.blade.php`
- `resources/views/admin/users.blade.php`
- `resources/views/admin/visitor-report.blade.php`

## resources/views/admin/partials

- `resources/views/admin/partials/activity-row.blade.php`

## resources/views/auth

- `resources/views/auth/password-reset.blade.php`

## resources/views/chat

- `resources/views/chat/inbox.blade.php`

## resources/views/emails

- `resources/views/emails/booking-confirmation.blade.php`

## resources/views/owner

- `resources/views/owner/bookings.blade.php`
- `resources/views/owner/calendar.blade.php`
- `resources/views/owner/card-details.blade.php`
- `resources/views/owner/create-food.blade.php`
- `resources/views/owner/create-hotel.blade.php`
- `resources/views/owner/create-menu.blade.php`
- `resources/views/owner/create-spot.blade.php`
- `resources/views/owner/day-plans.blade.php`
- `resources/views/owner/edit-food.blade.php`
- `resources/views/owner/edit-hotel.blade.php`
- `resources/views/owner/edit-menu.blade.php`
- `resources/views/owner/edit-spot.blade.php`
- `resources/views/owner/feedback.blade.php`
- `resources/views/owner/food-bookings.blade.php`
- `resources/views/owner/food-details.blade.php`
- `resources/views/owner/food-menus.blade.php`
- `resources/views/owner/foods.blade.php`
- `resources/views/owner/hotel-details.blade.php`
- `resources/views/owner/hotel-rooms.blade.php`
- `resources/views/owner/hotels.blade.php`
- `resources/views/owner/listings.blade.php`
- `resources/views/owner/notifications.blade.php`
- `resources/views/owner/packages.blade.php`
- `resources/views/owner/profile.blade.php`
- `resources/views/owner/reviews.blade.php`
- `resources/views/owner/spot-details.blade.php`
- `resources/views/owner/spots.blade.php`

## resources/views/owner/partials

- `resources/views/owner/partials/arrival.blade.php`
- `resources/views/owner/partials/payment-proof.blade.php`

## resources/views/partials

- `resources/views/partials/arrival-code.blade.php`
- `resources/views/partials/notification-menu.blade.php`
- `resources/views/partials/rating-board.blade.php`
- `resources/views/partials/score-stars.blade.php`
- `resources/views/partials/star-input.blade.php`
- `resources/views/partials/workspace-sidebar.blade.php`

## resources/views/user

- `resources/views/user/booking-confirm.blade.php`
- `resources/views/user/booking-paid.blade.php`
- `resources/views/user/bookings.blade.php`
- `resources/views/user/bookmarks.blade.php`
- `resources/views/user/checklists.blade.php`
- `resources/views/user/day-plan.blade.php`
- `resources/views/user/edit-comment.blade.php`
- `resources/views/user/edit-food-comment.blade.php`
- `resources/views/user/edit-hotel-comment.blade.php`
- `resources/views/user/edit-reply.blade.php`
- `resources/views/user/favorites.blade.php`
- `resources/views/user/feedback.blade.php`
- `resources/views/user/food-booking.blade.php`
- `resources/views/user/food-booking-confirm.blade.php`
- `resources/views/user/food-details.blade.php`
- `resources/views/user/food-payment.blade.php`
- `resources/views/user/foods.blade.php`
- `resources/views/user/hotel-booking.blade.php`
- `resources/views/user/hotel-booking-confirm.blade.php`
- `resources/views/user/hotel-details.blade.php`
- `resources/views/user/hotel-payment.blade.php`
- `resources/views/user/hotels.blade.php`
- `resources/views/user/notifications.blade.php`
- `resources/views/user/payment.blade.php`
- `resources/views/user/profile.blade.php`
- `resources/views/user/search.blade.php`
- `resources/views/user/spot-booking.blade.php`
- `resources/views/user/spot-details.blade.php`
- `resources/views/user/spots.blade.php`

## resources/views/user/partials

- `resources/views/user/partials/account-bar.blade.php`
- `resources/views/user/partials/chat-widget.blade.php`
- `resources/views/user/partials/food-menu-picker.blade.php`
- `resources/views/user/partials/payment-choice.blade.php`

## routes

- `routes/console.php`
- `routes/web.php`

## tests/Feature

- `tests/Feature/ExampleTest.php`

## tests

- `tests/TestCase.php`

## tests/Unit

- `tests/Unit/ExampleTest.php`
