<?php

namespace Concrete\Core\Cache;

use Concrete\Core\Application\Application;
use Concrete\Core\Cache\Command\ClearCacheCommandHandler;
use Concrete\Core\Cache\Level\CacheLevel;
use Concrete\Core\Cache\Page\PageCache;
use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Foundation\Service\Provider as ServiceProvider;
use Concrete\Core\Logging\Channels;
use Concrete\Core\Logging\LoggerAwareInterface;
use Concrete\Core\Logging\LoggerAwareTrait;
use Concrete\Core\Logging\LoggerFactory;
use Illuminate\Filesystem\FilesystemAdapter;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\Cache\Adapter\ChainAdapter;

class CacheServiceProvider extends ServiceProvider implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    public function register()
    {
        /**
         * @var array<string, CacheLevel> $caches
         */
        $caches = [
            'cache' => CacheLevel::Object,
            'cache/request' => CacheLevel::Request,
            'cache/expensive' => CacheLevel::Expensive,
            'cache/overrides' => CacheLevel::Overrides,
        ];
        foreach ($caches as $alias => $enum) {
            $class = $enum->getCacheClass();
            $this->app->when($class)
                ->needs(CacheItemPoolInterface::class)
                ->give(function () use ($enum) {
                    return $this->getCachePool($enum);
                });

            $this->app->singleton($class);
            $this->app->alias($class, $alias);
        }
        $this->app->singleton('cache/page', function () {
            return PageCache::getLibrary();
        });
        $this->app
            ->when(ClearCacheCommandHandler::class)
            ->needs(LoggerInterface::class)
            ->give(function (Application $app) {
                $factory = $app->make(LoggerFactory::class);
                return $factory->createLogger(Channels::CHANNEL_OPERATIONS);
            });
    }

    private function getCachePool(CacheLevel $level): CacheItemPoolInterface
    {
        $config = $this->app->make(Repository::class);


        $enabledKey = $level->getEnabledConfigKey();

        if ($enabledKey && !$config->get($enabledKey)) {
            return new ArrayAdapter();
        }

        $optionsKey = $level->getOptionsConfigKey();
        $options = $optionsKey ? $config->get($optionsKey) : [];

        $pool = $options['pool'] ?? [];
        if (is_string($pool)) {
            $pool = $options['pools'][$pool] ?? [];
        }

        return $this->poolFromConfig($pool);
    }

    private function poolFromConfig(array $pool): CacheItemPoolInterface
    {
        $class = $pool['class'] ?? null;
        if ($class instanceof CacheItemPoolInterface) {
            return $class;
        }

        $args = $pool['options'] ?? [];
        if (!$class) {
            return new ArrayAdapter();
        }

        if ($class === ChainAdapter::class && is_array($args['adapters'])) {
            $args['adapters'] = array_map($this->poolFromConfig(...), $args['adapters']);
        }

        return $this->app->make($class, $args);
    }

    public function getLoggerChannel()
    {
        return Channels::CHANNEL_APPLICATION;
    }
}
