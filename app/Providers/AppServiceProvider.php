<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
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
        RateLimiter::for('survey-ratings', function (Request $request) {
            // Keyed per application, not per IP - satu app yang "nakal" tidak
            // memblokir app lain.
            $appKey = $request->header('X-App-Key', 'unknown');
            return Limit::perMinute(30)->by($appKey);
        });
    }
}
