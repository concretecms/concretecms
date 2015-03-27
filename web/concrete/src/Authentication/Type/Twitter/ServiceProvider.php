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
        $this->app->bindShared(
            'authentication/twitter',
            function ($app, $callback = '/ccm/system/authentication/oauth2/twitter/callback/') {
                /** @var ServiceFactory $factory */
                $factory = $app->make('oauth/factory/service');
                return $factory->createService(
                    'twitter',
                    new Credentials(
                        \Config::get('auth.twitter.appid'),
                        \Config::get('auth.twitter.secret'),
                        (string) \URL::to($callback)
                    ),
                    new SymfonySession(\Session::getFacadeRoot(), false));
            }
        );
    }

}
