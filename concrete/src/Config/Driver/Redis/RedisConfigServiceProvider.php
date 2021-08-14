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

    /** @var callable A function that takes no arguments and returns an instance of \Redis */
    protected $redisFactory;

    public function __construct(Application $app, callable $redisFactory)
    {
        parent::__construct($app);
        $this->redisFactory = $redisFactory;
    }

    /**
     * Register Redis as a config saver and loader.
     * It's best to use a distinct redis connection rather than reusing one
     *
     * @param Application $app
     * @param callable $redisFactory
     */
    public function setup(Application $app, callable $redisFactory)
    {
        (new RedisConfigServiceProvider($app, $redisFactory))->register();
    }

    public function register()
    {
        $app = $this->app;

        if ($app->bound('config')) {
            throw new \RuntimeException('RedisConfigServiceProvider must be registered before the core ConfigServiceProvider.');
        }

        $redis = null;
        // Create a callable that manages memoizing the redis instance and configuring it
        $memoizedRedis = function() use (&$redis) {
            if (!$redis) {
                $factory = $this->redisFactory;
                $redis = $factory();

                if (!$redis || !$redis instanceof Redis) {
                    throw new \RuntimeException('Cannot use Redis for config. Invalid Redis instance given.');
                }

                if (!$redis->isConnected()) {
                    throw new \RuntimeException('Cannot use Redis for config. The given Redis instance is not connected.');
                }

                // Set some simple configuration
                $redis->setOption($redis::OPT_PREFIX, 'cfg=');
                $redis->setOption($redis::OPT_SCAN, $redis::SCAN_RETRY);
            }

            return $redis;
        };

        // Provide the memoized redis instance to both loader and saver
        $app->when(RedisLoader::class)->needs(Redis::class)->give($memoizedRedis);
        $app->when(RedisSaver::class)->needs(Redis::class)->give($memoizedRedis);

        // Bind a composite loader that includes redis
        $app->bind(LoaderInterface::class, static function($app) {
            return $app->make(CompositeLoader::class, ['app' => $app, 'loaders' => [
                CoreFileLoader::class,
                RedisLoader::class,
                FileLoader::class,
            ]]);
        });

        // Bind a redis saver
        $app->bind(SaverInterface::class, RedisSaver::class);
    }

}
