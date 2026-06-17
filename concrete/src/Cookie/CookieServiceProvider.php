<?php

namespace Concrete\Core\Cookie;

use Concrete\Core\Foundation\Service\Provider as ServiceProvider;
use Concrete\Core\Http\Request;

class CookieServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(ResponseCookieJar::class);
        $this->app->extend(ResponseCookieJar::class, function (ResponseCookieJar $jar): ResponseCookieJar {
            return $jar->setSecureDefault($this->app->make(Request::class)->isSecure());
        });
        $this->app->singleton(CookieJar::class);
        $this->app->alias(CookieJar::class, 'cookie');
    }
}
