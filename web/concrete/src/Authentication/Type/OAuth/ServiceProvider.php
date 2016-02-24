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
        $this->app->bind('oauth/factory/service', function ($app, $params = array()) {
            $factory = new ServiceFactory();
            $factory->setHttpClient($client = new CurlClient());
            $client->setCurlParameters((array) $params);

            return $factory;
        });
        $this->app->bindShared('oauth/factory/extractor', function () {
            return new ExtractorFactory();
        });

        $this->app->bind('oauth_extractor', function ($app, $params = array()) {
            if (!is_array($params)) {
                $params = array($params);
            }

            if (!$service = head($params)) {
                throw new \InvalidArgumentException('No Service given.');
            }

            $extractor_factory = $app->make('oauth/factory/extractor');

            return $extractor_factory->get($service);
        });

        \Route::register(
            '/ccm/system/authentication/oauth2/{type}/{action}',
            function ($type, $action) {
                try {
                    $type = \AuthenticationType::getByHandle($type);
                    if ($type && is_object($type) && !$type->isError()) {
                        /** @var GenericOauthTypeController $controller */
                        $controller = $type->getController();
                        if ($controller instanceof GenericOauthTypeController) {
                            switch ($action) {
                                case 'attempt_auth':
                                    $controller->handle_authentication_attempt();
                                    break;
                                case 'callback':
                                    $controller->handle_authentication_callback();
                                    break;
                                case 'attempt_attach':
                                    $controller->handle_attach_attempt();
                                    break;
                                case 'attach_callback':
                                    $controller->handle_attach_callback();
                                    break;
                            }
                        }
                    }
                } catch (\Exception $e) {
                    \Log::addNotice('OAUTH ERROR: ' . $e->getMessage());
                }
            });
    }
}
