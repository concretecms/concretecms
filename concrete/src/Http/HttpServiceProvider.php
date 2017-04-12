<?php
namespace Concrete\Core\Http;

use Concrete\Core\Foundation\Service\Provider as ServiceProvider;
use Concrete\Core\Http\Middleware\DelegateInterface;
use Concrete\Core\Http\Middleware\MiddlewareDelegate;
use Concrete\Core\Http\Middleware\MiddlewareStack;
use Concrete\Core\Http\Middleware\StackInterface;
use Zend\Http\Client\Adapter\Curl as CurlHttpAdapter;
use Zend\Http\Client\Adapter\Proxy as SocketHttpAdapter;

class HttpServiceProvider extends ServiceProvider
{
    public function register()
    {
        $singletons = [
            'helper/ajax' => '\Concrete\Core\Http\Service\Ajax',
            'helper/json' => '\Concrete\Core\Http\Service\Json',
        ];

        foreach ($singletons as $key => $value) {
            $this->app->singleton($key, $value);
        }

        $this->app->bind('Concrete\Core\Http\Request', function ($app) {
            return Request::getInstance();
        });

        $this->app->bind(StackInterface::class, MiddlewareStack::class);
        $this->app->bind(DelegateInterface::class, MiddlewareDelegate::class);
        $this->app->bind(DispatcherInterface::class, DefaultDispatcher::class);
        $this->app->singleton(ServerInterface::class, function ($app) {
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

        // HTTP Client
        $this->app->singleton(Client\Factory::class);

        $this->app->bind(Client\Client::class, function ($app) {
            $factory = $app->make(Client\Factory::class);

            return $factory->createFromConfig($app->make('config'));
        });
        $this->app->alias(Client\Client::class, 'http/client');

        $this->app->bind('http/client/curl', function ($app) {
            $factory = $app->make(Client\Factory::class);

            return $factory->createFromConfig($app->make('config'), CurlHttpAdapter::class);
        });

        $this->app->bind('http/client/socket', function ($app) {
            $factory = $app->make(Client\Factory::class);

            return $factory->createFromConfig($app->make('config'), SocketHttpAdapter::class);
        });
    }
}
