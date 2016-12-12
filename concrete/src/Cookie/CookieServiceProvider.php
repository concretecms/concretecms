<?php
namespace Concrete\Core\Cookie;

use Concrete\Core\Foundation\Service\Provider as ServiceProvider;

class CookieServiceProvider extends ServiceProvider
{

    public function register()
    {
        $this->app->singleton(
            'cookie',
            '\Concrete\Core\Cookie\CookieJar'
        );
    }

}
