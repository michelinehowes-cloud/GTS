<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use App\Models\User;
use App\Models\Company;
use App\Models\Training;
use App\Models\JobOpportunity;
use App\Models\GraduateData;
use App\Policies\UserPolicy;
use App\Policies\CompanyPolicy;
use App\Policies\TrainingPolicy;
use App\Policies\JobOpportunityPolicy;
use App\Policies\GraduateDataPolicy;
use App\Models\Nomination;
use App\Policies\NominationPolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        User::class => UserPolicy::class,
        Company::class => CompanyPolicy::class,
        Training::class => TrainingPolicy::class,
        JobOpportunity::class => JobOpportunityPolicy::class,
        GraduateData::class => GraduateDataPolicy::class,
        Nomination::class => NominationPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        // تسجيل صلاحيات إضافية باستخدام Gates
        \Gate::define('manage-users', function ($user) {
            return $user->role === 'admin';
        });

        \Gate::define('manage-companies', function ($user) {
            return in_array($user->role, ['admin', 'partnership_officer']);
        });

        \Gate::define('manage-trainings', function ($user) {
            return in_array($user->role, ['admin', 'training_coordinator']);
        });

        \Gate::define('manage-job-opportunities', function ($user) {
            return in_array($user->role, ['admin', 'partnership_officer']);
        });

        \Gate::define('manage-graduates', function ($user) {
            return in_array($user->role, ['admin', 'career_guidance_officer']);
        });

        \Gate::define('manage-nominations', function ($user) {
            return in_array($user->role, ['admin', 'partnership_officer', 'career_guidance_officer']);
        });

        \Gate::define('view-reports', function ($user) {
            return in_array($user->role, [
                'admin',
                'training_coordinator',
                'partnership_officer',
                'career_guidance_officer',
                'evaluation_followup'
            ]);
        });

        \Gate::define('export-data', function ($user) {
            return in_array($user->role, [
                'admin',
                'training_coordinator',
                'partnership_officer',
                'career_guidance_officer',
                'evaluation_followup'
            ]);
        });
    }
}
