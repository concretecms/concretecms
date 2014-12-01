<?php
namespace Concrete\Core\Authentication\Type\OAuth;

use Concrete\Core\Foundation\Service\Provider;
use OAuth\Common\Http\Client\CurlClient;
use OAuth\ServiceFactory;
use OAuth\UserData\ExtractorFactory;

class ServiceProvider extends Provider
{

    public function register()
    {
        $this->app->bind(
            'oauth_service_factory',
            function ($app, $params = array()) {
                $factory = new ServiceFactory();
                $factory->setHttpClient($client = new CurlClient());
                $client->setCurlParameters((array) $params);

                return $factory;
            });
        $this->app->bindShared(
            'oauth_extractor_factory',
            function () {
                return new ExtractorFactory();
            });

        $this->app->bind(
            'oauth_extractor',
            function ($app, $service) {
                $extractor_factory = $app->make('oauth_extractor_factory');
                return $extractor_factory->get($service);
            });
    }

}
