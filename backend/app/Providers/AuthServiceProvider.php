<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\User;
use App\Policies\UserPolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     * Register model => policy mapping here.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // Map model to its corresponding policy class
        \App\Models\User::class => \App\Policies\UserPolicy::class,

    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Example Gate definition, you can define global abilities here
        Gate::define('is-admin', function (User $user) {
            return $user->role === 'admin';
        });

        Gate::define('is-active', function (User $user) {
            return $user->active;
        });

        // Gate::before(function ($user, $ability) {
        //     return true;
        // });
    }
}
