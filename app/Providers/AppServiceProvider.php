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
        if (file_exists(base_path('../public_html'))) {
            $this->app->bind('path.public', function () {
                return realpath(base_path('../public_html'));
            });
        }
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if (config('app.env') === 'production' || request()->server('HTTP_X_FORWARDED_PROTO') === 'https') {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }

        \Illuminate\Pagination\Paginator::useBootstrapFive();
        
        // Auto-run migrations in production if schema is missing newer tables or columns
        try {
            $needsMigrate = !\Illuminate\Support\Facades\Schema::hasTable('job_fair_visitors') ||
                !\Illuminate\Support\Facades\Schema::hasColumn('job_fair_visits', 'job_opportunity_id') ||
                !\Illuminate\Support\Facades\Schema::hasColumn('job_opportunities', 'job_fair_id') ||
                !\Illuminate\Support\Facades\Schema::hasColumn('trainings', 'start_time');

            if (!$needsMigrate && !$this->app->runningUnitTests() && !\Illuminate\Support\Facades\Cache::get('schema_500_fix_20261029_applied')) {
                if (\Illuminate\Support\Facades\Schema::hasTable('migrations')) {
                    $hasFixMigration = \Illuminate\Support\Facades\DB::table('migrations')
                        ->where('migration', '2026_10_29_120000_fix_graduate_and_job_fair_columns_for_500_prevention')
                        ->exists();
                    if ($hasFixMigration) {
                        \Illuminate\Support\Facades\Cache::put('schema_500_fix_20261029_applied', true, 86400);
                    } else {
                        $needsMigrate = true;
                    }
                }
            }

            if ($needsMigrate) {
                \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
                if (!$this->app->runningUnitTests()) {
                    \Illuminate\Support\Facades\Cache::put('schema_500_fix_20261029_applied', true, 86400);
                }
            }
        } catch (\Throwable $e) {
            // Ignore database connection failures during asset/cache commands
        }

        // Share unread notifications & pending graduate registrations count with all layouts
        View::composer(['layouts.app', 'layouts.training-coordinator', 'layouts.*', 'admin.*', 'career-guidance.*'], function ($view) {
            if (Auth::check()) {
                $user = Auth::user();
                $unreadCount = 0;
                $pendingGraduatesCount = 0;

                try {
                    $unreadCount = Notification::where('user_id', $user->id)
                        ->where('is_read', false)
                        ->count();

                    if (in_array($user->role, ['admin', 'career_guidance_officer'])) {
                        $pendingGraduatesCount = \App\Models\User::where('role', 'graduate')
                            ->where('is_approved', false)
                            ->count();
                    }
                } catch (\Throwable $e) {
                    // Fallback gracefully if tables are temporarily not accessible
                    $unreadCount = 0;
                    $pendingGraduatesCount = 0;
                }

                $view->with('unreadNotificationsCount', $unreadCount);
                $view->with('pendingGraduatesCount', $pendingGraduatesCount);
            } else {
                $view->with('unreadNotificationsCount', 0);
                $view->with('pendingGraduatesCount', 0);
            }
        });
    }
}
