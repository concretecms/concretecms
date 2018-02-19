<?php

namespace Concrete\Core\Foundation\Queue;

use Bernard\Normalizer\EnvelopeNormalizer;
use Bernard\Normalizer\PlainMessageNormalizer;
use Bernard\Producer;
use Bernard\QueueFactory\PersistentFactory;
use Concrete\Core\Foundation\Queue\Driver\DriverFactory;
use Concrete\Core\Foundation\Service\Provider;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Concrete\Core\Foundation\Queue\Serializer\SerializerManager;

class QueueServiceProvider extends Provider
{

    public function register()
    {
        $this->app->singleton('queue/driver', function($app) {
            return $this->app->make(DriverFactory::class)
                ->createDriver();
        });

        $this->app->singleton(SerializerManager::class, function($app) {
            $manager = new SerializerManager();
            $manager->addNormalizer(new EnvelopeNormalizer());
            $manager->addNormalizer(new PlainMessageNormalizer());
            $manager->addNormalizer(new GetSetMethodNormalizer());
            return $manager;
        });

        $this->app->singleton('queue/serializer', function($app) {
            $manager = $app->make(SerializerManager::class);
            return $manager->getSerializer();
        });

        $this->app->singleton('queue/producer', function($app) {
            $driver = $app->make('queue/driver');
            $factory = new PersistentFactory($driver, $app->make('queue/serializer'));
            $producer = new Producer($factory, $this->app->make('director'));
            return $producer;
        });

    }
}
