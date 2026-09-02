<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use App\Models\Notification;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Share unread notifications & pending graduate registrations count with all layouts
        View::composer(['layouts.app', 'layouts.training-coordinator', 'layouts.*', 'admin.*', 'career-guidance.*'], function ($view) {
            if (Auth::check()) {
                $user = Auth::user();
                $unreadCount = Notification::where('user_id', $user->id)
                    ->where('is_read', false)
                    ->count();
                $view->with('unreadNotificationsCount', $unreadCount);

                if (in_array($user->role, ['admin', 'career_guidance_officer'])) {
                    $pendingGraduatesCount = \App\Models\User::where('role', 'graduate')
                        ->where('is_approved', false)
                        ->count();
                    $view->with('pendingGraduatesCount', $pendingGraduatesCount);
                } else {
                    $view->with('pendingGraduatesCount', 0);
                }
            } else {
                $view->with('unreadNotificationsCount', 0);
                $view->with('pendingGraduatesCount', 0);
            }
        });
    }
}
