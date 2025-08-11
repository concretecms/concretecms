<?php

namespace Concrete\Core\Authentication\Type\OAuth;

use Concrete\Core\Authentication\AuthenticationType;
use Concrete\Core\Error\UserMessageException;
use Concrete\Core\Foundation\Service\Provider;
use Concrete\Core\Logging\Channels;
use Concrete\Core\Routing\RouterInterface;
use OAuth\ServiceFactory;
use OAuth\UserData\ExtractorFactory;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class ServiceProvider extends Provider
{
    public function register()
    {
        $this->app->bind('oauth/factory/service', static function ($app) {
            $factory = new ServiceFactory();
            $factory->setHttpClient($app->make(HttpClient::class));

            return $factory;
        });
        $this->app->singleton('oauth/factory/extractor', static function () {
            return new ExtractorFactory();
        });
        $this->app->bind('oauth_extractor', static function ($app, $params = []) {
            if (!is_array($params)) {
                $params = [$params];
            }
            if (!$service = head($params)) {
                throw new \InvalidArgumentException('No Service given.');
            }
            $extractor_factory = $app->make('oauth/factory/extractor');

            return $extractor_factory->get($service);
        });

        $this->app->make(RouterInterface::class)->register(
            '/ccm/system/authentication/oauth2/{type}/{action}',
            function ($type, $action) {
                return $this->handle($type, $action);
            }
        );
    }

    private function handle(string $type, string $action): Response
    {
        try {
            $type = AuthenticationType::getByHandle($type);
        } catch (Throwable $_) {
            $type = null;
        }
        if (!is_object($type) || $type->isError()) {
            throw new UserMessageException(t('Invalid OAuth2 authentication type'));
        }
        try {
            $controller = $type->getController();
            if (!$controller instanceof GenericOauthTypeController) {
                throw new UserMessageException(t('Invalid OAuth2 controller'));
            }
            switch ($action) {
                case 'attempt_auth':
                    return $controller->handle_authentication_attempt();
                case 'callback':
                    return $controller->handle_authentication_callback();
                case 'attempt_attach':
                    return $controller->handle_attach_attempt();
                case 'attach_callback':
                    return $controller->handle_attach_callback();
                case 'attempt_detach':
                    return $controller->handle_detach_attempt();
                default:
                    throw new UserMessageException(t('Invalid OAuth2 action'));
            }
        } catch (Throwable $e) {
            $logger = $this->app->make('log/factory')->createLogger(Channels::CHANNEL_AUTHENTICATION);
            $logger->notice(t('OAuth Error: %s', $e->getMessage()));
            throw $e;
        }
    }
}
