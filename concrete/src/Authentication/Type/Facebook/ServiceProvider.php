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
        $this->app->bindShared(
            'authentication/facebook',
            function ($app, $callback = '/ccm/system/authentication/oauth2/facebook/callback/') {
                /** @var ServiceFactory $factory */
                $factory = $app->make('oauth/factory/service');
                return $factory->createService(
                    'facebook',
                    new Credentials(
                        \Config::get('auth.facebook.appid'),
                        \Config::get('auth.facebook.secret'),
                        (string) \URL::to($callback)
                    ),
                    new SymfonySession(\Session::getFacadeRoot(), false),
                    array('email'));
            }
        );
    }

}
