<?php

namespace App\Providers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        Gate::define('properties-manage',function(User $user){
            return $user->role_id === Role::OWNER_ROLE;
        });

        Gate::define('bookings-manage',function(User $user){
            return $user->role_id === Role::USER_ROLE;
        });
    }
}
