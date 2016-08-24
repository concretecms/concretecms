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

        $app = $this->app;

        if ($this->app->bound('director')) {
            // Add an event listener that renews session
            $this->app['director']->addListener('on_before_render', function () use ($app) {
                /** @var Session $session */
                $session = $app['session'];

                if ($session->get('uID', 0) > 0) {
                    // If the user is logged in, update the session so that we have more time on every page load
                    $session->migrate();
                    $session->getMetadataBag()->stampNew();
                }
            });
        }
    }
}
