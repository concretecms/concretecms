<?php
namespace Concrete\Core\Authentication\Type\Twitter;

use OAuth\Common\Consumer\Credentials;
use OAuth\Common\Storage\SymfonySession;
use OAuth\ServiceFactory;

class ServiceProvider extends \Concrete\Core\Foundation\Service\Provider
{

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        // Authentication
        \Route::register(
            '/system/authentication/twitter/attempt_auth',
            'Concrete\Authentication\Twitter\Controller::handle_authentication_attempt');
        \Route::register(
            '/system/authentication/twitter/callback',
            'Concrete\Authentication\Twitter\Controller::handle_authentication_callback');

        // Attaching
        \Route::register(
            '/system/authentication/twitter/attempt_attach',
            'Concrete\Authentication\Twitter\Controller::handle_attach_attempt');
        \Route::register(
            '/system/authentication/twitter/attach_callback',
            'Concrete\Authentication\Twitter\Controller::handle_attach_callback');

        $this->app->bindShared(
            'twitter_service',
            function ($app, $callback = '/system/authentication/twitter/callback/') {
                /** @var ServiceFactory $factory */
                $factory = $app->make('oauth_service_factory');
                return $factory->createService(
                    'twitter',
                    new Credentials(
                        \Config::get('auth.twitter.appid'),
                        \Config::get('auth.twitter.secret'),
                        BASE_URL . DIR_REL . \URL::to($callback)
                    ),
                    new SymfonySession(\Session::getFacadeRoot(), false));
            });
    }

}
