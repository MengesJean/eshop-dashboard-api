<?php

namespace App\Providers;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

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
        // Admin = passe partout (évite de passer dans les conditions "if")
        Gate::before(fn ($user, $ability): bool =>
             $user->role === UserRole::Admin ? true : null
        );

        // Qui a le droit d'obtenir un token/backoffice login
        Gate::define('access-backoffice', fn ($user): bool =>
            in_array($user->role, [UserRole::Admin, UserRole::Restricted], true)
        );

        Gate::define('access-dashboard', fn (User $user): bool =>
            $user->role === UserRole::Admin
        );

        Gate::define('access-restricted-dashboard', fn (User $user): bool =>
            $user->role === UserRole::Restricted
        );
    }
}
