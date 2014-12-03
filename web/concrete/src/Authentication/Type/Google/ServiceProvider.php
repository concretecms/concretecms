<?php
namespace Concrete\Core\Authentication\Type\Google;

use Concrete\Core\Application\Application;
use OAuth\Common\Consumer\Credentials;
use OAuth\Common\Storage\SymfonySession;
use OAuth\OAuth2\Service\Google;
use OAuth\ServiceFactory;
use OAuth\UserData\ExtractorFactory;

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
            '/system/authentication/google/attempt_auth',
            'Concrete\Authentication\Google\Controller::handle_authentication_attempt');
        \Route::register(
            '/system/authentication/google/callback',
            'Concrete\Authentication\Google\Controller::handle_authentication_callback');

        // Attaching
        \Route::register(
            '/system/authentication/google/attempt_attach',
            'Concrete\Authentication\Google\Controller::handle_attach_attempt');
        \Route::register(
            '/system/authentication/google/attach_callback',
            'Concrete\Authentication\Google\Controller::handle_attach_callback');

        /** @var ExtractorFactory $factory */
        $factory = $this->app->make('oauth/factory/extractor');
        $factory->addExtractorMapping('OAuth\\OAuth2\\Service\\Google',
                                      'Concrete\\Core\\Authentication\\Type\\Google\\Extractor\\Google');

        $this->app->bindShared(
            'authentication/google',
            function (Application $app, $callback = '/system/authentication/google/callback/') {
                /** @var ServiceFactory $factory */
                $factory = $app->make('oauth/factory/service', array(CURLOPT_SSL_VERIFYPEER => false));

                return $factory->createService(
                    'google',
                    new Credentials(
                        \Config::get('auth.google.appid'),
                        \Config::get('auth.google.secret'),
                        BASE_URL . DIR_REL . \URL::to($callback)
                    ),
                    new SymfonySession(\Session::getFacadeRoot(), false),
                    array(Google::SCOPE_EMAIL, Google::SCOPE_PROFILE));
            });
    }

}
