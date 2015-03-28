<?php
namespace Concrete\Core\Authentication\Type\Google;

use Concrete\Core\Application\Application;
use Config;
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
        /** @var ExtractorFactory $factory */
        $extractor = $this->app->make('oauth/factory/extractor');
        $extractor->addExtractorMapping(
            'OAuth\\OAuth2\\Service\\Google',
            'Concrete\\Core\\Authentication\\Type\\Google\\Extractor\\Google');

        $this->app->bindShared(
            'authentication/google',
            function (Application $app, $callback = '/ccm/system/authentication/oauth2/google/callback/') {
                /** @var ServiceFactory $factory */
                $factory = $app->make('oauth/factory/service', array(CURLOPT_SSL_VERIFYPEER => Config::get('app.curl.verifyPeer')));

                return $factory->createService(
                    'google',
                    new Credentials(
                        \Config::get('auth.google.appid'),
                        \Config::get('auth.google.secret'),
                        (string) \URL::to($callback)
                    ),
                    new SymfonySession(\Session::getFacadeRoot(), false),
                    array(Google::SCOPE_EMAIL, Google::SCOPE_PROFILE));
            }
        );
    }

}
