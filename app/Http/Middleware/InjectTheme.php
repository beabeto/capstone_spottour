<?php

namespace App\Http\Middleware;

use App\Models\Notification;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class InjectTheme
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $type = (string) $response->headers->get('Content-Type', '');
        if ($type !== '' && ! str_contains($type, 'text/html')) {
            return $response;
        }

        $html = $response->getContent();
        if (! is_string($html) || ! str_contains($html, '</head>') || ! str_contains($html, '</body>')) {
            return $response;
        }

        if (! str_contains($html, '/js/theme.js')) {
            $assets = '<link rel="stylesheet" href="'.asset('css/theme.css').'?v='.filemtime(public_path('css/theme.css')).'">'
                .'<link rel="stylesheet" href="'.asset('css/responsive.css').'?v='.filemtime(public_path('css/responsive.css')).'">'
                .'<script src="'.asset('js/theme.js').'?v='.filemtime(public_path('js/theme.js')).'"></script>';
            $html = str_replace('</head>', $assets.'</head>', $html);
        } elseif (! str_contains($html, '/css/responsive.css')) {
            $responsive = '<link rel="stylesheet" href="'.asset('css/responsive.css').'?v='.filemtime(public_path('css/responsive.css')).'">';
            $html = str_replace('</head>', $responsive.'</head>', $html);
        }

        if (! str_contains($html, 'theme-toggle')) {
            $button = '<button type="button" class="theme-toggle" aria-label="Dark mode" onclick="window.spottourToggleTheme && window.spottourToggleTheme()"></button>';
            $html = str_replace('</body>', $button.'</body>', $html);
        }

        $isLoggedInTraveller = Auth::check()
            && Auth::user()?->role === 'user';
        $isAuthSurface = $request->is('/')
            || $request->is('login')
            || $request->is('register')
            || $request->is('password')
            || $request->is('password/*');

        if ($isLoggedInTraveller && ! $isAuthSurface && ! str_contains($html, 'id="traveller-chat"')) {
            $widget = view('user.partials.chat-widget')->render();
            $html = str_replace('</body>', $widget.'</body>', $html);
        }

        if (Auth::user()?->role === 'admin' && ! str_contains($html, 'notification-icon')) {
            $bell = view('partials.notification-menu', [
                'notifications' => Notification::where('user_id', Auth::id())->latest()->take(6)->get(),
                'unreadCount' => Notification::where('user_id', Auth::id())->where('is_read', 0)->count(),
                'allUrl' => '/admin/notifications',
            ])->render();
            $html = str_replace('</body>', '<div class="admin-bell-float">'.$bell.'</div></body>', $html);
        }

        $response->setContent($html);

        return $response;
    }
}
