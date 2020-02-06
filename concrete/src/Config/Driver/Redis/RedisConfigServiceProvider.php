<?php

namespace Concrete\Core\Config\Driver\Redis;

use Concrete\Core\Application\Application;
use Concrete\Core\Config\CompositeLoader;
use Concrete\Core\Config\CoreFileLoader;
use Concrete\Core\Config\FileLoader;
use Concrete\Core\Config\LoaderInterface;
use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Config\SaverInterface;
use Concrete\Core\Foundation\Service\Provider;
use Redis;

/**
 * Redis config service provider.
 * Use `RedisConfigServiceProvider::setup()` before the core config service provider in order to use redis config
 */
class RedisConfigServiceProvider extends Provider
{

    protected function __construct(Application $app)
    {
        parent::__construct($app);
    }

    /**
     * Register Redis as a config saver and loader.
     *
     * @param Application $app
     * @param callable $redisFactory
     */
    public static function setup(Application $app, callable $redisFactory)
    {
        if ($app->bound('config')) {
            throw new \RuntimeException('RedisConfigServiceProvider must be registered before the core ConfigServiceProvider.');
        }

        $redis = null;
        $memoizedRedis = function() use (&$redis, $redisFactory) {
            if (!$redis) {
                $redis = $redisFactory();
            }

            if (!$redis || !$redis instanceof Redis) {
                throw new \RuntimeException('Cannot use Redis for config. Invalid Redis instance given.');
            }

            if (!$redis->isConnected()) {
                throw new \RuntimeException('Cannot use Redis for config. The given Redis instance is not connected.');
            }

            return $redis;
        };

        // Provide the memoized redis instance to both loader and saver
        $app->when(RedisLoader::class)->needs(Redis::class)->give($memoizedRedis);
        $app->when(RedisSaver::class)->needs(Redis::class)->give($memoizedRedis);

        (new RedisConfigServiceProvider($app))->register();
    }

    public function register()
    {
        $app = $this->app;

        // Bind a composite loader that includes redis
        $app->bind(LoaderInterface::class, static function($app) {
            return $app->make(CompositeLoader::class, [$app, [
                CoreFileLoader::class,
                RedisLoader::class,
                FileLoader::class,
            ]]);
        });

        // Bind a redis saver
        $app->bind(SaverInterface::class, RedisSaver::class);
    }

}
