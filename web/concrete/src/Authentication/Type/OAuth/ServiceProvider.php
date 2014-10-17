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
        $this->app->bindShared('oauth_service_factory', function() {
            $factory = new ServiceFactory();
            $factory->setHttpClient(new CurlClient());

            return $factory;
        });
        $this->app->bindShared('oauth_extractor_factory', function() {
            return new ExtractorFactory();
        });

        $this->app->bind('oauth_extractor', function($app, $service) {
            $extractor_factory = $app->make('oauth_extractor_factory');
            return $extractor_factory->get($service);
        });
    }

}
