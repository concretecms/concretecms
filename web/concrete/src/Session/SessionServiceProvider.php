<?php
namespace Concrete\Core\Session;

use Concrete\Core\Foundation\Service\Provider as ServiceProvider;

class SessionServiceProvider extends ServiceProvider
{

    public function register()
    {

        $this->app->singleton(
            'session',
            function () {
                return Session::start();
            });

    }

}
