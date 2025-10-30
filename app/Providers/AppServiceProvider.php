<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
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
        $this->configureSecureUrls();

        RateLimiter::for('guest-try-upload', function ($request) {
            return [Limit::perDay(5)->by('try:'.$request->ip())];
        });
    }

    protected function configureSecureUrls()
    {
        // Determine if HTTPS should be enforced
        $enforceHttps = $this->app->environment(['production', 'staging'])
            && ! $this->app->runningUnitTests();

        // Force HTTPS for all generated URLs
        if ($enforceHttps) {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }

        // Ensure proper server variable is set
        if ($enforceHttps) {
            $this->app['request']->server->set('HTTPS', 'on');
        }

    }
}
