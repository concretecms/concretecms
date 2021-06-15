<?php

namespace Concrete\Core\Foundation\Queue;

use Bernard\Consumer;
use Bernard\Normalizer\EnvelopeNormalizer;
use Bernard\Normalizer\PlainMessageNormalizer;
use Bernard\Producer;
use Bernard\Router\ClassNameRouter;
use Concrete\Core\Foundation\Command\DispatcherFactory;
use Concrete\Core\Foundation\Command\SynchronousBus;
use Concrete\Core\Foundation\Queue\Driver\DriverFactory;
use Concrete\Core\Foundation\Queue\Mutex\MutexGeneratorFactory;
use Concrete\Core\Foundation\Queue\Receiver\QueueCommandMessageReceiver;
use Concrete\Core\Foundation\Queue\Serializer\SerializerManager;
use Concrete\Core\Foundation\Serializer\JsonSerializer;
use Concrete\Core\Foundation\Service\Provider;
use League\Tactician\Bernard\QueueableCommand;
use League\Tactician\Bernard\QueueCommandNormalizer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\CustomNormalizer;
use Symfony\Component\Serializer\Normalizer\JsonSerializableNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class QueueServiceProvider extends Provider
{

    public function register()
    {
        $this->app->singleton('queue/driver', function($app) {
            return $this->app->make(DriverFactory::class)
                ->createDriver();
        });

        $this->app->singleton(MutexGeneratorFactory::class);

        $this->app->singleton(SerializerManager::class, function($app) {
            $manager = new SerializerManager();
            $manager->addNormalizer(new EnvelopeNormalizer());
            $manager->addNormalizer(new QueueCommandNormalizer());
            $manager->addNormalizer(new ObjectNormalizer());
            $manager->addNormalizer(new PlainMessageNormalizer());
            return $manager;
        });

        $this->app->singleton('queue/consumer', function($app) {
            $dispatcher = $app->make(DispatcherFactory::class)->getDispatcher();
            $receiver = new QueueCommandMessageReceiver($dispatcher);
            $router = new ClassNameRouter();
            $router->add(QueueableCommand::class, $receiver);
            return new Consumer($router, $app->make('director'));
        });

        $this->app->singleton('queue/serializer', function($app) {
            $manager = $app->make(SerializerManager::class);
            return $manager->getSerializer();
        });

        $this->app->singleton('queue/producer', function($app) {
            $driver = $app->make('queue/driver');
            $factory = new QueueFactory($driver, $app->make('queue/serializer'));
            $producer = new Producer($factory, $this->app->make('director'));
            return $producer;
        });

        $this->app->singleton('queue', function($app) {
            return $app->make(QueueService::class);
        });

        $this->app->singleton(JsonSerializer::class, function($app) {
            $serializer = new JsonSerializer([
                new JsonSerializableNormalizer(),
                new CustomNormalizer()
            ], [
                new JsonEncoder()
            ]);
            return $serializer;
        });
    }
}
