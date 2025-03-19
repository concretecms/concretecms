<?php

namespace Concrete\Core\Filesystem;

use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Foundation\Service\Provider;
use Stash\Interfaces\PoolInterface;
use Twig\Cache\FilesystemCache;

class TemplateServiceProvider extends Provider
{
    public function register()
    {
        $this->app->when(TemplateService::class)->needs(PoolInterface::class)->give(function () {
            return $this->app->make('cache/overrides')->pool;
        });

        $this->app->singleton(TemplateService::class);

        $this->app->singleton(TwigFactory::class, function ($app) {
            $config = $app->make(Repository::class)->get('app.twig', []) ?: [];

            $factory = new TwigFactory(
                new FilesystemCache($config['cache_dir'] ?? DIR_FILES_UPLOADED_STANDARD . '/cache/twig'),
                (bool) ($config['debug'] ?? false)
            );

            foreach ($config['extensions'] ?? [] as $extension) {
                $factory->addExtension($this->app->make($extension));
            }

            return $factory;
        });
    }
}