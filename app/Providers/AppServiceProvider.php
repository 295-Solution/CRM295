<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
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
        Gate::define('manage-users', fn (User $user): bool => $user->isSuperAdmin());

        RateLimiter::for('login', function (Request $request): Limit {
            return Limit::perMinute(20)->by((string) $request->ip());
        });

        RateLimiter::for('api-read', function (Request $request): Limit {
            $user = $request->user();

            if (! $user) {
                return Limit::perMinute(60)->by('ip:'.$request->ip());
            }

            $limit = $user->isAdmin() ? 240 : 120;

            return Limit::perMinute($limit)->by('user:'.$user->id);
        });

        RateLimiter::for('api-write', function (Request $request): Limit {
            $user = $request->user();

            if (! $user) {
                return Limit::perMinute(20)->by('ip:'.$request->ip());
            }

            $limit = $user->isAdmin() ? 120 : 60;

            return Limit::perMinute($limit)->by('user:'.$user->id);
        });
    }
}
