<?php
namespace Concrete\Core\Authentication\Type\Facebook;

use OAuth\Common\Consumer\Credentials;
use OAuth\Common\Storage\SymfonySession;
use OAuth\ServiceFactory;

class ServiceProvider extends \Concrete\Core\Foundation\Service\Provider
{
    /**
     * Register the service provider.
     */
    public function register()
    {
        $this->app->singleton(
            'authentication/facebook',
            function ($app, $callback = '/ccm/system/authentication/oauth2/facebook/callback/') {
                /** @var \Concrete\Core\Application\Application $app */
                /** @var \Config $config */
                $config = $app['config'];
                /** @var ServiceFactory $factory */
                $factory = $app->make('oauth/factory/service', array(CURLOPT_SSL_VERIFYPEER => $config->get('app.curl.verifyPeer')));
                return $factory->createService(
                    'facebook',
                    new Credentials(
                        $config->get('auth.facebook.appid'),
                        $config->get('auth.facebook.secret'),
                        (string) \URL::to($callback)
                    ),
                    new SymfonySession(\Session::getFacadeRoot(), false),
                    array('email'));
            }
        );
    }
}
