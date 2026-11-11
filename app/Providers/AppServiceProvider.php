<?php

namespace App\Providers;

use App\Support\EnsureLiveSchema;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use App\Models\Notification;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::defaultStringLength(191);

        if (str_starts_with((string) config('app.url'), 'https://')) {
            URL::forceScheme('https');
            config(['session.secure' => true]);
        }

        EnsureLiveSchema::run();

        // Keep the bell badge and its dropdown data available anywhere the
        // shared notification partial is rendered, including standalone pages.
        View::composer('partials.notification-menu', function ($view): void {
            $data = $view->getData();
            $user = Auth::user();

            if (!$user) {
                return;
            }

            $view->with('unreadCount', $data['unreadCount'] ?? Notification::where('user_id', $user->id)->where('is_read', false)->count());
            $view->with('notifications', $data['notifications'] ?? Notification::where('user_id', $user->id)->latest()->take(6)->get());
            $view->with('allUrl', $data['allUrl'] ?? ($user->role === 'admin' ? '/admin/notifications' : ($user->role === 'owner' ? '/owner/notifications' : '/user/notifications')));
        });
    }
}
