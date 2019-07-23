<?php
namespace Concrete\Core\Session;

use Concrete\Core\Application\Application;
use Concrete\Core\Foundation\Service\Provider as ServiceProvider;
use Concrete\Core\Logging\Channels;
use Concrete\Core\Logging\LoggerFactory;
use Psr\Log\LoggerInterface;

class SessionServiceProvider extends ServiceProvider
{
    public function register()
    {
        // Supply the deprecated static session accessor with a real application object
        Session::setApplicationObject($this->app);

        $this->app->bind('Concrete\Core\Session\SessionValidatorInterface', SessionValidator::class);
        $this->app->singleton(SessionValidator::class);
        $this->app->bind('Concrete\Core\Session\SessionFactoryInterface', 'Concrete\Core\Session\SessionFactory');

        $this->app->singleton('session', function ($app) {
            return $app->make('Concrete\Core\Session\SessionFactoryInterface')->createSession();
        });
        $this->app->bind('Symfony\Component\HttpFoundation\Session\Session', 'session');
    }
}
