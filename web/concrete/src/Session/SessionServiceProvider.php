<?php
namespace Concrete\Core\Session;

use Concrete\Core\Foundation\Service\Provider as ServiceProvider;

class SessionServiceProvider extends ServiceProvider
{
    public function register()
    {
        // Supply the deprecated static session accessor with a real application object
        Session::setApplicationObject($this->app);

        $this->app->bind('Concrete\Core\Session\SessionValidatorInterface', 'Concrete\Core\Session\SessionValidator');
        $this->app->bind('Concrete\Core\Session\SessionFactoryInterface', 'Concrete\Core\Session\SessionFactory');

        $this->app->singleton('session', function ($app) {
            return $app->make('Concrete\Core\Session\SessionFactoryInterface')->createSession();
        });
        $this->app->bind('Symfony\Component\HttpFoundation\Session\Session', 'session');
    }
}
