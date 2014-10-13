<?php
namespace Concrete\Core\Authentication\Type\Facebook;

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
            '/system/authentication/facebook/attempt_auth',
            'Concrete\Authentication\Facebook\Controller::handle_authentication_attempt');
        \Route::register(
            '/system/authentication/facebook/callback',
            'Concrete\Authentication\Facebook\Controller::handle_authentication_callback');

        // Attaching
        \Route::register(
            '/system/authentication/facebook/attempt_attach',
            'Concrete\Authentication\Facebook\Controller::handle_attach_attempt');
        \Route::register(
            '/system/authentication/facebook/attach_callback',
            'Concrete\Authentication\Facebook\Controller::handle_attach_callback');

        $this->app->bindShared(
            'facebook_service',
            function ($app, $callback = '/system/authentication/facebook/callback/') {
                /** @var ServiceFactory $factory */
                $factory = $app->make('oauth_service_factory');
                return $factory->createService(
                    'facebook',
                    new Credentials(
                        \Config::get('auth.facebook.appid'),
                        \Config::get('auth.facebook.secret'),
                        BASE_URL . DIR_REL . \URL::to($callback)
                    ),
                    new SymfonySession(\Session::getFacadeRoot(), false));
            });
    }

}
