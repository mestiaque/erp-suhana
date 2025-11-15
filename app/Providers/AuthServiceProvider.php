<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\User;
use Illuminate\Container\Attributes\Log;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [

    ];

    public function boot(): void
    {
        $this->registerPolicies();
        Gate::before(function (?User $user, string $ability) {
            if ($user && $user->hasPermission($ability)) {
                return true;
            }

            return null;
        });
    }
}
