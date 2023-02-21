<?php

namespace App\Providers;

use App\Contracts\Services\OAuthContract;
use App\Http\Services\OAuthService;
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
        $this->app->bind(OAuthContract::class , OAuthService::class);
    }
}
