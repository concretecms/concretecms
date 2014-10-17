<?php
namespace Concrete\Core\Authentication\Type\Community;

use OAuth\Common\Consumer\Credentials;
use OAuth\Common\Storage\SymfonySession;
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
            '/system/authentication/community/attempt_auth',
            'Concrete\\Authentication\\Community\\Controller::handle_authentication_attempt');
        \Route::register(
            '/system/authentication/community/callback',
            'Concrete\\Authentication\\Community\\Controller::handle_authentication_callback');

        // Attaching
        \Route::register(
            '/system/authentication/community/attempt_attach',
            'Concrete\\Authentication\\Community\\Controller::handle_attach_attempt');
        \Route::register(
            '/system/authentication/community/attach_callback',
            'Concrete\\Authentication\\Community\\Controller::handle_attach_callback');

        /** @var ExtractorFactory $factory */
        $factory = $this->app->make('oauth_extractor_factory');
        $factory->addExtractorMapping('Concrete\\Core\\Authentication\\Type\\Community\\Service\\Community',
                                      'Concrete\\Core\\Authentication\\Type\\Community\\Extractor\\Community');

        $this->app->bindShared(
            'community_service',
            function ($app, $callback = '/system/authentication/community/callback/') {
                /** @var ServiceFactory $factory */
                $factory = $app->make('oauth_service_factory');
                $factory->registerService('community', '\\Concrete\\Core\\Authentication\\Type\\Community\\Service\\Community');
                return $factory->createService(
                    'community',
                    new Credentials(
                        \Config::get('auth.community.appid'),
                        \Config::get('auth.community.secret'),
                        BASE_URL . DIR_REL . \URL::to($callback)
                    ),
                    new SymfonySession(\Session::getFacadeRoot(), false));
            });
    }

}
