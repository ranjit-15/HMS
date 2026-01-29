<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use App\Models\AdminNotification;

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
        // Share unread admin notification count with all views for authenticated users
        View::composer('*', function ($view) {
            $user = Auth::user();
            if ($user) {
                $unread = AdminNotification::forUser($user->id)
                    ->whereDoesntHave('readByUsers', function ($q) use ($user) {
                        $q->where('user_id', $user->id);
                    })->count();
                $view->with('unreadNotificationsCount', $unread);
            } else {
                $view->with('unreadNotificationsCount', 0);
            }
        });
    }
}
