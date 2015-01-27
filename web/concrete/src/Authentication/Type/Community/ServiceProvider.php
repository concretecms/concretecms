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
        /** @var ExtractorFactory $extractor */
        $extractor = $this->app->make('oauth/factory/extractor');
        $extractor->addExtractorMapping(
            'Concrete\\Core\\Authentication\\Type\\Community\\Service\\Community',
            'Concrete\\Core\\Authentication\\Type\\Community\\Extractor\\Community');

        /** @var ServiceFactory $factory */
        $factory = $this->app->make('oauth/factory/service');
        $factory->registerService('community', '\\Concrete\\Core\\Authentication\\Type\\Community\\Service\\Community');

        $this->app->bindShared(
            'authentication/community',
            function ($app, $callback = '/ccm/system/authentication/oauth2/community/callback/') use ($factory) {
                return $factory->createService(
                    'community',
                    new Credentials(
                        \Config::get('auth.community.appid'),
                        \Config::get('auth.community.secret'),
                        BASE_URL . \URL::to($callback)
                    ),
                    new SymfonySession(\Session::getFacadeRoot(), false));
            });
    }

}
