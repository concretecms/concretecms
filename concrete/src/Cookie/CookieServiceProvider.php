<?php

namespace Concrete\Core\Cookie;

use Concrete\Core\Foundation\Service\Provider as ServiceProvider;

class CookieServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(ResponseCookieJar::class);
        $this->app->singleton(CookieJar::class);
        $this->app->alias(CookieJar::class, 'cookie');
    }
}
