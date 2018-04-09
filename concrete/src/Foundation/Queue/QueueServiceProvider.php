<?php

namespace Concrete\Core\Foundation\Queue;

use Bernard\BernardEvents;
use Bernard\Consumer;
use Bernard\Event\RejectEnvelopeEvent;
use Bernard\Normalizer\EnvelopeNormalizer;
use Bernard\Normalizer\PlainMessageNormalizer;
use Bernard\Producer;
use Bernard\QueueFactory\PersistentFactory;
use Bernard\Router\ClassNameRouter;
use Concrete\Core\Foundation\Queue\Driver\DriverFactory;
use Concrete\Core\Foundation\Queue\Mutex\MutexGeneratorFactory;
use Concrete\Core\Foundation\Queue\Mutex\QueueMutexGenerator;
use Concrete\Core\Foundation\Service\Provider;
use League\Tactician\Bernard\QueueableCommand;
use League\Tactician\Bernard\Receiver\SeparateBusReceiver;
use Psr\Log\LoggerInterface;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Concrete\Core\Foundation\Queue\Serializer\SerializerManager;
use Concrete\Core\Events\EventDispatcher;

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
            $manager->addNormalizer(new PlainMessageNormalizer());
            $manager->addNormalizer(new GetSetMethodNormalizer());
            return $manager;
        });

        $this->app->singleton('queue/router', function($app) {
            $bus = $app->make('command/bus');
            $receiver = new SeparateBusReceiver($bus->getSyncBus());
            $router = new ClassNameRouter();
            $router->add(QueueableCommand::class, $receiver);
            return $router;
        });

        $this->app->singleton('queue/consumer', function($app) {
            $router = $app->make('queue/router');
            return new Consumer($router, $app->make('director'));
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

        $this->app->singleton('queue', function($app) {
            return $app->make(QueueService::class);
        });

        $subscriber = $this->app->make(BernardSubscriber::class);
        $dispatcher = $this->app->make('director');
        $dispatcher->addSubscriber($subscriber);
    }
}
