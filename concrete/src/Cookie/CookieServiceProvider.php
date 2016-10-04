<?php
namespace Concrete\Core\Cookie;

use Concrete\Core\Foundation\Service\Provider as ServiceProvider;

class CookieServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton('\Concrete\Core\Cookie\CookieJar');
        $this->app->bind('cookie', '\Concrete\Core\Cookie\CookieJar');
    }
}
