<?php
namespace Concrete\Core\Http;

use Concrete\Core\Foundation\Service\Provider as ServiceProvider;

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

        $this->app->bind(DispatcherInterface::class, DefaultDispatcher::class);
        $this->app->singleton(ServerInterface::class, function($app) {
            $server = $app->build(DefaultServer::class);

            $config = $this->app['config'];
            foreach ($config->get('app.middleware') as $middleware) {
                if (is_array($middleware)) {
                    $server->addMiddleware($app->make($middleware['class']), $middleware['priority']);
                } else {
                    $server->addMiddleware($app->make($middleware));
                }
            }

            return $server;
        });
    }

}
