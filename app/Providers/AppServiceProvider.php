<?php

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Illuminate\Http\Request;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $this->configureSecureUrls();

        RateLimiter::for('guest-try-upload', function (Request $request) {
            return [ Limit::perDay(5)->by('try:'.$request->ip()) ];
        });
    }

    protected function configureSecureUrls(): void
    {
        $enforceHttps = $this->app->environment(['production', 'staging'])
            && ! $this->app->runningUnitTests();

        if ($enforceHttps) {
            URL::forceScheme('https');
            // Ofta onödigt om TrustProxies/Nginx är rätt, men skadar inte:
            $this->app['request']->server->set('HTTPS', 'on');
        }

    }
}
