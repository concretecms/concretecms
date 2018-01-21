<?php

namespace Concrete\Core\Foundation\Queue;

use Bernard\Command\ProduceCommand;
use Bernard\Normalizer\EnvelopeNormalizer;
use Bernard\Normalizer\PlainMessageNormalizer;
use Concrete\Core\Console\Application;
use Concrete\Core\Foundation\Queue\Driver\ConcreteDatabaseDriver;
use Bernard\Producer;
use Bernard\QueueFactory\PersistentFactory;
use Bernard\Serializer;
use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Foundation\Service\Provider;
use Bernard\Driver\DoctrineDriver;
use Normalt\Normalizer\AggregateNormalizer;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Normalizer\PropertyNormalizer;
use Concrete\Core\Foundation\Queue\Driver\DriverFactory;

class QueueServiceProvider extends Provider
{

    public function register()
    {
        $this->app->singleton('queue/driver', function($app) {
            return $this->app->make(DriverFactory::class)
                ->createDriver();
        });

        $this->app->singleton('queue/serializer', function($app) {
            $aggregateNormalizer = new AggregateNormalizer([
                new EnvelopeNormalizer(),
                new PropertyNormalizer(),
                new PlainMessageNormalizer(),
            ]);
            $serializer = new Serializer($aggregateNormalizer);
            return $serializer;
        });

        $this->app->singleton('queue/producer', function($app) {
            $driver = $app->make('queue/driver');
            $factory = new PersistentFactory($driver, $app->make('queue/serializer'));
            $producer = new Producer($factory, $this->app->make('director'));
            return $producer;
        });
    }
}
