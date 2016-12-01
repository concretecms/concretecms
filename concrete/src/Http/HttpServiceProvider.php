<?php
namespace Concrete\Core\Http;

use Concrete\Core\Foundation\Service\Provider as ServiceProvider;
use Concrete\Core\Http\Middleware\DelegateInterface;
use Concrete\Core\Http\Middleware\MiddlewareDelegate;
use Concrete\Core\Http\Middleware\MiddlewareStack;
use Concrete\Core\Http\Middleware\StackInterface;
use Zend\Http\Client;

class HttpServiceProvider extends ServiceProvider
{
    public function register()
    {
        $singletons = array(
            'helper/ajax' => '\Concrete\Core\Http\Service\Ajax',
            'helper/json' => '\Concrete\Core\Http\Service\Json',
        );

        foreach ($singletons as $key => $value) {
            $this->app->singleton($key, $value);
        }

        $this->app->bind('Concrete\Core\Http\Request', function ($app) {
            return Request::getInstance();
        });

        $this->app->bind(StackInterface::class, MiddlewareStack::class);
        $this->app->bind(DelegateInterface::class, MiddlewareDelegate::class);
        $this->app->bind(DispatcherInterface::class, DefaultDispatcher::class);
        $this->app->singleton(ServerInterface::class, function($app) {
            $server = $app->build(DefaultServer::class);

            $config = $app['config'];
            foreach ($config->get('app.middleware') as $middleware) {
                if (is_array($middleware)) {
                    $server->addMiddleware($app->make($middleware['class']), $middleware['priority']);
                } else {
                    $server->addMiddleware($app->make($middleware));
                }
            }

            return $server;
        });

        // Response Factory
        $this->app->bind(ResponseFactoryInterface::class, ResponseFactory::class);

        $this->app->bind('curl', function (\Concrete\Core\Application\Application $app, array $arguments) {
            $config = $app->make('config');
            $options = [
                'adapter' => Curl::class,
            ];
            $proxyHost = $config->get('concrete.proxy.host');
            if ($proxyHost) {
                $options['proxyhost'] = $proxyHost;
                $proxyPort = $config->get('concrete.proxy.port');
                if ($proxyPort && is_numeric($proxyPort)) {
                    $options['proxyport'] = $proxyPort;
                }
                $proxyUser = $config->get('concrete.proxy.user');
                if (is_string($proxyUser) && $proxyUser !== '') {
                    $options['proxyuser'] = $proxyUser;
                    $options['proxypass'] = (string) $config->get('concrete.proxy.password');
                }
            }
            $options['sslverifypeer'] = (bool) $config->get('app.curl.verifyPeer');
            $connectionTimeout = $config->get('app.curl.connectionTimeout');
            if (is_numeric($connectionTimeout)) {
                $options['connectiontimeout'] = (int) $connectionTimeout;
            }
            $responseTimeout = $config->get('app.curl.responseTimeout');
            if (is_numeric($responseTimeout)) {
                $options['responsetimeout'] = (int) $responseTimeout;
            }
            $client = new Client(array_shift($arguments), $options);
            $curlAdapter = $client->getAdapter();
            $caInfo = $config->get('app.curl.caInfo');
            if (is_string($caInfo) && $caInfo !== '') {
                $curlAdapter->setCurlOption(CURLOPT_CAINFO, $caInfo);
            }
            $caPath = $config->get('app.curl.caPath');
            if (is_string($caPath) && $caPath !== '') {
                $curlAdapter->setCurlOption(CURLOPT_CAPATH, $caPath);
            }

            return $client;
        });
    }
}
